<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Mission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; 
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;

class DepartmentHeadController extends Controller
{
    /**
     * Display the dashboard.
     */
   
public function dashboard()
{
    $user = Auth::user();
    
    // Ensure user is a department head
    if (!$user->isChefDepartement()) {
        return redirect()->route('home');
    }
    
    // Check if department is set
    if (!$user->department) {
        return redirect()->route('chef.settings')
            ->with('error', 'Veuillez configurer votre département dans vos paramètres avant de continuer.');
    }
    
    // Get pending missions for validation
    $pendingMissions = $user->departmentMissions()
                            ->where('status', 'soumise')
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();
    
    // Get recent validated missions
    $recentValidatedMissions = $user->departmentMissions()
                               ->where('status', 'validee_chef')
                               ->orderBy('updated_at', 'desc')
                               ->take(5)
                               ->get();
    
    // Get mission counts by status
    $missionStats = [
        'pending' => $user->departmentMissions()->where('status', 'soumise')->count(),
        'validated' => $user->departmentMissions()->where('status', 'validee_chef')->count(),
        'rejected' => $user->departmentMissions()->where('status', 'rejetee')->count(),
        'completed' => $user->departmentMissions()->where('status', 'terminee')->count(),
    ];
    
    // Get total missions count for this year
    $currentYear = date('Y');
    $missionsThisYear = $user->departmentMissions()
        ->whereYear('created_at', $currentYear)
        ->count();
    
    // Get missions by month for current year for chart
    $missionsByMonth = $user->departmentMissions()
        ->whereYear('created_at', $currentYear)
        ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
        ->groupBy('month')
        ->pluck('count', 'month')
        ->toArray();
    
    // Format data for monthly chart
    $monthLabels = [];
    $monthData = [];
    
    for ($m = 1; $m <= 12; $m++) {
        $monthLabels[] = Carbon::create(null, $m, 1)->format('M');
        $monthData[] = $missionsByMonth[$m] ?? 0;
    }
    
    // Get department teachers count
    $teachersCount = User::where('department', $user->department)
                        ->where('role', 'enseignant')
                        ->count();
    
    // Get recently active teachers
    $recentlyActiveTeachers = User::where('department', $user->department)
        ->where('role', 'enseignant')
        ->whereHas('missions', function($query) {
            $query->whereDate('created_at', '>=', Carbon::now()->subDays(30));
        })
        ->withCount(['missions' => function($query) {
            $query->whereDate('created_at', '>=', Carbon::now()->subDays(30));
        }])
        ->orderByDesc('missions_count')
        ->take(5)
        ->get();
    
    // Get mission type distribution
    $missionTypes = [
        'nationale' => $user->departmentMissions()->where('type', 'nationale')->count(),
        'internationale' => $user->departmentMissions()->where('type', 'internationale')->count(),
    ];

    // Get notifications
    $notifications = $user->notifications()
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();
    
    return view('chef.dashboard', compact(
        'user',
        'pendingMissions',
        'recentValidatedMissions',
        'missionStats',
        'teachersCount',
        'missionsThisYear',
        'monthLabels',
        'monthData',
        'recentlyActiveTeachers',
        'missionTypes',
        'notifications'
    ));
}
    /**
     * Display a listing of missions to validate.
     */
   
public function missionValidationList()
{
    $user = Auth::user();
    
    // Check if department is set
    if (!$user->department) {
        // Return view with empty missions
        return view('chef.mission_validate', [
            'user' => $user,
            'pendingMissions' => collect([]),
        ]);
    }
    
    // Get pending missions for validation
    $pendingMissions = $user->departmentMissions()
                           ->where('status', 'soumise')
                           ->orderBy('created_at', 'desc')
                           ->paginate(10);
    
    return view('chef.mission_validate', compact('user', 'pendingMissions'));
}

    /**
     * Display mission details for validation.
     */
    public function missionDetails(Mission $mission)
    {
        $user = Auth::user();
        
        // Ensure user is authorized to view this mission
        if ($mission->user->department !== $user->department) {
            return redirect()->route('chef.mission_validate')
                ->with('error', 'Vous n\'êtes pas autorisé à voir cette mission.');
        }
        
        return view('chef.mission_details', compact('mission', 'user'));
    }

    /**
     * Validate a mission.
     */
    public function validateMission(Request $request, Mission $mission)
    {
        $user = Auth::user();
        
        // Validate the request
        $request->validate([
            'decision' => 'required|in:approve,reject',
            'rejection_reason' => 'required_if:decision,reject',
            'comments' => 'nullable|string',
        ]);
        
        // Ensure user is authorized to validate this mission
        if ($mission->user->department !== $user->department) {
            return redirect()->route('chef.mission_validate')
                ->with('error', 'Vous n\'êtes pas autorisé à valider cette mission.');
        }
        
        // Ensure mission is in a state that can be validated
        if ($mission->status !== 'soumise') {
            return redirect()->route('chef.mission_validate')
                ->with('error', 'Cette mission ne peut pas être validée dans son état actuel.');
        }
        
        // Process the validation
        if ($request->decision === 'approve') {
            $mission->status = 'validee_chef';
            $mission->chef_approval_date = Carbon::now();
            $mission->chef_comments = $request->comments;
            
            $message = 'La mission a été validée avec succès et sera transmise au directeur.';
        } else {
            $mission->status = 'rejetee';
            $mission->rejection_reason = $request->rejection_reason;
            $mission->chef_comments = $request->comments;
            
            $message = 'La mission a été rejetée. L\'enseignant sera notifié.';
        }
        
        $mission->save();
        
        // Notify relevant parties (would be implemented with Laravel Notifications)
        // Code would go here...
        if ($request->decision === 'approve') {
            app(NotificationService::class)->notifyMissionValidatedByHead($mission);
        } else {
            app(NotificationService::class)->notifyMissionRejected($mission, 'chef');
        }
        return redirect()->route('chef.mission_validate')
            ->with('success', $message);
    }

    /**
     * List all department missions.
     */
    
public function departmentMissions(Request $request)
{
    $user = Auth::user();
    
    // Check if department is set
    if (!$user->department) {
        // Return view with empty missions
        return view('chef.mission_validate', [
            'user' => $user,
            'pendingMissions' => collect([]),
        ]);
    }
    
    $query = $user->departmentMissions();
    
    // Apply filters if present
    if ($request->has('status') && $request->status !== 'all') {
        $query->where('status', $request->status);
    }
    
    // Apply search if present
    if ($request->has('search') && $request->search) {
        $search = '%' . $request->search . '%';
        $query->where(function($q) use ($search) {
            $q->where('title', 'like', $search)
              ->orWhere('destination_city', 'like', $search)
              ->orWhereHas('user', function($userQuery) use ($search) {
                  $userQuery->where('name', 'like', $search);
              });
        });
    }
    
    // Apply date filter if present
    if ($request->has('date_start') && $request->date_start) {
        $query->where('start_date', '>=', $request->date_start);
    }
    
    if ($request->has('date_end') && $request->date_end) {
        $query->where('end_date', '<=', $request->date_end);
    }
    
    // Get paginated results
    $pendingMissions = $query->orderBy('created_at', 'desc')->paginate(3)->withQueryString();
    
    return view('chef.mission_validate', compact('user', 'pendingMissions'));
}
    /**
     * Display department statistics.
     */
  
public function departmentStats()
{
    $user = Auth::user();
    
    // Check if department is set
    if (!$user->department) {
        return redirect()->route('chef.settings')
            ->with('error', 'Veuillez configurer votre département dans vos paramètres avant de continuer.');
    }
    
    // Get mission counts by status
    $statusStats = [
        'soumise' => $user->departmentMissions()->where('status', 'soumise')->count(),
        'validee_chef' => $user->departmentMissions()->where('status', 'validee_chef')->count(),
        'validee_directeur' => $user->departmentMissions()->where('status', 'validee_directeur')->count(),
        'billet_reserve' => $user->departmentMissions()->where('status', 'billet_reserve')->count(),
        'terminee' => $user->departmentMissions()->where('status', 'terminee')->count(),
        'rejetee' => $user->departmentMissions()->where('status', 'rejetee')->count(),
    ];
    
    // Get mission counts by type
    $typeStats = [
        'nationale' => $user->departmentMissions()->where('type', 'nationale')->count(),
        'internationale' => $user->departmentMissions()->where('type', 'internationale')->count(),
    ];
    
    // Get missions by month for current year
    $currentYear = date('Y');
    $missionsThisYear = $user->departmentMissions()
        ->whereYear('created_at', $currentYear)
        ->count();
    
    $missionsByMonth = $user->departmentMissions()
        ->whereYear('created_at', $currentYear)
        ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
        ->groupBy('month')
        ->pluck('count', 'month')
        ->toArray();
    
    // Format data for charts
    $monthLabels = [];
    $monthData = [];
    
    for ($m = 1; $m <= 12; $m++) {
        $monthLabels[] = Carbon::create(null, $m, 1)->format('M');
        $monthData[] = $missionsByMonth[$m] ?? 0;
    }
    
    // Get teachers with most missions
    $teachersWithMostMissions = User::where('department', $user->department)
        ->where('role', 'enseignant')
        ->withCount(['missions' => function($query) use ($currentYear) {
            $query->whereYear('created_at', $currentYear);
        }])
        ->orderByDesc('missions_count')
        ->limit(5)
        ->get();
    
    // Get total missions by year (last 5 years)
    $missionsByYear = [];
    for ($y = $currentYear - 4; $y <= $currentYear; $y++) {
        $missionsByYear[$y] = $user->departmentMissions()
            ->whereYear('created_at', $y)
            ->count();
    }
    
    // Get missions completion rate
    $totalMissions = array_sum($statusStats);
    $completionRate = $totalMissions > 0 
        ? round(($statusStats['terminee'] / $totalMissions) * 100) 
        : 0;
    
    // Get average mission duration
    $averageDuration = $user->departmentMissions()
        ->selectRaw('AVG(DATEDIFF(end_date, start_date) + 1) as avg_duration')
        ->first()
        ->avg_duration ?? 0;
    
    // Get rejection rate
    $rejectionRate = $totalMissions > 0 
        ? round(($statusStats['rejetee'] / $totalMissions) * 100) 
        : 0;
    
    // Get most popular destinations
    $popularDestinations = $user->departmentMissions()
        ->selectRaw('destination_city, COUNT(*) as count')
        ->groupBy('destination_city')
        ->orderByDesc('count')
        ->limit(5)
        ->get();
    
    // Get department budget info
    $departmentSettings = \App\Models\DepartmentSetting::where('department', $user->department)->first();
    $departmentBudget = $departmentSettings ? $departmentSettings->budget : 0;
    
    return view('chef.department_stats', compact(
        'user',
        'statusStats',
        'typeStats',
        'monthLabels',
        'monthData',
        'teachersWithMostMissions',
        'missionsByYear',
        'missionsThisYear',
        'completionRate',
        'averageDuration',
        'rejectionRate',
        'popularDestinations',
        'departmentBudget'
    ));
}
    
public function settings()
{
    $user = Auth::user();
    return view('chef.settings', compact('user'));
}

public function updateProfile(Request $request)
{
    $user = Auth::user();
    
    $request->validate([
        'firstName' => 'required|string|max:255',
        'lastName' => 'required|string|max:255',
        'email' => [
            'required',
            'string',
            'email',
            'max:255',
            Rule::unique('users')->ignore($user->id),
        ],
        'department' => 'required|string|max:100',
        // Autres validations...
    ]);
    
    // Mise à jour des informations...
    $user->name = $request->firstName . ' ' . $request->lastName;
    $user->email = $request->email;
    $user->department = $request->department;
    // Autres mises à jour...
    
    $user->save();
    
    return redirect()->route('chef.settings')
        ->with('success', 'Profil mis à jour avec succès.');
}
}
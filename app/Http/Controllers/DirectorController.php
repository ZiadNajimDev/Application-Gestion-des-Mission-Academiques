<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Mission;
use Illuminate\Http\Request;
use App\Models\DepartmentSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;

class DirectorController extends Controller
{
    /**
     * Display the director dashboard.
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Ensure user is a director
        if ($user->role !== 'directeur') {
            return redirect()->route('home');
        }
        
        // Get mission counts by status
        $missionStats = [
            'pending' => Mission::where('status', 'validee_chef')->count(),
            'approved' => Mission::where('status', 'validee_directeur')->count(),
            'rejected' => Mission::where('status', 'rejetee')->count(),
            'completed' => Mission::where('status', 'terminee')->count(),
        ];
        
        // Get department count from department_settings table
        $departmentCount = DepartmentSetting::count();
        
        // Get teacher count
        $teacherCount = User::where('role', 'enseignant')->count();
        
        // Get pending missions
        $pendingMissions = Mission::where('status', 'validee_chef')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Get missions by department
        $missionsByDepartment = Mission::join('users', 'missions.user_id', '=', 'users.id')
            ->where('users.role', 'enseignant')
            ->select('users.department', DB::raw('count(*) as total'))
            ->groupBy('users.department')
            ->pluck('total', 'department')
            ->toArray();

        // Get notifications
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Get recently approved missions
        $recentlyApprovedMissions = Mission::where('status', 'validee_directeur')
                                        ->orderBy('director_approval_date', 'desc')
                                        ->with('user')
                                        ->take(5)
                                        ->get();
        
        // Get mission counts by department
        $missionsByDepartment = User::join('missions', 'users.id', '=', 'missions.user_id')
                                    ->select('department', DB::raw('count(*) as count'))
                                    ->whereNotNull('department')
                                    ->groupBy('department')
                                    ->pluck('count', 'department')
                                    ->toArray();
        
        // Get missions counts by month for current year
        $currentYear = date('Y');
        $missionsByMonth = Mission::whereYear('created_at', $currentYear)
                                ->select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as count'))
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
        
        // Get total budget for all departments
        $totalBudget = DepartmentSetting::sum('budget');
        
        // Get mission type distribution
        $missionTypes = [
            'nationale' => Mission::where('type', 'nationale')->count(),
            'internationale' => Mission::where('type', 'internationale')->count(),
        ];
        
        // Get popular destinations
        $popularDestinations = Mission::select('destination_city', DB::raw('count(*) as count'))
                                  ->groupBy('destination_city')
                                  ->orderByDesc('count')
                                  ->limit(5)
                                  ->get();
        
        return view('director.dashboard', compact(
            'user',
            'missionStats',
            'departmentCount',
            'teacherCount',
            'pendingMissions',
            'missionsByDepartment',
            'notifications',
            'recentlyApprovedMissions',
            'totalBudget',
            'monthLabels',
            'monthData',
            'missionTypes',
            'popularDestinations'
        ));
    }

    /**
     * Display missions awaiting approval.
     */
    
public function pendingMissions(Request $request)
{
    $user = Auth::user();
    
    $query = Mission::where('status', 'validee_chef')
                   ->with(['user' => function($query) {
                       $query->select('id', 'name', 'email', 'department', 'profile_photo_path');
                   }]);
    
    // Apply department filter if present
    if ($request->has('department') && $request->department !== 'all') {
        $query->whereHas('user', function($q) use ($request) {
            $q->where('department', $request->department);
        });
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
    if ($request->has('date_from') && $request->date_from) {
        $query->where('start_date', '>=', $request->date_from);
    }
    
    if ($request->has('date_to') && $request->date_to) {
        $query->where('end_date', '<=', $request->date_to);
    }
    
    // Get the missions with pagination
    $pendingMissions = $query->orderBy('chef_approval_date', 'desc')
                           ->paginate(10)
                           ->withQueryString();
    
    // Get mission stats
    $missionStats = [
        'pending' => Mission::where('status', 'validee_chef')->count(),
        'approved' => Mission::where('status', 'validee_directeur')->count(),
        'rejected' => Mission::where('status', 'rejetee')->whereNotNull('director_approval_date')->count()
    ];
    
    // Get departments from both users and department_settings tables
    $departments = \App\Models\DepartmentSetting::select('department')
                     ->orderBy('department')
                     ->pluck('department')
                     ->toArray();
    
    return view('director.pending_missions', compact(
        'user',
        'pendingMissions',
        'missionStats',
        'departments'
    ));
}

    /**
     * Show mission details.
     */
    public function missionDetails(Mission $mission)
    {
        $user = Auth::user();
        
        // Load mission with related user
        $mission->load('user');
        
        return view('director.mission_details', compact('user', 'mission'));
    }

    /**
     * Process mission approval or rejection.
     */
    public function processMission(Request $request, Mission $mission)
    {
        $request->validate([
            'decision' => 'required|in:approve,reject',
            'rejection_reason' => 'required_if:decision,reject',
            'comments' => 'nullable|string',
        ]);
        
        // Check if mission can be processed
        if ($mission->status !== 'validee_chef') {
            return redirect()->route('director.pending_missions')
                ->with('error', 'Cette mission ne peut pas être traitée dans son état actuel.');
        }
        
        // Process the mission
        if ($request->decision === 'approve') {
            $mission->status = 'validee_directeur';
            $mission->director_approval_date = Carbon::now();
            $mission->director_comments = $request->comments;
            
            $message = 'La mission a été approuvée avec succès.';
        } else {
            $mission->status = 'rejetee';
            $mission->rejection_reason = $request->rejection_reason;
            $mission->director_comments = $request->comments;
            $mission->director_approval_date = Carbon::now();
            
            $message = 'La mission a été rejetée.';
        }
        
        $mission->save();
        
        // Notify relevant parties (would be implemented with Laravel Notifications)
        // Code would go here...
        if ($request->decision === 'approve') {
            app(NotificationService::class)->notifyMissionValidatedByDirector($mission);
        } else {
            app(NotificationService::class)->notifyMissionRejected($mission, 'directeur');
        }
        return redirect()->route('director.pending_missions')
            ->with('success', $message);
    }

    /**
     * Display all missions.
     */
    
public function allMissions(Request $request)
{
    $user = Auth::user();
    
    $query = Mission::query()->with(['user' => function($query) {
        $query->select('id', 'name', 'email', 'department', 'profile_photo_path');
    }]);
    
    // Apply filters if present
    if ($request->has('status') && $request->status !== 'all') {
        $query->where('status', $request->status);
    }
    
    if ($request->has('department') && $request->department !== 'all') {
        $query->whereHas('user', function($q) use ($request) {
            $q->where('department', $request->department);
        });
    }
    
    if ($request->has('type') && $request->type !== 'all') {
        $query->where('type', $request->type);
    }
    
    // Apply date filters
    if ($request->has('date_from') && $request->date_from) {
        $query->where('start_date', '>=', $request->date_from);
    }
    
    if ($request->has('date_to') && $request->date_to) {
        $query->where('end_date', '<=', $request->date_to);
    }
    
    if ($request->has('created_from') && $request->created_from) {
        $query->whereDate('created_at', '>=', $request->created_from);
    }
    
    if ($request->has('created_to') && $request->created_to) {
        $query->whereDate('created_at', '<=', $request->created_to);
    }
    
    // Apply search if present
    if ($request->has('search') && $request->search) {
        $search = '%' . $request->search . '%';
        $query->where(function($q) use ($search) {
            $q->where('title', 'like', $search)
                ->orWhere('destination_city', 'like', $search)
                ->orWhere('destination_institution', 'like', $search)
                ->orWhereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'like', $search)
                        ->orWhere('email', 'like', $search);
                });
        });
    }
    
    // Apply sorting
    $sortBy = $request->input('sort_by', 'created_at');
    $sortOrder = $request->input('sort_order', 'desc');
    
    $allowedSortFields = [
        'created_at', 'start_date', 'title', 'destination_city', 'status'
    ];
    
    if (!in_array($sortBy, $allowedSortFields)) {
        $sortBy = 'created_at';
    }
    
    $query->orderBy($sortBy, $sortOrder);
    
    // Get paginated results
    $missions = $query->paginate(15)->withQueryString();
    
    // Get necessary data for filters
    $departments = User::whereNotNull('department')
                     ->select('department')
                     ->distinct()
                     ->pluck('department');
    
    $statusOptions = [
        'soumise' => 'En attente de validation (chef)',
        'validee_chef' => 'Validée par chef de département',
        'validee_directeur' => 'Validée par directeur',
        'billet_reserve' => 'Billet réservé',
        'terminee' => 'Terminée',
        'rejetee' => 'Rejetée'
    ];
    
    // Get mission stats
    $statusStats = [
        'soumise' => Mission::where('status', 'soumise')->count(),
        'validee_chef' => Mission::where('status', 'validee_chef')->count(),
        'validee_directeur' => Mission::where('status', 'validee_directeur')->count(),
        'billet_reserve' => Mission::where('status', 'billet_reserve')->count(),
        'terminee' => Mission::where('status', 'terminee')->count(),
        'rejetee' => Mission::where('status', 'rejetee')->count(),
    ];
    
    return view('director.all_missions', compact(
        'user',
        'missions',
        'departments',
        'statusOptions',
        'statusStats',
        'sortBy',
        'sortOrder'
    ));
}

    /**
     * Display statistics.
     */
    public function statistics()
    {
        $user = Auth::user();
        
        // Get mission counts by status
        $statusStats = [
            'soumise' => Mission::where('status', 'soumise')->count(),
            'validee_chef' => Mission::where('status', 'validee_chef')->count(),
            'validee_directeur' => Mission::where('status', 'validee_directeur')->count(),
            'billet_reserve' => Mission::where('status', 'billet_reserve')->count(),
            'terminee' => Mission::where('status', 'terminee')->count(),
            'rejetee' => Mission::where('status', 'rejetee')->count(),
        ];
        
        // Get mission counts by type
        $typeStats = [
            'nationale' => Mission::where('type', 'nationale')->count(),
            'internationale' => Mission::where('type', 'internationale')->count(),
        ];
        
        // Get missions by department
        $missionsByDepartment = User::join('missions', 'users.id', '=', 'missions.user_id')
                                    ->select('department', DB::raw('count(*) as count'))
                                    ->whereNotNull('department')
                                    ->groupBy('department')
                                    ->get();
        
        // Get missions by month for current year
        $currentYear = date('Y');
        $missionsByMonth = Mission::whereYear('created_at', $currentYear)
                                ->select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as count'))
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
        
        // Get missions by year (last 5 years)
        $yearlyStats = [];
        for ($y = $currentYear - 4; $y <= $currentYear; $y++) {
            $yearlyStats[$y] = Mission::whereYear('created_at', $y)->count();
        }
        
        // Get teachers with most missions
        $topTeachers = User::where('role', 'enseignant')
                          ->withCount(['missions' => function($query) use ($currentYear) {
                              $query->whereYear('created_at', $currentYear);
                          }])
                          ->orderByDesc('missions_count')
                          ->limit(10)
                          ->get();
        
        // Get popular destinations
        $popularDestinations = Mission::select('destination_city', DB::raw('count(*) as count'))
                                  ->groupBy('destination_city')
                                  ->orderByDesc('count')
                                  ->limit(10)
                                  ->get();
        
        return view('director.statistics', compact(
            'user',
            'statusStats',
            'typeStats',
            'missionsByDepartment',
            'monthLabels',
            'monthData',
            'yearlyStats',
            'topTeachers',
            'popularDestinations'
        ));
    }

    /**
     * Display departments and budgets.
     */
   

     
public function departments(Request $request)
{
    $user = Auth::user();

    // Synchronize departments from users table
    $this->syncDepartmentsFromUsers();

    // Get all departments with their settings
    $query = DepartmentSetting::query();

    // Apply search if present
    if ($request->has('search') && $request->search) {
        $search = '%' . $request->search . '%';
        $query->where('department', 'like', $search);
    }

    // Get departments with pagination
    $departments = $query->paginate(10)->withQueryString();

    // Calculate totals
    $totalBudget = 0;
    $totalMissions = 0;
    $totalTeachers = 0;

    // For each department, get summary statistics
    foreach ($departments as $dept) {
        // Get department head
        $dept->department_head = User::where('department', $dept->department)
                                   ->where('role', 'chef_departement')
                                   ->first();
        
        // Count teachers (only count users with role 'enseignant')
        $dept->teacherCount = User::where('department', $dept->department)
                                ->where('role', 'enseignant')
                                ->count();
        
        // Count missions
        $dept->missionCount = Mission::whereHas('user', function($query) use ($dept) {
            $query->where('department', $dept->department);
        })->count();
        
        // Update totals
        $totalBudget += $dept->budget;
        $totalMissions += $dept->missionCount;
        $totalTeachers += $dept->teacherCount;
    }

    return view('director.departments', compact(
        'user',
        'departments',
        'totalBudget',
        'totalMissions',
        'totalTeachers'
    ));
}


/**
 * Display department details.
 */
public function departmentDetails($department)
{
    $user = Auth::user();
    
    // Get department settings
    $departmentSettings = DepartmentSetting::where('department', $department)->firstOrFail();
    
    // Get department head
    $departmentHead = User::where('department', $department)
                        ->where('role', 'chef_departement')
                        ->first();
    
    // Get teachers in this department
    $teachers = User::where('department', $department)
                  ->where('role', 'enseignant')
                  ->get();
    
    // Get missions for this department
    $missions = Mission::whereHas('user', function($query) use ($department) {
                    $query->where('department', $department);
                })
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->paginate(10);
    
    // Get missions by status
    $missionsByStatus = Mission::whereHas('user', function($query) use ($department) {
                            $query->where('department', $department);
                        })
                        ->select('status', DB::raw('count(*) as count'))
                        ->groupBy('status')
                        ->pluck('count', 'status')
                        ->toArray();
    
    // Get missions by month for current year
    $currentYear = date('Y');
    $missionsByMonth = Mission::whereHas('user', function($query) use ($department) {
                            $query->where('department', $department);
                        })
                        ->whereYear('created_at', $currentYear)
                        ->select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as count'))
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
    
    return view('director.department_details', compact(
        'user',
        'department',
        'departmentSettings',
        'departmentHead',
        'teachers',
        'missions',
        'missionsByStatus',
        'monthLabels',
        'monthData'
    ));
}

/**
 * Update department settings.
 */
public function updateDepartmentSettings(Request $request, $department)
{
    $request->validate([
        'budget' => 'required|numeric|min:0',
        'description' => 'nullable|string',
        'director_validation' => 'boolean',
        'budget_check' => 'boolean',
    ]);
    
    $departmentSettings = DepartmentSetting::where('department', $department)->firstOrFail();
    
    $departmentSettings->budget = $request->budget;
    $departmentSettings->description = $request->description;
    $departmentSettings->director_validation = $request->has('director_validation');
    $departmentSettings->budget_check = $request->has('budget_check');
    
    $departmentSettings->save();
    
    return redirect()->route('director.department_details', $department)
        ->with('success', 'Les paramètres du département ont été mis à jour avec succès.');
}
public function storeDepartment(Request $request)
{
    $request->validate([
        'department_name' => 'required|string|max:100|unique:department_settings,department',
        'budget' => 'required|numeric|min:0',
        'description' => 'nullable|string',
    ]);
    
    $departmentSettings = new DepartmentSetting();
    $departmentSettings->department = $request->department_name;
    $departmentSettings->budget = $request->budget;
    $departmentSettings->description = $request->description;
    $departmentSettings->director_validation = $request->has('director_validation');
    $departmentSettings->budget_check = $request->has('budget_check');
    
    $departmentSettings->save();
    
    return redirect()->route('director.departments')
        ->with('success', 'Nouveau département créé avec succès.');
}

public function allocateBudgets(Request $request)
{
    $request->validate([
        'totalBudget' => 'required|numeric|min:0',
        'budgets' => 'required|array',
        'budgets.*' => 'required|numeric|min:0',
    ]);
    
    $totalBudget = $request->totalBudget;
    $budgets = $request->budgets;
    
    // Get total allocated budget
    $allocatedBudget = array_sum($budgets);
    
    // Check if allocation exceeds total budget
    if ($allocatedBudget > $totalBudget) {
        return redirect()->back()->with('error', 'Le total des budgets alloués dépasse le budget total.');
    }
    
    // Update each department's budget
    foreach ($budgets as $deptId => $budget) {
        $department = DepartmentSetting::findOrFail($deptId);
        $department->budget = $budget;
        $department->save();
    }
    
    return redirect()->route('director.departments')
        ->with('success', 'Les budgets ont été alloués avec succès.');
}

/**
 * Update a specific department's budget.
 */
public function updateDepartmentBudget(Request $request, $deptId)
{
    $request->validate([
        'budget' => 'required|numeric|min:0',
        'description' => 'nullable|string',
    ]);
    
    $department = DepartmentSetting::findOrFail($deptId);
    $department->budget = $request->budget;
    $department->description = $request->description;
    $department->save();
    
    return redirect()->route('director.departments')
        ->with('success', "Le budget du département {$department->department} a été mis à jour avec succès.");
}
public function createDepartment(Request $request)
{
    $request->validate([
        'department_name' => 'required|string|max:100|unique:department_settings,department',
        'department_budget' => 'required|numeric|min:0',
        'department_description' => 'nullable|string',
    ]);
    
    $department = new DepartmentSetting();
    $department->department = $request->department_name;
    $department->budget = $request->department_budget;
    $department->description = $request->department_description;
    $department->save();
    
    return redirect()->route('director.departments')
        ->with('success', "Le département {$department->department} a été créé avec succès.");
}

public function deleteDepartment($id)
{
    $department = DepartmentSetting::findOrFail($id);
    
    // Check if there are any missions associated with this department
    $missionsCount = Mission::whereHas('user', function($query) use ($department) {
        $query->where('department', $department->department);
    })->count();
    
    if ($missionsCount > 0) {
        return redirect()->route('director.departments')
            ->with('error', "Impossible de supprimer le département {$department->department}. Il y a encore {$missionsCount} mission(s) associée(s) à ce département.");
    }
    
    // Check if there are any users associated with this department
    $usersCount = User::where('department', $department->department)->count();
    
    if ($usersCount > 0) {
        return redirect()->route('director.departments')
            ->with('error', "Impossible de supprimer le département {$department->department}. Il y a encore {$usersCount} utilisateur(s) associé(s) à ce département.");
    }
    
    // If no missions or users, delete the department
    $departmentName = $department->department;
    $department->delete();
    
    return redirect()->route('director.departments')
        ->with('success', "Le département {$departmentName} a été supprimé avec succès.");
}
private function syncDepartmentsFromUsers()
{
    // Get all unique departments from users
    $userDepartments = User::whereNotNull('department')
        ->select('department')
        ->distinct()
        ->pluck('department');
    
    foreach ($userDepartments as $departmentName) {
        // Check if department already exists in department_settings
        $exists = DepartmentSetting::where('department', $departmentName)->exists();
        
        if (!$exists) {
            // Create new department setting
            DepartmentSetting::create([
                'department' => $departmentName,
                'budget' => 0, // Default budget
                'description' => 'Automatically created based on user assignments'
            ]);
        }
    }
}

public function reports(Request $request)
{
    $user = Auth::user();
    
    // Get date range for filtering
    $startDate = $request->input('start_date', Carbon::now()->subYear()->startOfMonth()->toDateString());
    $endDate = $request->input('end_date', Carbon::now()->toDateString());
    
    // Convert to Carbon instances for easier manipulation
    $startDateCarbon = Carbon::parse($startDate);
    $endDateCarbon = Carbon::parse($endDate);
    
    // Calculate the date ranges for comparison (previous period of same length)
    $dateRange = $startDateCarbon->diffInDays($endDateCarbon) + 1;
    $previousStartDate = $startDateCarbon->copy()->subDays($dateRange);
    $previousEndDate = $startDateCarbon->copy()->subDay();
    
    // Get missions within the selected date range
    $missionsQuery = Mission::whereBetween('created_at', [$startDate, $endDate]);
    $missionsCount = $missionsQuery->count();
    
    // Get missions for the previous period
    $previousMissionsCount = Mission::whereBetween('created_at', [$previousStartDate, $previousEndDate])->count();
    
    // Calculate growth percentage
    $missionGrowth = $previousMissionsCount > 0 
        ? round((($missionsCount - $previousMissionsCount) / $previousMissionsCount) * 100, 2)
        : 100;
    
    // Get mission status breakdown
    $statusBreakdown = Mission::whereBetween('created_at', [$startDate, $endDate])
        ->select('status', DB::raw('count(*) as count'))
        ->groupBy('status')
        ->pluck('count', 'status')
        ->toArray();
    
    // Get mission type breakdown
    $typeBreakdown = Mission::whereBetween('created_at', [$startDate, $endDate])
        ->select('type', DB::raw('count(*) as count'))
        ->groupBy('type')
        ->pluck('count', 'type')
        ->toArray();
    
    // Get department breakdown
    $departmentBreakdown = User::join('missions', 'users.id', '=', 'missions.user_id')
        ->whereBetween('missions.created_at', [$startDate, $endDate])
        ->select('department', DB::raw('count(missions.id) as count'))
        ->groupBy('department')
        ->pluck('count', 'department')
        ->toArray();
    
    // Get monthly trend data
    $monthlyTrend = [];
    $currentDate = $startDateCarbon->copy()->startOfMonth();
    $endMonth = $endDateCarbon->copy()->startOfMonth();
    
    while ($currentDate->lte($endMonth)) {
        $monthKey = $currentDate->format('Y-m');
        $monthName = $currentDate->format('M Y');
        
        $monthCount = Mission::whereYear('created_at', $currentDate->year)
            ->whereMonth('created_at', $currentDate->month)
            ->count();
        
        $monthlyTrend[$monthKey] = [
            'month' => $monthName,
            'count' => $monthCount
        ];
        
        $currentDate->addMonth();
    }
    
    // Get top teachers by mission count
    $topTeachers = User::where('role', 'enseignant')
        ->withCount(['missions' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }])
        ->having('missions_count', '>', 0)
        ->orderByDesc('missions_count')
        ->limit(10)
        ->get();
    
    // Get top destinations
    $topDestinations = Mission::whereBetween('created_at', [$startDate, $endDate])
        ->select('destination_city', DB::raw('count(*) as count'))
        ->groupBy('destination_city')
        ->orderByDesc('count')
        ->limit(10)
        ->get();
    
    // Calculate average mission duration
    $avgDuration = Mission::whereBetween('created_at', [$startDate, $endDate])
        ->selectRaw('AVG(DATEDIFF(end_date, start_date) + 1) as avg_duration')
        ->first()
        ->avg_duration ?? 0;
    
    // Calculate completion rate 
    $completedCount = $statusBreakdown['terminee'] ?? 0;
    $completionRate = $missionsCount > 0 ? round(($completedCount / $missionsCount) * 100, 2) : 0;
    
    // Calculate rejection rate
    $rejectedCount = $statusBreakdown['rejetee'] ?? 0;
    $rejectionRate = $missionsCount > 0 ? round(($rejectedCount / $missionsCount) * 100, 2) : 0;
    
    // Calculate time-to-approval (average days between creation and director approval)
    $timeToApproval = Mission::whereBetween('created_at', [$startDate, $endDate])
        ->whereNotNull('director_approval_date')
        ->selectRaw('AVG(DATEDIFF(director_approval_date, created_at)) as avg_days')
        ->first()
        ->avg_days ?? 0;
    
    return view('director.reports', compact(
        'user',
        'startDate',
        'endDate',
        'missionsCount',
        'missionGrowth',
        'statusBreakdown',
        'typeBreakdown',
        'departmentBreakdown',
        'monthlyTrend',
        'topTeachers',
        'topDestinations',
        'avgDuration',
        'completionRate',
        'rejectionRate',
        'timeToApproval'
    ));
}

public function settings()
{
    $user = Auth::user();
    return view('director.settings', compact('user'));
}

/**
 * Update profile settings.
 */
public function updateProfile(Request $request)
{
    $user = Auth::user();
    
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
        'phone' => 'nullable|string|max:20',
        'cin' => 'nullable|string|max:20',
        'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ]);
    
    // Update user profile
    $user->name = $request->name;
    $user->email = $request->email;
    $user->phone = $request->phone;
    $user->cin = $request->cin;
    
    // Handle profile photo upload
    if ($request->hasFile('profile_photo')) {
        $path = $request->file('profile_photo')->store('profile-photos', 'public');
        $user->profile_photo_path = $path;
    }
    
    $user->save();
    
    return redirect()->route('director.settings')
        ->with('success', 'Votre profil a été mis à jour avec succès.');
}

/**
 * Update security settings (password).
 */
public function updatePassword(Request $request)
{
    $request->validate([
        'current_password' => 'required|string',
        'password' => 'required|string|min:8|confirmed',
    ]);
    
    $user = Auth::user();
    
    // Verify current password
    if (!Hash::check($request->current_password, $user->password)) {
        return redirect()->back()
            ->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
    }
    
    // Update password
    $user->password = Hash::make($request->password);
    $user->save();
    
    return redirect()->route('director.settings')
        ->with('success', 'Votre mot de passe a été mis à jour avec succès.');
}
}
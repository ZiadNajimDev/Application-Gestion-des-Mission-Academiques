<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Mission;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Dashboard for teachers
     */
    public function teacherDashboard()
    {
        $user = Auth::user();
        
        // Get recent missions
        $recentMissions = $user->missions()
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
            
        // Get upcoming missions (missions that haven't started yet)
        $upcomingMissions = $user->missions()
            ->where('start_date', '>', Carbon::now())
            ->orderBy('start_date', 'asc')
            ->take(3)
            ->get();
            
        // Process upcoming missions to add some useful properties
        foreach ($upcomingMissions as $mission) {
            $startDate = Carbon::parse($mission->start_date);
            $mission->days_until = Carbon::now()->diffInDays($startDate, false);
            $mission->formatted_start_date = $startDate->format('d/m/Y');
            $mission->formatted_end_date = Carbon::parse($mission->end_date)->format('d/m/Y');
        }
        
        // Get missions pending action (e.g., requiring document upload)
        $pendingMissions = $user->missions()
            ->whereIn('status', ['validee_chef', 'validee_directeur', 'billet_reserve'])
            ->orderBy('start_date', 'asc')
            ->take(3)
            ->get();
            
        // Get missions by status for statistics
        $missionsByStatus = [
            'soumise' => $user->missions()->where('status', 'soumise')->count(),
            'validee_chef' => $user->missions()->where('status', 'validee_chef')->count(),
            'validee_directeur' => $user->missions()->where('status', 'validee_directeur')->count(),
            'billet_reserve' => $user->missions()->where('status', 'billet_reserve')->count(),
            'terminee' => $user->missions()->where('status', 'terminee')->count(),
            'rejetee' => $user->missions()->where('status', 'rejetee')->count(),
        ];
        
        // Get missions by type for statistics
        $missionsByType = [
            'nationale' => $user->missions()->where('type', 'nationale')->count(),
            'internationale' => $user->missions()->where('type', 'internationale')->count(),
        ];
        
        // Get missions by month for chart
        $missionsByMonth = $user->missions()
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as count'))
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();
            
        // Generate month labels and counts for all 12 months
        $months = [
            1 => 'Jan', 2 => 'Fév', 3 => 'Mar', 4 => 'Avr', 
            5 => 'Mai', 6 => 'Juin', 7 => 'Juil', 8 => 'Août', 
            9 => 'Sept', 10 => 'Oct', 11 => 'Nov', 12 => 'Déc'
        ];
        
        $chartLabels = [];
        $chartData = [];
        
        foreach ($months as $num => $name) {
            $chartLabels[] = $name;
            $chartData[] = $missionsByMonth[$num] ?? 0;
        }
        
        // Get recent notifications (real notifications from the database)
        $notifications = Auth::user()->notifications()
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
        
        // Get mission statistics
        $totalMissions = $user->missions()->count();
        $completedMissions = $user->missions()->where('status', 'terminee')->count();
        $inProgressMissions = $user->missions()->whereIn('status', ['validee_chef', 'validee_directeur', 'billet_reserve'])->count();
        $pendingValidationMissions = $user->missions()->where('status', 'soumise')->count();
        
        return view('teacher.dashboard', compact(
            'user',
            'recentMissions',
            'upcomingMissions',
            'pendingMissions',
            'missionsByStatus',
            'missionsByType',
            'chartLabels',
            'chartData',
            'notifications',
            'totalMissions',
            'completedMissions',
            'inProgressMissions',
            'pendingValidationMissions'
        ));
    }
    
    // Other dashboard methods for different roles will go here
}
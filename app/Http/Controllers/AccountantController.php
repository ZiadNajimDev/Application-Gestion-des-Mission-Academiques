<?php

namespace App\Http\Controllers;

use App\Models\Mission;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\MissionProof;
use Illuminate\Http\Request;
use App\Models\ProofDocument;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Storage;
use App\Models\DepartmentSetting;
use App\Models\DepartmentExpense;

class AccountantController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:comptable');
    }

  
    public function dashboard()
    {
        $user = Auth::user();
        
        // Dashboard statistics
        $pendingReservations = Mission::where('status', 'validee_directeur')->count();
        $completedReservations = Mission::where('status', 'billet_reserve')->count();
        $totalMissions = Mission::count();
        
        // Get missions needing reservations (recently approved by director)
        $reservationsMissions = Mission::where('status', 'validee_directeur')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Get pending proof documents
        $pendingProofs = ProofDocument::where('status', 'pending')
            ->with(['mission', 'mission.user'])
            ->count();
        
        // Get recent proof submissions
        $recentProofs = ProofDocument::with(['mission', 'mission.user'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Get unpaid missions with approved proofs
        $unpaidMissions = Mission::where('status', 'billet_reserve')
            ->whereDoesntHave('payment', function($query) {
                $query->where('status', 'paid');
            })
            ->whereHas('proofDocuments', function($query) {
                $query->where('status', 'approved');
            })
            ->count();
        
        // Get missions ready for completion
        $readyForCompletion = Mission::where('status', 'billet_reserve')
            ->whereHas('proofDocuments', function($query) {
                $query->where('status', 'approved');
            })
            ->whereHas('payment', function($query) {
                $query->where('status', 'paid');
            })
            ->count();
        
        // Financial statistics
        $totalPayments = Payment::sum('total_amount');
        $monthlyPayments = Payment::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');
        
        // Monthly statistics for charts
        $monthlyData = Mission::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();
        
        $months = [];
        $missionCounts = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $months[] = date('F', mktime(0, 0, 0, $i, 1));
            $missionCounts[] = $monthlyData[$i] ?? 0;
        }
        
        return view('accountant.dashboard', compact(
            'user',
            'pendingReservations',
            'completedReservations',
            'totalMissions',
            'reservationsMissions',
            'pendingProofs',
            'recentProofs',
            'unpaidMissions',
            'readyForCompletion',
            'totalPayments',
            'monthlyPayments',
            'months',
            'missionCounts'
        ));
    }
    
    public function reservations()
    {
        $user = Auth::user();
        
        // Get missions that need reservations (already approved by director)
        $missions = Mission::whereIn('status', ['validee_directeur', 'billet_reserve', 'terminee'])
           ->with(['user', 'reservations'])
           ->orderBy('created_at', 'desc')
           ->paginate(10); 
        
        return view('accountant.reservations', compact('user', 'missions'));
    }
    
    public function storeReservation(Request $request)
    {
        $request->validate([
            'mission_id' => 'required|exists:missions,id',
            'type' => 'required|in:flight,train,bus,car,hotel',
            'provider' => 'required_if:type,flight,train,bus,hotel|nullable|string|max:255',
            'reservation_number' => 'required_if:type,flight,train|nullable|string|max:255',
            'cost' => 'required|numeric|min:0',
            'departure_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:departure_date',
            'hotel_name' => 'nullable|string|max:255',
            'hotel_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);
    
        $mission = Mission::findOrFail($request->mission_id);
        
        // Store the main reservation (transport)
        $reservation = new Reservation();
        $reservation->mission_id = $request->mission_id;
        $reservation->user_id = Auth::id();
        $reservation->status = 'completed';
        $reservation->type = $request->type;
        $reservation->provider = $request->provider;
        $reservation->reservation_number = $request->reservation_number;
        $reservation->cost = $request->cost;
        $reservation->reservation_date = $request->departure_date;
        $reservation->notes = $request->notes;
        
        // Handle file upload
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('reservations', 'public');
            $reservation->attachment = $path;
        }
        
        $reservation->save();
    
        // If hotel is needed, create a separate reservation for it
        if ($request->has('hotel_name') && $request->hotel_name && $request->hotel_cost) {
            $hotelReservation = new Reservation();
            $hotelReservation->mission_id = $request->mission_id;
            $hotelReservation->user_id = Auth::id();
            $hotelReservation->status = 'completed';
            $hotelReservation->type = 'hotel';
            $hotelReservation->provider = $request->hotel_name;
            $hotelReservation->cost = $request->hotel_cost;
            $hotelReservation->reservation_date = $request->departure_date;
            $hotelReservation->notes = 'Hébergement pour la mission';
            $hotelReservation->save();
        }
        
        // Update mission status
        $mission->status = 'billet_reserve';
        $mission->save();
    
        // Send notification to teacher
        $notificationService = app(NotificationService::class);
        $notificationService->notifyUser(
            $mission->user_id,
            'Réservation effectuée',
            'Votre mission a été réservée. N\'oubliez pas de soumettre vos justificatifs financiers après la mission.',
            'success',
            'ticket-alt',
            route('teacher.missions.show', $mission->id),
            $mission->id
        );
        
        return redirect()->route('accountant.reservations')
            ->with('success', 'Réservation ajoutée avec succès');
    }
    
    public function updateReservation(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);
        
        $request->validate([
            'type' => 'required|in:flight,train,hotel,other',
            'provider' => 'required|string|max:255',
            'reservation_number' => 'required|string|max:255',
            'cost' => 'required|numeric|min:0',
            'reservation_date' => 'required|date',
            'notes' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);
        
        // Update reservation
        $reservation->type = $request->type;
        $reservation->provider = $request->provider;
        $reservation->reservation_number = $request->reservation_number;
        $reservation->cost = $request->cost;
        $reservation->reservation_date = $request->reservation_date;
        $reservation->notes = $request->notes;
        $reservation->status = $request->status ?? $reservation->status;
        
        // Handle file upload
        if ($request->hasFile('attachment')) {
            // Delete old file if exists
            if ($reservation->attachment && Storage::disk('public')->exists($reservation->attachment)) {
                Storage::disk('public')->delete($reservation->attachment);
            }
            
            $path = $request->file('attachment')->store('reservations', 'public');
            $reservation->attachment = $path;
        }
        
        $reservation->save();
        
        return redirect()->route('accountant.reservations')
            ->with('success', 'Réservation mise à jour avec succès');
    }
    
    public function cancelReservation($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->status = 'cancelled';
        $reservation->save();
        
        return redirect()->route('accountant.reservations')
            ->with('success', 'Réservation annulée avec succès');
    }
    
    public function settings()
    {
        return view('accountant.settings', ['user' => Auth::user()]);
    }
    
public function completeMission($id)
{
    $mission = Mission::findOrFail($id);
    
    // Update mission status
    $mission->status = 'billet_reserve';
    $mission->save();
    
    return redirect()->route('accountant.reservations')
        ->with('success', 'Mission marquée comme réservée avec succès');
}

public function payments()
{
    $user = Auth::user();
    
    // Get missions that have all required proof documents submitted and approved
    $missions = Mission::whereIn('status', ['billet_reserve', 'terminee'])
        ->whereHas('proofs', function($query) {
            $query->where('status', 'approved')
                  ->where('category', 'financier');
        })
        ->with(['user', 'payment', 'proofs' => function($query) {
            $query->where('category', 'financier');
        }])
        ->orderBy('created_at', 'desc')
        ->paginate(10);
    
    // Get department budgets and expenses
    $departments = DepartmentSetting::with(['expenses' => function($query) {
        $query->whereYear('created_at', now()->year);
    }])->get();
    
    $departmentBudgets = [];
    foreach ($departments as $dept) {
        $totalExpenses = $dept->expenses->sum('amount');
        $departmentBudgets[$dept->department] = [
            'budget' => $dept->budget,
            'spent' => $totalExpenses,
            'remaining' => $dept->budget - $totalExpenses
        ];
    }
    
    return view('accountant.payments', compact('user', 'missions', 'departmentBudgets'));
}

public function storePayment(Request $request)
{
    $request->validate([
        'mission_id' => 'required|exists:missions,id',
        'payment_method' => 'required|in:virement,cheque,especes',
        'payment_reference' => 'nullable|string|max:255',
        'payment_date' => 'required|date',
        'comments' => 'nullable|string',
    ]);
    
    $mission = Mission::with(['user', 'proofs' => function($query) {
        $query->where('category', 'financier')
              ->where('status', 'approved');
    }])->findOrFail($request->mission_id);
    
    // Calculate amounts based on mission type and duration
    $startDate = \Carbon\Carbon::parse($mission->start_date);
    $endDate = \Carbon\Carbon::parse($mission->end_date);
    $durationDays = $startDate->diffInDays($endDate) + 1;
    
    // Define daily allowance based on mission type
    $dailyAllowance = $mission->type === 'internationale' ? 2000 : 400;
    $allowanceAmount = $dailyAllowance * $durationDays;
    
    // Get transport costs from reservations
    $transportAmount = $mission->reservations()->sum('cost');
    
    // Get additional costs from approved financial proof documents
    $additionalCosts = $mission->proofs->sum('amount');
    
    $totalAmount = $allowanceAmount + $transportAmount + $additionalCosts;
    
    // Check department budget
    $department = $mission->user->department;
    $departmentSetting = DepartmentSetting::where('department', $department)->first();
    
    if ($departmentSetting && $departmentSetting->budget_check) {
        $currentYearExpenses = DepartmentExpense::where('department', $department)
            ->whereYear('created_at', now()->year)
            ->sum('amount');
        
        $remainingBudget = $departmentSetting->budget - $currentYearExpenses;
        
        if ($totalAmount > $remainingBudget) {
            return redirect()->back()
                ->with('error', "Le budget restant du département ({$remainingBudget} DH) est insuffisant pour cette mission ({$totalAmount} DH).");
        }
    }
    
    // Create or update payment
    $payment = Payment::updateOrCreate(
        ['mission_id' => $mission->id],
        [
            'user_id' => Auth::id(),
            'allowance_amount' => $allowanceAmount,
            'transport_amount' => $transportAmount,
            'additional_costs' => $additionalCosts,
            'total_amount' => $totalAmount,
            'status' => 'paid',
            'payment_method' => $request->payment_method,
            'payment_reference' => $request->payment_reference,
            'payment_date' => $request->payment_date,
            'comments' => $request->comments,
        ]
    );
    
    // Record department expenses
    if ($allowanceAmount > 0) {
        DepartmentExpense::create([
            'department' => $department,
            'mission_id' => $mission->id,
            'payment_id' => $payment->id,
            'amount' => $allowanceAmount,
            'type' => 'allowance',
            'description' => 'Indemnité journalière pour ' . $durationDays . ' jours'
        ]);
    }
    
    if ($transportAmount > 0) {
        DepartmentExpense::create([
            'department' => $department,
            'mission_id' => $mission->id,
            'payment_id' => $payment->id,
            'amount' => $transportAmount,
            'type' => 'transport',
            'description' => 'Frais de transport'
        ]);
    }
    
    if ($additionalCosts > 0) {
        DepartmentExpense::create([
            'department' => $department,
            'mission_id' => $mission->id,
            'payment_id' => $payment->id,
            'amount' => $additionalCosts,
            'type' => 'additional',
            'description' => 'Frais additionnels'
        ]);
    }
    
    return redirect()->route('accountant.payments')
        ->with('success', 'Paiement enregistré avec succès');
}

public function printPaymentReceipt($id)
{
    $payment = Payment::with(['mission', 'mission.user'])->findOrFail($id);
    
    // In a real application, you'd generate a PDF here
    // For now, let's just return a view
    return view('accountant.payment_receipt', compact('payment'));
}

public function proofs()
{
    $user = Auth::user();
    
    // Get missions with proof documents that need verification
    $missions = Mission::whereIn('status', ['billet_reserve', 'terminee'])
        ->with(['user', 'proofs'])
        ->orderBy('created_at', 'desc')
        ->paginate(10);
    
    // Get individual proof documents for easy filtering
    $proofDocuments = MissionProof::with(['mission', 'mission.user'])
        ->orderBy('created_at', 'desc')
        ->paginate(10);
    
    return view('accountant.proofs', compact('user', 'missions', 'proofDocuments'));
}

public function processProofDocument(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:approved,rejected',
        'reviewer_comment' => 'required_if:status,rejected|nullable|string|max:500',
    ]);

    $proof = MissionProof::findOrFail($id);

    // Update proof document
    $proof->update([
        'status' => $request->status,
        'reviewer_id' => Auth::id(),
        'reviewer_comment' => $request->reviewer_comment,
        'reviewed_at' => now(),
    ]);

    // Check if mission should be marked as complete
    $this->checkMissionCompletion($proof->mission_id);

    $message = $request->status == 'approved' 
        ? 'Justificatif approuvé avec succès' 
        : 'Justificatif rejeté avec succès';

    return redirect()->route('accountant.proofs')
        ->with('success', $message);
}
private function checkMissionCompletion($missionId)
{
    $mission = Mission::findOrFail($missionId);

    // Check if all required proof documents are submitted and approved
    $requiredProofs = $mission->proofs()
        ->whereIn('category', ['financier', 'execution', 'retour'])
        ->get();

    $allProofsApproved = $requiredProofs->count() > 0 && 
                       $requiredProofs->where('status', 'approved')->count() === $requiredProofs->count();

    // If all justificatifs are approved, update mission status
    if ($allProofsApproved) {
        $mission->status = 'terminee';
        $mission->save();

        // Notify the teacher
       
    }
}

public function showProofDocument($id)
{
    $proofDocument = \App\Models\MissionProof::with(['mission', 'mission.user'])->findOrFail($id);
    
    return view('accountant.proof_detail', compact('proofDocument'));
}

public function downloadProofDocument($id)
{
    $proofDocument = MissionProof::findOrFail($id);
    
    // Check if file exists
    if (!Storage::disk('public')->exists($proofDocument->file_path)) {
        return redirect()->route('accountant.proofs')
            ->with('error', 'Le fichier demandé n\'existe pas.');
    }
    
    return Storage::disk('public')->download($proofDocument->file_path, $proofDocument->file_name);
}

public function markMissionComplete($id)
{
    $mission = Mission::findOrFail($id);
    
    // Verify all requirements are met
    $proofDocuments = $mission->proofDocuments;
    $allDocumentsApproved = $proofDocuments->count() > 0 && 
                          $proofDocuments->where('status', 'approved')->count() == $proofDocuments->count();
    
    $hasPayment = $mission->payment && $mission->payment->status == 'paid';
    
    if (!$allDocumentsApproved || !$hasPayment) {
        return redirect()->back()->with('error', 'Cette mission ne peut pas être marquée comme terminée. Vérifiez que tous les justificatifs sont approuvés et que le paiement est effectué.');
    }
    
    // Update mission status
    $mission->status = 'terminee';
    $mission->save();
    app(NotificationService::class)->notifyMissionCompleted($mission);
    return redirect()->back()->with('success', 'Mission marquée comme terminée avec succès.');
}

private function getRequiredDocumentTypes($mission)
{
    if ($mission->type === 'internationale') {
        return [
            'passport_stamps', 
            'boarding_pass', 
            'conference_proof', 
            'hotel_receipt'
        ];
    } else { // nationale
        return [
            'transport_ticket', 
            'hotel_receipt', 
            'meeting_proof'
        ];
    }
}

private function hasAllRequiredDocuments($mission)
{
    $requiredTypes = $this->getRequiredDocumentTypes($mission);
    $approvedTypes = $mission->proofDocuments()
                            ->where('status', 'approved')
                            ->pluck('proof_type')
                            ->toArray();
    
    foreach ($requiredTypes as $type) {
        if (!in_array($type, $approvedTypes)) {
            return false;
        }
    }
    
    return true;
}

public function updateProfile(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
        'cin' => 'nullable|string|max:20',
        'phone' => 'nullable|string|max:20',
    ]);
    
    $user = Auth::user();
    $user->name = $request->name;
    $user->email = $request->email;
    $user->cin = $request->cin;
    $user->phone = $request->phone;
    $user->save();
    
    return redirect()->route('accountant.settings')
        ->with('profile_success', 'Votre profil a été mis à jour avec succès.');
}

public function updatePassword(Request $request)
{
    $request->validate([
        'current_password' => 'required|string|current_password',
        'password' => 'required|string|min:8|confirmed',
    ]);
    
    $user = Auth::user();
    $user->password = Hash::make($request->password);
    $user->save();
    
    return redirect()->route('accountant.settings')
        ->with('password_success', 'Votre mot de passe a été mis à jour avec succès.');
}

public function updateNotificationPreferences(Request $request)
{
    $request->validate([
        'email_notifications' => 'nullable|boolean',
        'in_app_notifications' => 'nullable|boolean',
        'new_mission_notifications' => 'nullable|boolean',
        'proof_submission_notifications' => 'nullable|boolean',
        'payment_notifications' => 'nullable|boolean',
    ]);
    
    $user = Auth::user();
    
    // Store notification preferences in JSON format
    $preferences = [
        'channels' => [
            'email' => $request->has('email_notifications'),
            'in_app' => $request->has('in_app_notifications'),
        ],
        'types' => [
            'new_mission' => $request->has('new_mission_notifications'),
            'proof_submission' => $request->has('proof_submission_notifications'),
            'payment' => $request->has('payment_notifications'),
        ]
    ];
    
    // Store preferences in the user's record - add a notification_preferences column to users table if needed
    $user->notification_preferences = json_encode($preferences);
    $user->save();
    
    return redirect()->route('accountant.settings')
        ->with('notification_success', 'Vos préférences de notification ont été mises à jour.');
}
}
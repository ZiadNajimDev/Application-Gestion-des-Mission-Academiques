<?php

namespace App\Http\Controllers;

use App\Models\DepartmentBudget;
use App\Models\DepartmentExpense;
use App\Models\Mission;
use App\Models\Payment;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    private $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function processPayment(Request $request, Mission $mission)
    {
        $request->validate([
            'payment_method' => 'required|in:bank_transfer,cash,check',
            'payment_date' => 'required|date',
            'reference_number' => 'required_if:payment_method,bank_transfer,check',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Check department budget
            $department = $mission->department;
            $departmentBudget = DepartmentBudget::where('department_id', $department->id)->first();
            
            if (!$departmentBudget || $departmentBudget->remaining_budget < $mission->total_amount) {
                $this->notificationService->notifyInsufficientBudget(
                    $mission,
                    $department->name,
                    $mission->total_amount,
                    $departmentBudget ? $departmentBudget->remaining_budget : 0
                );
                
                return back()->with('error', 'Budget insuffisant pour effectuer le paiement.');
            }

            // Create payment record
            $payment = Payment::create([
                'mission_id' => $mission->id,
                'amount' => $mission->total_amount,
                'payment_method' => $request->payment_method,
                'payment_date' => $request->payment_date,
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
                'status' => 'completed'
            ]);

            // Update mission status
            $mission->update(['status' => 'paid']);

            // Update department budget
            $departmentBudget->update([
                'remaining_budget' => $departmentBudget->remaining_budget - $mission->total_amount
            ]);

            // Record department expense
            DepartmentExpense::create([
                'department_id' => $department->id,
                'mission_id' => $mission->id,
                'amount' => $mission->total_amount,
                'description' => 'Paiement mission: ' . $mission->title
            ]);

            // Notify teacher about payment
            $this->notificationService->notifyPaymentMade($mission, $mission->total_amount);

            DB::commit();
            return back()->with('success', 'Paiement effectué avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors du paiement: ' . $e->getMessage());
        }
    }

    public function rejectPayment(Request $request, Mission $mission)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            // Create payment record with rejected status
            Payment::create([
                'mission_id' => $mission->id,
                'amount' => $mission->total_amount,
                'payment_method' => null,
                'payment_date' => null,
                'reference_number' => null,
                'notes' => $request->rejection_reason,
                'status' => 'rejected'
            ]);

            // Update mission status
            $mission->update(['status' => 'rejected']);

            // Notify teacher about rejection
            $this->notificationService->notifyPaymentRejected($mission, $request->rejection_reason);

            DB::commit();
            return back()->with('success', 'Paiement rejeté avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors du rejet du paiement: ' . $e->getMessage());
        }
    }

    public function requestAdditionalDocuments(Request $request, Mission $mission)
    {
        $request->validate([
            'requested_documents' => 'required|string|max:500'
        ]);

        try {
            // Update mission status
            $mission->update([
                'status' => 'additional_documents_needed',
                'notes' => $request->requested_documents
            ]);

            // Notify teacher about additional documents needed
            $this->notificationService->notifyAdditionalDocumentsRequested(
                $mission,
                $request->requested_documents
            );

            return back()->with('success', 'Demande de documents supplémentaires envoyée.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la demande de documents: ' . $e->getMessage());
        }
    }
} 
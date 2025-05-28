<?php

namespace App\Http\Controllers;

use App\Models\Mission;
use App\Models\ProofDocument;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProofDocumentController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function store(Request $request, Mission $mission)
    {
        $request->validate([
            'documents.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->file('documents') as $file) {
                $path = $file->store('proof_documents');
                
                ProofDocument::create([
                    'mission_id' => $mission->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'notes' => $request->notes
                ]);
            }

            // Update mission status
            $mission->update(['status' => 'proof_documents_submitted']);

            // Notify accountant about new proof documents
            $this->notificationService->notifyProofDocumentsSubmitted($mission);

            DB::commit();
            return back()->with('success', 'Documents soumis avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la soumission des documents: ' . $e->getMessage());
        }
    }

    public function validateDocuments(Request $request, Mission $mission)
    {
        $request->validate([
            'validation_notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Update all proof documents as validated
            ProofDocument::where('mission_id', $mission->id)
                ->update(['status' => 'validated']);

            // Update mission status
            $mission->update([
                'status' => 'proof_documents_validated',
                'notes' => $request->validation_notes
            ]);

            // Notify teacher about validation
            $this->notificationService->notifyProofDocumentsValidated($mission);

            DB::commit();
            return back()->with('success', 'Documents validés avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la validation des documents: ' . $e->getMessage());
        }
    }

    public function rejectDocuments(Request $request, Mission $mission)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            // Update all proof documents as rejected
            ProofDocument::where('mission_id', $mission->id)
                ->update(['status' => 'rejected']);

            // Update mission status
            $mission->update([
                'status' => 'proof_documents_rejected',
                'notes' => $request->rejection_reason
            ]);

            // Notify teacher about rejection
            $this->notificationService->notifyAdditionalDocumentsRequested(
                $mission,
                $request->rejection_reason
            );

            DB::commit();
            return back()->with('success', 'Documents rejetés avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors du rejet des documents: ' . $e->getMessage());
        }
    }
} 
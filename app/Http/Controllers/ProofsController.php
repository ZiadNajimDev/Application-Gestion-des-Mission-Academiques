<?php

namespace App\Http\Controllers;

use App\Models\Mission;
use App\Models\MissionProof;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProofsController extends Controller
{
    /**
     * Show the form for submitting proofs.
     *
     * @param  \App\Models\Mission  $mission
     * @return \Illuminate\Http\Response
     */
    public function create(Mission $mission)
    {
        // Check if the user owns this mission
        $this->authorize('view', $mission);
        
        // Get already submitted proofs
        $financialProofs = $mission->financialProofs;
        $executionProofs = $mission->executionProofs;
        $returnProofs = $mission->returnProofs;
        
        return view('teacher.proofs.create', compact('mission', 'financialProofs', 'executionProofs', 'returnProofs'));
    }

    /**
     * Store a newly created proof in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'mission_id' => 'required|exists:missions,id',
            'category' => 'required|in:financier,execution,retour',
            'proof_type' => 'required|string|max:255',
            'proof_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'amount' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);
        
        // Check if the user owns this mission
        $mission = Mission::findOrFail($request->mission_id);
        $this->authorize('view', $mission);
        
        // Store the file
        $file = $request->file('proof_file');
        $path = $file->store('proof_documents', 'public');
        
        // Create the proof record
        $proof = new MissionProof();
        $proof->mission_id = $request->mission_id;
        $proof->category = $request->category;
        $proof->proof_type = $request->proof_type;
        $proof->file_path = $path;
        $proof->file_name = $file->getClientOriginalName();
        $proof->file_type = $file->getClientMimeType();
        $proof->file_size = $file->getSize();
        $proof->amount = $request->amount;
        $proof->description = $request->description;
        $proof->status = 'pending';
        $proof->save();
        
        return redirect()->route('teacher.proofs.create', $mission)
            ->with('success', 'Justificatif soumis avec succès.');
    }

    /**
     * Display the specified proof.
     *
     * @param  \App\Models\MissionProof  $proof
     * @return \Illuminate\Http\Response
     */
    public function show(MissionProof $proof)
    {
        // Check if the user owns this proof
        $this->authorize('view', $proof->mission);
        
        return response()->file(Storage::disk('public')->path($proof->file_path));
    }

    /**
     * Remove the specified proof from storage.
     *
     * @param  \App\Models\MissionProof  $proof
     * @return \Illuminate\Http\Response
     */
    public function destroy(MissionProof $proof)
    {
        // Check if the user owns this proof and it's still pending
        $this->authorize('view', $proof->mission);
        
        if ($proof->status !== 'pending') {
            return redirect()->back()->with('error', 'Vous ne pouvez pas supprimer un justificatif qui a déjà été traité.');
        }
        
        // Delete the file
        Storage::disk('public')->delete($proof->file_path);
        
        // Delete the record
        $proof->delete();
        
        return redirect()->back()->with('success', 'Justificatif supprimé avec succès.');
    }
}
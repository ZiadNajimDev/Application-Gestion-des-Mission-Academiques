<?php
// app/Http/Controllers/MissionController.php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Mission;
use Illuminate\Http\Request;
use App\Models\MissionDocument;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Storage;

class MissionController extends Controller
{
    
    /**
     * Display a listing of the missions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Auth::user()->missions();

        // Filter by status if provided
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Filter by type if provided
        if ($request->has('type') && $request->type != 'all') {
            $query->where('type', $request->type);
        }

        // Filter by date range if provided
        if ($request->has('date_start') && $request->date_start) {
            $query->where('start_date', '>=', $request->date_start);
        }
        if ($request->has('date_end') && $request->date_end) {
            $query->where('end_date', '<=', $request->date_end);
        }

        // Search by title or destination
        if ($request->has('search') && $request->search) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                  ->orWhere('destination_city', 'like', $searchTerm)
                  ->orWhere('destination_institution', 'like', $searchTerm);
            });
        }

        // Sort by creation date, defaulting to most recent first
        $sortDirection = $request->has('sort_dir') && $request->sort_dir === 'asc' ? 'asc' : 'desc';
        $query->orderBy('created_at', $sortDirection);

        // Paginate the results
        $missions = $query->paginate(5)->withQueryString();

        // Get counts for filtering options
        $statusCounts = [
            'all' => Auth::user()->missions()->count(),
            'soumise' => Auth::user()->missions()->where('status', 'soumise')->count(),
            'validee_chef' => Auth::user()->missions()->where('status', 'validee_chef')->count(),
            'validee_directeur' => Auth::user()->missions()->where('status', 'validee_directeur')->count(),
            'billet_reserve' => Auth::user()->missions()->where('status', 'billet_reserve')->count(),
            'terminee' => Auth::user()->missions()->where('status', 'terminee')->count(),
            'rejetee' => Auth::user()->missions()->where('status', 'rejetee')->count(),
        ];

        // Process missions to add some computed properties
        foreach ($missions as $mission) {
            // Add a human-readable date range
            $startDate = Carbon::parse($mission->start_date);
            $endDate = Carbon::parse($mission->end_date);
            $mission->date_range = $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y');
            
            // Add duration in days
            $mission->duration = $startDate->diffInDays($endDate) + 1;
            
            // Add a status label for display
            switch($mission->status) {
                case 'soumise':
                    $mission->status_label = 'Soumise';
                    $mission->status_class = 'warning';
                    break;
                case 'validee_chef':
                    $mission->status_label = 'Validée (chef)';
                    $mission->status_class = 'info';
                    break;
                case 'validee_directeur':
                    $mission->status_label = 'Validée (directeur)';
                    $mission->status_class = 'primary';
                    break;
                case 'billet_reserve':
                    $mission->status_label = 'Billet réservé';
                    $mission->status_class = 'secondary';
                    break;
                case 'terminee':
                    $mission->status_label = 'Terminée';
                    $mission->status_class = 'success';
                    break;
                case 'rejetee':
                    $mission->status_label = 'Rejetée';
                    $mission->status_class = 'danger';
                    break;
                default:
                    $mission->status_label = ucfirst($mission->status);
                    $mission->status_class = 'secondary';
            }
        }

        return view('teacher.missions.index', compact('missions', 'statusCounts'));
    }


    public function create()
    {
        return view('teacher.missions.create');
    }

     /**
     * Store a newly created mission in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'missionType' => 'required|in:nationale,internationale',
            'transportType' => 'required|in:voiture,transport_public,train,avion',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
            'destinationCity' => 'required|string|max:255',
            'destinationInstitution' => 'required|string|max:255',
            'missionTitle' => 'required|string|max:255',
            'missionObjective' => 'required|string',
            'supervisorName' => 'nullable|string|max:255',
            'additionalDocuments.*' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $mission = new Mission();
        $mission->user_id = Auth::id();
        $mission->type = $request->missionType;
        $mission->transport_type = $request->transportType;
        $mission->start_date = $request->startDate;
        $mission->end_date = $request->endDate;
        $mission->destination_city = $request->destinationCity;
        $mission->destination_institution = $request->destinationInstitution;
        $mission->title = $request->missionTitle;
        $mission->objective = $request->missionObjective;
        $mission->supervisor_name = $request->supervisorName;
        $mission->status = 'soumise';

        $mission->save();

        // Handle multiple file uploads if present
        if ($request->hasFile('additionalDocuments')) {
            foreach ($request->file('additionalDocuments') as $file) {
                $path = $file->store('mission_documents', 'public');
                
                // Create a document record linked to this mission
                $document = new MissionDocument();
                $document->mission_id = $mission->id;
                $document->file_path = $path;
                $document->file_name = $file->getClientOriginalName();
                $document->document_type = $file->getClientMimeType();
              //  $document->file_size = $file->getSize();
                $document->save();
            }
        }

        // Notify chef de département
        // This would be implemented with Laravel Notifications
        app(NotificationService::class)->notifyMissionSubmitted($mission);
    
        return redirect()->route('teacher.missions.index')
            ->with('success', 'Votre mission a été soumise avec succès et sera examinée par votre chef de département.');
    }

    public function show($id)
    {
        $mission = Mission::findOrFail($id);
        $this->authorize('view', $mission);
        
        return view('teacher.missions.show', compact('mission'));
    }
    /**
 * Remove the specified mission.
 *
 * @param  \App\Models\Mission  $mission
 * @return \Illuminate\Http\Response
 */
public function destroy(Mission $mission)
{
    // Check if authorized
    $this->authorize('delete', $mission);
    
    // Delete any uploaded documents
    if ($mission->additional_documents) {
        Storage::disk('public')->delete($mission->additional_documents);
    }
    
    // Delete the mission
    $mission->delete();
    
    return redirect()->route('teacher.missions.index')
        ->with('success', 'La mission a été supprimée avec succès.');
}
    // Other methods like edit, update, etc.
    /**
     * Show the form for editing the specified mission.
     *
     * @param  \App\Models\Mission  $mission
     * @return \Illuminate\Http\Response
     */
    public function edit(Mission $mission)
    {
        // Authorization check
        $this->authorize('update', $mission);
        
        return view('teacher.missions.edit', compact('mission'));
    }

    /**
     * Update the specified mission in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Mission  $mission
     * @return \Illuminate\Http\Response
     */
   
public function update(Request $request, Mission $mission)
{
    // Authorization check
    $this->authorize('update', $mission);
    
    $request->validate([
        'missionType' => 'required|in:nationale,internationale',
        'transportType' => 'required|in:voiture,transport_public,train,avion',
        'startDate' => 'required|date',
        'endDate' => 'required|date|after_or_equal:startDate',
        'destinationCity' => 'required|string|max:255',
        'destinationInstitution' => 'required|string|max:255',
        'missionTitle' => 'required|string|max:255',
        'missionObjective' => 'required|string',
        'supervisorName' => 'nullable|string|max:255',
        'additionalDocuments.*' => 'nullable|file|mimes:pdf|max:10240',
    ]);
    
    $mission->type = $request->missionType;
    $mission->transport_type = $request->transportType;
    $mission->start_date = $request->startDate;
    $mission->end_date = $request->endDate;
    $mission->destination_city = $request->destinationCity;
    $mission->destination_institution = $request->destinationInstitution;
    $mission->title = $request->missionTitle;
    $mission->objective = $request->missionObjective;
    $mission->supervisor_name = $request->supervisorName;
    
    $mission->save();
    
    // Handle file uploads if present
    if ($request->hasFile('additionalDocuments')) {
        foreach ($request->file('additionalDocuments') as $file) {
            $path = $file->store('mission_documents', 'public');
            
            // Create a document record linked to this mission
            $document = new MissionDocument();
            $document->mission_id = $mission->id;
            $document->file_path = $path;
            $document->file_name = $file->getClientOriginalName();
            $document->file_type = $file->getClientMimeType();
          //  $document->file_size = $file->getSize();
            $document->save();
        }
    }
    
    return redirect()->route('teacher.missions.show', $mission->id)
        ->with('success', 'Mission mise à jour avec succès.');
}
    /**
     * Generate a printable version of the mission.
     *
     * @param  \App\Models\Mission  $mission
     * @return \Illuminate\Http\Response
     */
    public function print(Mission $mission)
    {
        // Check if user is authorized to view this mission
        $this->authorize('view', $mission);
        
        // Format the dates
        $mission->formatted_start_date = Carbon::parse($mission->start_date)->format('d/m/Y');
        $mission->formatted_end_date = Carbon::parse($mission->end_date)->format('d/m/Y');
        $mission->duration = Carbon::parse($mission->start_date)->diffInDays(Carbon::parse($mission->end_date)) + 1;
        
        // Get the user (teacher) information
        $user = Auth::user();
        
        // Get today's date for the document
        $today = Carbon::now()->format('d/m/Y');
        
        // Generate a reference number if needed
        $reference = 'MIS-' . date('Y', strtotime($mission->created_at)) . '-' . sprintf('%03d', $mission->id);
        
        return view('teacher.missions.print', compact('mission', 'user', 'today', 'reference'));
    }
}
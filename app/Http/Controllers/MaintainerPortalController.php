<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Property;
use App\Models\MaintenanceRequest;

class MaintainerPortalController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:maintainer']);
    }

    /**
     * Display the maintainer dashboard
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get assigned maintenance requests
        $assignedRequests = MaintenanceRequest::where('assigned_to', $user->id)
            ->with(['property', 'requestedBy'])
            ->latest()
            ->get();
        
        // Get all pending requests
        $pendingRequests = MaintenanceRequest::where('status', 'pending')
            ->with(['property', 'requestedBy'])
            ->latest()
            ->get();
        
        // Calculate statistics
        $stats = [
            'assigned_requests' => $assignedRequests->count(),
            'pending_requests' => $pendingRequests->count(),
            'completed_this_month' => MaintenanceRequest::where('assigned_to', $user->id)
                ->where('status', 'completed')
                ->whereMonth('completed_at', now()->month)
                ->whereYear('completed_at', now()->year)
                ->count(),
            'urgent_requests' => $assignedRequests->where('priority', 'urgent')->count(),
        ];

        return view('portals.maintainer.dashboard', compact('assignedRequests', 'pendingRequests', 'stats'));
    }

    /**
     * Display all maintenance requests
     */
    public function requests()
    {
        $user = auth()->user();
        
        $requests = MaintenanceRequest::with(['property', 'requestedBy'])
            ->latest()
            ->paginate(20);

        return view('portals.maintainer.requests', compact('requests'));
    }

    /**
     * Display assigned maintenance requests
     */
    public function assigned()
    {
        $user = auth()->user();
        
        $requests = MaintenanceRequest::where('assigned_to', $user->id)
            ->with(['property', 'requestedBy'])
            ->latest()
            ->paginate(20);

        return view('portals.maintainer.assigned', compact('requests'));
    }

    /**
     * Display maintenance request details
     */
    public function show(MaintenanceRequest $request)
    {
        $request->load(['property', 'requestedBy', 'assignedTo']);
        
        return view('portals.maintainer.show', compact('request'));
    }

    /**
     * Update maintenance request status
     */
    public function updateStatus(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'notes' => 'nullable|string|max:1000',
        ]);

        $maintenanceRequest->update([
            'status' => $request->status,
            'notes' => $request->notes,
            'completed_at' => $request->status === 'completed' ? now() : null,
        ]);

        return redirect()->back()
            ->with('success', 'Maintenance request status updated successfully.');
    }

    /**
     * Assign maintenance request to self
     */
    public function assignToSelf(MaintenanceRequest $maintenanceRequest)
    {
        $maintenanceRequest->update([
            'assigned_to' => auth()->id(),
            'status' => 'in_progress',
        ]);

        return redirect()->back()
            ->with('success', 'Maintenance request assigned to you successfully.');
    }

    /**
     * Display maintainer's schedule
     */
    public function schedule()
    {
        $user = auth()->user();
        
        $upcomingRequests = MaintenanceRequest::where('assigned_to', $user->id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->with(['property'])
            ->orderBy('scheduled_date')
            ->get();

        return view('portals.maintainer.schedule', compact('upcomingRequests'));
    }

    /**
     * Display maintainer's profile
     */
    public function profile()
    {
        $user = auth()->user();
        return view('portals.maintainer.profile', compact('user'));
    }

    /**
     * Update maintainer's profile
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'specialization' => 'nullable|string|max:255',
        ]);

        $user->update($request->only(['name', 'phone', 'specialization']));

        return redirect()->route('maintainer.profile')
            ->with('success', 'Profile updated successfully.');
    }
}

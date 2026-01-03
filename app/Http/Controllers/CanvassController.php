<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Canvass;
use App\Models\RFQ;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CanvassController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Canvass::with(['rfq.purchaseRequest', 'canvasser']);

        // Filter based on user role
        if ($user->hasRole('CANVASSER')) {
            $query->where('canvasser_id', $user->id);
        }

        // Search filter
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('task_description', 'like', '%' . $request->search . '%')
                  ->orWhereHas('rfq', function($rfq) use ($request) {
                      $rfq->where('rfq_number', 'like', '%' . $request->search . '%')
                         ->orWhereHas('purchaseRequest', function($pr) use ($request) {
                             $pr->where('pr_number', 'like', '%' . $request->search . '%')
                                ->orWhere('project_title', 'like', '%' . $request->search . '%');
                         });
                  });
            });
        }

        // Status filter
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $canvasses = $query->latest()->paginate(15);

        // Check for overdue canvasses
        foreach ($canvasses as $canvass) {
            if ($canvass->isOverdue() && $canvass->status !== 'OVERDUE') {
                $canvass->update(['status' => 'OVERDUE']);
            }
        }

        return view('canvasses.index', compact('canvasses'));
    }

    public function create(RFQ $rfq)
    {
        if (!Auth::user()->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }

        // Get all canvassers
        $canvassers = User::where('role', 'CANVASSER')->get();

        $rfq->load('purchaseRequest');
        return view('canvasses.create', compact('rfq', 'canvassers'));
    }

    public function store(Request $request, RFQ $rfq)
    {
        if (!Auth::user()->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'canvasser_id' => 'required|exists:users,id',
            'task_description' => 'required|string',
            'deadline' => 'required|date|after:today',
            'notes' => 'nullable|string',
        ]);

        // Verify user is a canvasser
        $canvasser = User::findOrFail($validated['canvasser_id']);
        if ($canvasser->role !== 'CANVASSER') {
            return back()->withInput()->withErrors(['canvasser_id' => 'Selected user must be a canvasser.']);
        }

        DB::beginTransaction();
        try {
            $canvass = Canvass::create([
                'rfq_id' => $rfq->id,
                'canvasser_id' => $validated['canvasser_id'],
                'task_description' => $validated['task_description'],
                'deadline' => $validated['deadline'],
                'status' => 'PENDING',
                'notes' => $validated['notes'] ?? null,
            ]);

            activity_log('CREATED', 'CANVASS', $canvass->id, [
                'rfq_number' => $rfq->rfq_number,
                'canvasser' => $canvasser->name,
            ]);

            DB::commit();

            return redirect()->route('canvasses.show', $canvass)
                ->with('success', 'Canvass task assigned successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to assign canvass task: ' . $e->getMessage()]);
        }
    }

    public function show(Canvass $canvass)
    {
        $user = Auth::user();
        
        // Canvassers can only view their own canvasses
        if ($user->hasRole('CANVASSER') && $canvass->canvasser_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $canvass->load([
            'rfq.purchaseRequest.endUser',
            'rfq.purchaseRequest.prItems',
            'rfq.procurementOfficer',
            'canvasser',
            'supplierQuotations.quotationItems'
        ]);

        return view('canvasses.show', compact('canvass'));
    }

    public function edit(Canvass $canvass)
    {
        $user = Auth::user();
        
        // Only canvassers can edit their own canvasses, or procurement officers/admins
        if ($user->hasRole('CANVASSER') && $canvass->canvasser_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        
        if (!Auth::user()->hasAnyRole(['CANVASSER', 'PROCUREMENT_OFFICER', 'ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }

        $canvass->load('rfq.purchaseRequest');
        return view('canvasses.edit', compact('canvass'));
    }

    public function update(Request $request, Canvass $canvass)
    {
        $user = Auth::user();
        
        if ($user->hasRole('CANVASSER') && $canvass->canvasser_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        
        if (!Auth::user()->hasAnyRole(['CANVASSER', 'PROCUREMENT_OFFICER', 'ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'task_description' => 'required|string',
            'deadline' => 'required|date',
            'status' => 'required|in:PENDING,IN_PROGRESS,COMPLETED,OVERDUE',
            'notes' => 'nullable|string',
        ]);

        $oldStatus = $canvass->status;
        $canvass->update($validated);

        // If status changed to COMPLETED, check if we should update RFQ/PR status
        if ($validated['status'] === 'COMPLETED' && $oldStatus !== 'COMPLETED') {
            // Check if all canvasses for this RFQ are completed
            $allCompleted = $canvass->rfq->canvasses()
                ->where('status', '!=', 'COMPLETED')
                ->count() === 0;

            if ($allCompleted && $canvass->rfq->purchaseRequest->status === 'RFQ_DISSEMINATED') {
                $canvass->rfq->purchaseRequest->update(['status' => 'CANVASS_COMPLETE']);
            }
        }

        activity_log('UPDATED', 'CANVASS', $canvass->id, [
            'old_status' => $oldStatus,
            'new_status' => $validated['status'],
        ]);

        return redirect()->route('canvasses.show', $canvass)
            ->with('success', 'Canvass updated successfully.');
    }

    public function updateStatus(Request $request, Canvass $canvass)
    {
        $user = Auth::user();
        
        if ($user->hasRole('CANVASSER') && $canvass->canvasser_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'status' => 'required|in:PENDING,IN_PROGRESS,COMPLETED,OVERDUE',
        ]);

        $oldStatus = $canvass->status;
        $canvass->update(['status' => $validated['status']]);

        // If status changed to COMPLETED, check if we should update RFQ/PR status
        if ($validated['status'] === 'COMPLETED' && $oldStatus !== 'COMPLETED') {
            // Check if all canvasses for this RFQ are completed
            $allCompleted = $canvass->rfq->canvasses()
                ->where('status', '!=', 'COMPLETED')
                ->count() === 0;

            if ($allCompleted && $canvass->rfq->purchaseRequest->status === 'RFQ_DISSEMINATED') {
                $canvass->rfq->purchaseRequest->update(['status' => 'CANVASS_COMPLETE']);
            }
        }

        activity_log('STATUS_UPDATED', 'CANVASS', $canvass->id, [
            'old_status' => $oldStatus,
            'new_status' => $validated['status'],
        ]);

        return back()->with('success', 'Canvass status updated successfully.');
    }
}

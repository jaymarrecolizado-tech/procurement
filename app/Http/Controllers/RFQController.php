<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Models\RFQ;
use App\Models\ApprovalRouting;
use App\Models\User;
use App\Notifications\ApprovalRequired;
use App\Notifications\ItemApproved;
use App\Notifications\ItemRejected;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RFQController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get RFQs with their purchase requests
        $query = RFQ::with(['purchaseRequest.endUser', 'procurementOfficer']);

        // Filter based on user role
        if ($user->hasRole('PROCUREMENT_OFFICER')) {
            $query->where('procurement_officer_id', $user->id);
        }

        // Search filter
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('rfq_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('purchaseRequest', function($pr) use ($request) {
                      $pr->where('pr_number', 'like', '%' . $request->search . '%')
                         ->orWhere('project_title', 'like', '%' . $request->search . '%');
                  });
            });
        }

        // Status filter
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $rfqs = $query->latest()->paginate(15);

        // Also get PRs ready for RFQ creation (limit to 20 for performance)
        $prsReadyForRfq = PurchaseRequest::with('endUser')
            ->where('status', 'RFQ_READY')
            ->whereDoesntHave('rfq')
            ->latest()
            ->limit(20)
            ->get();

        return view('rfqs.index', compact('rfqs', 'prsReadyForRfq'));
    }

    public function create(PurchaseRequest $purchaseRequest)
    {
        if (!Auth::user()->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }

        // Check if PR is ready for RFQ
        if ($purchaseRequest->status !== 'RFQ_READY' && $purchaseRequest->status !== 'PR_UNDER_REVIEW') {
            return redirect()->route('rfqs.index')
                ->with('error', 'Purchase Request is not ready for RFQ creation.');
        }

        // Check if RFQ already exists
        if ($purchaseRequest->rfq) {
            return redirect()->route('rfqs.show', $purchaseRequest->rfq)
                ->with('info', 'RFQ already exists for this Purchase Request.');
        }

        $purchaseRequest->load('prItems');
        return view('rfqs.create', compact('purchaseRequest'));
    }

    public function store(Request $request, PurchaseRequest $purchaseRequest)
    {
        if (!Auth::user()->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }

        // Check if RFQ already exists
        if ($purchaseRequest->rfq) {
            return redirect()->route('rfqs.show', $purchaseRequest->rfq)
                ->with('error', 'RFQ already exists for this Purchase Request.');
        }

        $validated = $request->validate([
            'delivery_schedule' => 'nullable|string',
            'payment_terms' => 'nullable|string',
            'canvassing_deadline' => 'required|date|after:today',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $rfq = RFQ::create([
                'rfq_number' => RFQ::generateRfqNumber(),
                'purchase_request_id' => $purchaseRequest->id,
                'procurement_officer_id' => Auth::id(),
                'delivery_schedule' => $validated['delivery_schedule'] ?? null,
                'payment_terms' => $validated['payment_terms'] ?? null,
                'canvassing_deadline' => $validated['canvassing_deadline'],
                'status' => 'PENDING',
                'notes' => $validated['notes'] ?? null,
            ]);

            // Update PR status to RFQ_READY if it was PR_UNDER_REVIEW
            if ($purchaseRequest->status === 'PR_UNDER_REVIEW') {
                $purchaseRequest->update(['status' => 'RFQ_READY']);
            }

            activity_log('CREATED', 'RFQ', $rfq->id, [
                'rfq_number' => $rfq->rfq_number,
                'pr_number' => $purchaseRequest->pr_number,
            ]);

            DB::commit();

            return redirect()->route('rfqs.show', $rfq)
                ->with('success', 'RFQ created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to create RFQ: ' . $e->getMessage()]);
        }
    }

    public function show(RFQ $rfq)
    {
        $rfq->loadMissing(['purchaseRequest.endUser', 'purchaseRequest.prItems', 'procurementOfficer', 'canvasses.canvasser', 'approvalRoutings.approver', 'documents.uploader']);
        
        return view('rfqs.show', compact('rfq'));
    }

    public function edit(RFQ $rfq)
    {
        if (!Auth::user()->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }

        if ($rfq->procurement_officer_id !== Auth::id() && !Auth::user()->hasRole('ADMIN')) {
            abort(403, 'Unauthorized action.');
        }

        $rfq->load('purchaseRequest.prItems');
        return view('rfqs.edit', compact('rfq'));
    }

    public function update(Request $request, RFQ $rfq)
    {
        if (!Auth::user()->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }

        if ($rfq->procurement_officer_id !== Auth::id() && !Auth::user()->hasRole('ADMIN')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'delivery_schedule' => 'nullable|string',
            'payment_terms' => 'nullable|string',
            'canvassing_deadline' => 'required|date',
            'status' => 'required|in:PENDING,ACTIVE,COMPLETED',
            'notes' => 'nullable|string',
        ]);

        $rfq->update($validated);

        activity_log('UPDATED', 'RFQ', $rfq->id, [
            'rfq_number' => $rfq->rfq_number,
        ]);

        return redirect()->route('rfqs.show', $rfq)
            ->with('success', 'RFQ updated successfully.');
    }

    public function updateStatus(Request $request, RFQ $rfq)
    {
        if (!Auth::user()->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'status' => 'required|in:PENDING,ACTIVE,COMPLETED',
        ]);

        $oldStatus = $rfq->status;
        $rfq->update(['status' => $validated['status']]);

        // Update PR status when RFQ becomes active
        if ($validated['status'] === 'ACTIVE' && $rfq->purchaseRequest->status === 'RFQ_READY') {
            $rfq->purchaseRequest->update(['status' => 'RFQ_DISSEMINATED']);
        }

        activity_log('STATUS_UPDATED', 'RFQ', $rfq->id, [
            'old_status' => $oldStatus,
            'new_status' => $validated['status'],
        ]);

        return back()->with('success', 'RFQ status updated successfully.');
    }
}


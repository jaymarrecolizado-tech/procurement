<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PurchaseRequest;
use App\Models\RFQ;
use App\Models\BacDocument;
use App\Models\ApprovalRouting;

class ApprovalDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get pending approvals for Purchase Requests
        $prPendingApprovals = ApprovalRouting::where('approver_id', $user->id)
            ->where('status', 'PENDING')
            ->where('document_type', 'PR')
            ->with(['purchaseRequest.endUser', 'purchaseRequest.approvalRoutings.approver'])
            ->get()
            ->map(function($routing) {
                $pr = $routing->purchaseRequest;
                
                // Check if previous approvals are complete
                $previousComplete = $pr->approvalRoutings
                    ->where('sequence', '<', $routing->sequence)
                    ->where('status', 'APPROVED')
                    ->count();
                
                $previousTotal = $pr->approvalRoutings
                    ->where('sequence', '<', $routing->sequence)
                    ->count();
                
                return [
                    'type' => 'PR',
                    'id' => $pr->id,
                    'document_type' => 'Purchase Request',
                    'pr_number' => $pr->pr_number,
                    'project_title' => $pr->project_title,
                    'sequence' => $routing->sequence,
                    'can_approve' => $previousComplete === $previousTotal,
                    'pending_since' => $routing->created_at,
                    'routing' => $routing,
                    'url' => route('purchase-requests.show', $pr),
                ];
            });

        // Get pending approvals for RFQs
        $rfqPendingApprovals = ApprovalRouting::where('approver_id', $user->id)
            ->where('status', 'PENDING')
            ->where('document_type', 'RFQ')
            ->with(['rfq.purchaseRequest.endUser', 'rfq.approvalRoutings.approver'])
            ->get()
            ->map(function($routing) {
                $rfq = $routing->rfq;
                $pr = $rfq->purchaseRequest;
                
                // Check if previous approvals are complete
                $previousComplete = $rfq->approvalRoutings
                    ->where('sequence', '<', $routing->sequence)
                    ->where('status', 'APPROVED')
                    ->count();
                
                $previousTotal = $rfq->approvalRoutings
                    ->where('sequence', '<', $routing->sequence)
                    ->count();
                
                return [
                    'type' => 'RFQ',
                    'id' => $rfq->id,
                    'document_type' => 'Request for Quotation',
                    'pr_number' => $pr->pr_number,
                    'project_title' => $pr->project_title,
                    'sequence' => $routing->sequence,
                    'can_approve' => $previousComplete === $previousTotal,
                    'pending_since' => $routing->created_at,
                    'routing' => $routing,
                    'url' => route('rfqs.show', $rfq),
                ];
            });

        // Get pending approvals for BAC documents
        $bacPendingApprovals = ApprovalRouting::where('approver_id', $user->id)
            ->where('status', 'PENDING')
            ->where('document_type', 'BAC')
            ->with(['bacDocument.purchaseRequest.endUser', 'bacDocument.approvalRoutings.approver'])
            ->get()
            ->map(function($routing) {
                $bacDocument = $routing->bacDocument;
                $pr = $bacDocument->purchaseRequest;
                
                // Check if previous approvals are complete
                $previousComplete = $bacDocument->approvalRoutings
                    ->where('sequence', '<', $routing->sequence)
                    ->where('status', 'APPROVED')
                    ->count();
                
                $previousTotal = $bacDocument->approvalRoutings
                    ->where('sequence', '<', $routing->sequence)
                    ->count();
                
                return [
                    'type' => 'BAC',
                    'id' => $bacDocument->id,
                    'document_type' => $bacDocument->document_type_name,
                    'pr_number' => $pr->pr_number,
                    'project_title' => $pr->project_title,
                    'sequence' => $routing->sequence,
                    'can_approve' => $previousComplete === $previousTotal,
                    'pending_since' => $routing->created_at,
                    'routing' => $routing,
                    'url' => route('bac-documents.show', $bacDocument),
                ];
            });

        // Combine all pending approvals
        $allPendingApprovals = $prPendingApprovals->concat($rfqPendingApprovals)->concat($bacPendingApprovals);

        // Filter by document type if requested
        $documentType = $request->get('type', 'all');
        if ($documentType !== 'all') {
            $allPendingApprovals = $allPendingApprovals->filter(function($item) use ($documentType) {
                return $item['type'] === $documentType;
            });
        }

        // Sort by pending since (oldest first)
        $allPendingApprovals = $allPendingApprovals->sortBy('pending_since');

        // Get approval history (completed approvals)
        $approvalHistory = ApprovalRouting::where('approver_id', $user->id)
            ->whereIn('status', ['APPROVED', 'REJECTED'])
            ->with(['bacDocument.purchaseRequest.endUser', 'purchaseRequest.endUser', 'rfq.purchaseRequest.endUser'])
            ->latest('signed_at')
            ->limit(20)
            ->get();

        // Get statistics (optimized: single query instead of multiple)
        $approvalStats = ApprovalRouting::where('approver_id', $user->id)
            ->selectRaw('
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as total_approved,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as total_rejected
            ', ['APPROVED', 'REJECTED'])
            ->first();
        
        $stats = [
            'total_pending' => $allPendingApprovals->count(),
            'can_approve_now' => $allPendingApprovals->where('can_approve', true)->count(),
            'waiting_for_others' => $allPendingApprovals->where('can_approve', false)->count(),
            'total_approved' => $approvalStats->total_approved ?? 0,
            'total_rejected' => $approvalStats->total_rejected ?? 0,
        ];

        return view('approvals.dashboard', compact('allPendingApprovals', 'approvalHistory', 'stats', 'documentType'));
    }
}


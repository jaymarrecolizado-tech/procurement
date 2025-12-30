<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PurchaseRequest;
use App\Models\RFQ;
use App\Models\Canvass;
use App\Models\PurchaseOrder;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get statistics based on user role
        $stats = $this->getStatistics($user);
        
        // Get recent purchase requests
        $recentPRs = PurchaseRequest::with(['endUser'])
            ->latest()
            ->limit(10)
            ->get();
        
        // Get pending tasks based on user role
        $pendingTasks = $this->getPendingTasks($user);
        
        // Get workflow visualization data
        $workflowSteps = $this->getWorkflowSteps();
        
        return view('dashboard.index', compact(
            'stats', 
            'recentPRs', 
            'pendingTasks', 
            'workflowSteps'
        ));
    }
    
    private function getStatistics($user)
    {
        switch ($user->role) {
            case 'END_USER':
                return [
                    'total_prs' => PurchaseRequest::where('end_user_id', $user->id)->count(),
                    'pending_review' => PurchaseRequest::where('end_user_id', $user->id)
                        ->where('status', 'PR_UNDER_REVIEW')->count(),
                    'in_progress' => PurchaseRequest::where('end_user_id', $user->id)
                        ->whereIn('status', ['RFQ_READY', 'RFQ_DISSEMINATED', 'CANVASS_COMPLETE', 'BAC_DOCS_READY', 'BAC_APPROVED', 'PO_APPROVED'])
                        ->count(),
                    'completed' => PurchaseRequest::where('end_user_id', $user->id)
                        ->whereIn('status', ['PO_COMPLETE', 'COA_STAMPED'])->count(),
                ];
                
            case 'PROCUREMENT_OFFICER':
                return [
                    'total_prs' => PurchaseRequest::count(),
                    'pending_review' => PurchaseRequest::where('status', 'PR_UNDER_REVIEW')->count(),
                    'active_rfqs' => RFQ::where('status', 'ACTIVE')->count(),
                    'completed' => PurchaseOrder::where('status', 'COMPLETE')->count(),
                ];
                
            case 'CANVASSER':
                return [
                    'total_tasks' => Canvass::where('canvasser_id', $user->id)->count(),
                    'pending_tasks' => Canvass::where('canvasser_id', $user->id)
                        ->where('status', 'PENDING')->count(),
                    'in_progress' => Canvass::where('canvasser_id', $user->id)
                        ->where('status', 'IN_PROGRESS')->count(),
                    'completed' => Canvass::where('canvasser_id', $user->id)
                        ->where('status', 'COMPLETED')->count(),
                ];
                
            case 'BAC_SECRETARIAT':
            case 'BAC_CHAIR':
            case 'BAC_MEMBER':
                return [
                    'total_prs' => PurchaseRequest::where('status', 'BAC_DOCS_READY')->count(),
                    'pending_approval' => PurchaseRequest::where('status', 'BAC_DOCS_READY')->count(),
                    'approved' => PurchaseRequest::where('status', 'BAC_APPROVED')->count(),
                    'rejected' => PurchaseRequest::where('status', 'BAC_DOCS_READY')->count(),
                ];
                
            default:
                return [
                    'total_prs' => PurchaseRequest::count(),
                    'pending_review' => PurchaseRequest::where('status', 'PR_UNDER_REVIEW')->count(),
                    'in_progress' => PurchaseRequest::whereIn('status', ['RFQ_READY', 'RFQ_DISSEMINATED'])->count(),
                    'completed' => PurchaseRequest::where('status', 'COA_STAMPED')->count(),
                ];
        }
    }
    
    private function getPendingTasks($user)
    {
        switch ($user->role) {
            case 'CANVASSER':
                return Canvass::where('canvasser_id', $user->id)
                    ->whereIn('status', ['PENDING', 'IN_PROGRESS'])
                    ->with('rfq.purchaseRequest')
                    ->limit(5)
                    ->get();
                
            case 'BAC_CHAIR':
            case 'BAC_MEMBER':
                return PurchaseRequest::where('status', 'BAC_DOCS_READY')
                    ->with('endUser')
                    ->limit(5)
                    ->get();
                
            case 'PROCUREMENT_OFFICER':
                return PurchaseRequest::where('status', 'PR_UNDER_REVIEW')
                    ->with('endUser')
                    ->limit(5)
                    ->get();
                
            default:
                return collect();
        }
    }
    
    private function getWorkflowSteps()
    {
        return [
            [
                'step' => 1,
                'name' => 'Purchase Request',
                'description' => 'Create and submit PR',
                'icon' => 'document-text',
                'color' => 'blue'
            ],
            [
                'step' => 2,
                'name' => 'RFQ',
                'description' => 'Request for Quotation',
                'icon' => 'clipboard-document-list',
                'color' => 'yellow'
            ],
            [
                'step' => 3,
                'name' => 'Canvass',
                'description' => 'Get supplier quotes',
                'icon' => 'magnifying-glass',
                'color' => 'orange'
            ],
            [
                'step' => 4,
                'name' => 'BAC Documents',
                'description' => 'Prepare BAC documents',
                'icon' => 'document-duplicate',
                'color' => 'purple'
            ],
            [
                'step' => 5,
                'name' => 'Purchase Order',
                'description' => 'Generate PO',
                'icon' => 'receipt-percent',
                'color' => 'green'
            ],
            [
                'step' => 6,
                'name' => 'COA',
                'description' => 'COA Stamping',
                'icon' => 'check-circle',
                'color' => 'indigo'
            ]
        ];
    }
}
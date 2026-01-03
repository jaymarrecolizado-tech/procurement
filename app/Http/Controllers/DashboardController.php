<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PurchaseRequest;
use App\Models\ApprovalRouting;

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
        // Cache statistics for 5 minutes to improve performance
        $cacheKey = 'dashboard_stats_' . $user->id . '_' . $user->role;
        
        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, function() use ($user) {
            switch ($user->role) {
                case 'END_USER':
                    // Optimize: Use single query with conditional aggregation
                    $prStats = PurchaseRequest::where('end_user_id', $user->id)
                        ->selectRaw('
                            COUNT(*) as total_prs,
                            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending_review,
                            SUM(CASE WHEN status IN (?, ?, ?, ?, ?, ?) THEN 1 ELSE 0 END) as in_progress,
                            SUM(CASE WHEN status IN (?, ?) THEN 1 ELSE 0 END) as completed
                        ', [
                            'PR_UNDER_REVIEW',
                            'RFQ_READY', 'RFQ_DISSEMINATED', 'CANVASS_COMPLETE', 'BAC_DOCS_READY', 'BAC_APPROVED', 'PO_APPROVED',
                            'PO_COMPLETE', 'COA_STAMPED'
                        ])
                        ->first();
                    
                    return [
                        'total_prs' => $prStats->total_prs ?? 0,
                        'pending_review' => $prStats->pending_review ?? 0,
                        'in_progress' => $prStats->in_progress ?? 0,
                        'completed' => $prStats->completed ?? 0,
                    ];
                    
                case 'PROCUREMENT_OFFICER':
                    // Optimize: Combine queries where possible
                    $prCounts = PurchaseRequest::selectRaw('
                        COUNT(*) as total_prs,
                        SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending_review
                    ', ['PR_UNDER_REVIEW'])->first();
                    
                    return [
                        'total_prs' => $prCounts->total_prs ?? 0,
                        'pending_review' => $prCounts->pending_review ?? 0,
                        'active_rfqs' => class_exists('App\Models\RFQ') ? \App\Models\RFQ::where('status', 'ACTIVE')->count() : 0,
                        'completed' => class_exists('App\Models\PurchaseOrder') ? \App\Models\PurchaseOrder::where('status', 'COMPLETE')->count() : 0,
                    ];
                    
                case 'CANVASSER':
                    if (class_exists('App\Models\Canvass')) {
                        // Optimize: Single query with conditional aggregation
                        $canvassStats = \App\Models\Canvass::where('canvasser_id', $user->id)
                            ->selectRaw('
                                COUNT(*) as total_tasks,
                                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending_tasks,
                                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as in_progress,
                                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as completed
                            ', ['PENDING', 'IN_PROGRESS', 'COMPLETED'])
                            ->first();
                        
                        return [
                            'total_tasks' => $canvassStats->total_tasks ?? 0,
                            'pending_tasks' => $canvassStats->pending_tasks ?? 0,
                            'in_progress' => $canvassStats->in_progress ?? 0,
                            'completed' => $canvassStats->completed ?? 0,
                        ];
                    }
                    return [
                        'total_tasks' => 0,
                        'pending_tasks' => 0,
                        'in_progress' => 0,
                        'completed' => 0,
                    ];
                    
                case 'BAC_SECRETARIAT':
                case 'BAC_CHAIR':
                case 'BAC_MEMBER':
                    // Optimize: Combine approval routing queries
                    $approvalStats = ApprovalRouting::where('approver_id', $user->id)
                        ->selectRaw('
                            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending_approval,
                            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as rejected
                        ', ['PENDING', 'REJECTED'])
                        ->first();
                    
                    return [
                        'total_prs' => PurchaseRequest::where('status', 'BAC_DOCS_READY')->count(),
                        'pending_approval' => $approvalStats->pending_approval ?? 0,
                        'approved' => PurchaseRequest::where('status', 'BAC_APPROVED')->count(),
                        'rejected' => $approvalStats->rejected ?? 0,
                    ];
                    
                default:
                    // Optimize: Single query with conditional aggregation
                    $prStats = PurchaseRequest::selectRaw('
                        COUNT(*) as total_prs,
                        SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending_review,
                        SUM(CASE WHEN status IN (?, ?) THEN 1 ELSE 0 END) as in_progress,
                        SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as completed
                    ', ['PR_UNDER_REVIEW', 'RFQ_READY', 'RFQ_DISSEMINATED', 'COA_STAMPED'])
                        ->first();
                    
                    return [
                        'total_prs' => $prStats->total_prs ?? 0,
                        'pending_review' => $prStats->pending_review ?? 0,
                        'in_progress' => $prStats->in_progress ?? 0,
                        'completed' => $prStats->completed ?? 0,
                    ];
            }
        });
    }
    
    private function getPendingTasks($user)
    {
        switch ($user->role) {
            case 'CANVASSER':
                if (class_exists('App\Models\Canvass')) {
                    return \App\Models\Canvass::where('canvasser_id', $user->id)
                        ->whereIn('status', ['PENDING', 'IN_PROGRESS'])
                        ->with('rfq.purchaseRequest')
                        ->limit(5)
                        ->get();
                }
                return collect();
                
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
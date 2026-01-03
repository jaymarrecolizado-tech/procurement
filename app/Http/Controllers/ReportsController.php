<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Models\RFQ;
use App\Models\BacDocument;
use App\Models\PurchaseOrder;
use App\Models\ApprovalRouting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReportsController extends Controller
{
    /**
     * Display reports dashboard
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Date range filter
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        // Cache reports data for 15 minutes to improve performance
        $cacheKey = 'reports_data_' . md5($startDate . $endDate);
        
        $data = \Illuminate\Support\Facades\Cache::remember($cacheKey, 900, function() use ($startDate, $endDate) {
            $dateEnd = $endDate . ' 23:59:59';
            
            // Optimize: Combine PR statistics in single query
            $prStats = PurchaseRequest::whereBetween('created_at', [$startDate, $dateEnd])
                ->selectRaw('
                    COUNT(*) as total_prs,
                    SUM(estimated_budget) as total_budget
                ')
                ->first();
            
            // Overall Statistics (optimized)
            $stats = [
                'total_prs' => $prStats->total_prs ?? 0,
                'total_rfqs' => RFQ::whereBetween('created_at', [$startDate, $dateEnd])->count(),
                'total_bac_docs' => BacDocument::whereBetween('created_at', [$startDate, $dateEnd])->count(),
                'total_pos' => PurchaseOrder::whereBetween('created_at', [$startDate, $dateEnd])->count(),
                'total_budget' => $prStats->total_budget ?? 0,
                'total_contract_amount' => PurchaseOrder::whereBetween('created_at', [$startDate, $dateEnd])->sum('contract_amount'),
            ];

            // PR Status Distribution
            $prStatusDistribution = PurchaseRequest::whereBetween('created_at', [$startDate, $dateEnd])
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status');

            // RFQ Status Distribution
            $rfqStatusDistribution = RFQ::whereBetween('created_at', [$startDate, $dateEnd])
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status');

            // PO Status Distribution
            $poStatusDistribution = PurchaseOrder::whereBetween('created_at', [$startDate, $dateEnd])
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status');

            // Approval Time Statistics
            $approvalTimes = ApprovalRouting::whereBetween('created_at', [$startDate, $dateEnd])
                ->where('status', 'APPROVED')
                ->whereNotNull('time_spent_hours')
                ->select(DB::raw('AVG(time_spent_hours) as avg_time'), DB::raw('MAX(time_spent_hours) as max_time'), DB::raw('MIN(time_spent_hours) as min_time'))
                ->first();

            // Top Departments by PR Count
            $topDepartments = PurchaseRequest::whereBetween('created_at', [$startDate, $dateEnd])
                ->select('end_user_department', DB::raw('count(*) as count'), DB::raw('sum(estimated_budget) as total_budget'))
                ->groupBy('end_user_department')
                ->orderByDesc('count')
                ->limit(10)
                ->get();

            // Top Suppliers by PO Count
            $topSuppliers = PurchaseOrder::whereBetween('created_at', [$startDate, $dateEnd])
                ->select('supplier_name', DB::raw('count(*) as count'), DB::raw('sum(contract_amount) as total_amount'))
                ->groupBy('supplier_name')
                ->orderByDesc('count')
                ->limit(10)
                ->get();

            // Monthly Trends (Last 12 months)
            $monthlyTrends = PurchaseRequest::select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                    DB::raw('count(*) as pr_count'),
                    DB::raw('sum(estimated_budget) as total_budget')
                )
                ->where('created_at', '>=', now()->subMonths(12))
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            // Approval Statistics by Approver
            $approverStats = ApprovalRouting::whereBetween('created_at', [$startDate, $dateEnd])
                ->where('status', 'APPROVED')
                ->with('approver')
                ->select('approver_id', DB::raw('count(*) as approval_count'), DB::raw('AVG(time_spent_hours) as avg_time'))
                ->groupBy('approver_id')
                ->orderByDesc('approval_count')
                ->limit(10)
                ->get();

            return compact(
                'stats',
                'prStatusDistribution',
                'rfqStatusDistribution',
                'poStatusDistribution',
                'approvalTimes',
                'topDepartments',
                'topSuppliers',
                'monthlyTrends',
                'approverStats'
            );
        });

        return view('reports.index', array_merge($data, [
            'startDate' => $startDate,
            'endDate' => $endDate
        ]));
    }
}

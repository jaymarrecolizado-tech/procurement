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

        // Overall Statistics
        $stats = [
            'total_prs' => PurchaseRequest::whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])->count(),
            'total_rfqs' => RFQ::whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])->count(),
            'total_bac_docs' => BacDocument::whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])->count(),
            'total_pos' => PurchaseOrder::whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])->count(),
            'total_budget' => PurchaseRequest::whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])->sum('estimated_budget'),
            'total_contract_amount' => PurchaseOrder::whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])->sum('contract_amount'),
        ];

        // PR Status Distribution
        $prStatusDistribution = PurchaseRequest::whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // RFQ Status Distribution
        $rfqStatusDistribution = RFQ::whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // PO Status Distribution
        $poStatusDistribution = PurchaseOrder::whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Approval Time Statistics
        $approvalTimes = ApprovalRouting::whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
            ->where('status', 'APPROVED')
            ->whereNotNull('time_spent_hours')
            ->select(DB::raw('AVG(time_spent_hours) as avg_time'), DB::raw('MAX(time_spent_hours) as max_time'), DB::raw('MIN(time_spent_hours) as min_time'))
            ->first();

        // Top Departments by PR Count
        $topDepartments = PurchaseRequest::whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
            ->select('end_user_department', DB::raw('count(*) as count'), DB::raw('sum(estimated_budget) as total_budget'))
            ->groupBy('end_user_department')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Top Suppliers by PO Count
        $topSuppliers = PurchaseOrder::whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
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
        $approverStats = ApprovalRouting::whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
            ->where('status', 'APPROVED')
            ->with('approver')
            ->select('approver_id', DB::raw('count(*) as approval_count'), DB::raw('AVG(time_spent_hours) as avg_time'))
            ->groupBy('approver_id')
            ->orderByDesc('approval_count')
            ->limit(10)
            ->get();

        return view('reports.index', compact(
            'stats',
            'prStatusDistribution',
            'rfqStatusDistribution',
            'poStatusDistribution',
            'approvalTimes',
            'topDepartments',
            'topSuppliers',
            'monthlyTrends',
            'approverStats',
            'startDate',
            'endDate'
        ));
    }
}

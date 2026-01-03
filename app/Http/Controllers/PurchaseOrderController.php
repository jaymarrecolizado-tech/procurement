<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseRequest::with(['endUser'])
            ->whereIn('status', ['PO_APPROVED', 'AWAITING_CONFORME', 'PO_COMPLETE', 'COA_STAMPED']);

        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('pr_number', 'like', '%' . $request->search . '%')
                  ->orWhere('project_title', 'like', '%' . $request->search . '%');
            });
        }

        $purchaseRequests = $query->latest()->paginate(15);

        return view('purchase-orders.index', compact('purchaseRequests'));
    }
}


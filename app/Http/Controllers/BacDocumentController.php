<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use Illuminate\Support\Facades\Auth;

class BacDocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseRequest::with(['endUser'])
            ->whereIn('status', ['BAC_DOCS_READY', 'BAC_APPROVED', 'PO_APPROVED']);

        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('pr_number', 'like', '%' . $request->search . '%')
                  ->orWhere('project_title', 'like', '%' . $request->search . '%');
            });
        }

        $purchaseRequests = $query->latest()->paginate(15);

        return view('bac-documents.index', compact('purchaseRequests'));
    }
}


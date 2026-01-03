<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\PurchaseRequest;
use App\Models\RFQ;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class DocumentController extends Controller
{
    /**
     * Upload document for Purchase Request
     */
    public function uploadForPR(Request $request, PurchaseRequest $purchaseRequest)
    {
        $validated = $request->validate([
            'file' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
            'document_type' => 'required|in:PR,QUOTATION,AOQ,BAC_RESOLUTION,CONFORME,COA_PACKET',
        ]);

        DB::beginTransaction();
        try {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents/pr/' . $purchaseRequest->id, $fileName, 'public');

            $document = Document::create([
                'purchase_request_id' => $purchaseRequest->id,
                'document_type' => $validated['document_type'],
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'file_size' => $file->getSize(),
                'uploaded_by' => Auth::id(),
            ]);

            activity_log('UPLOADED', 'DOCUMENT', $document->id, [
                'document_type' => $validated['document_type'],
                'file_name' => $file->getClientOriginalName(),
                'related_to' => 'PR-' . $purchaseRequest->pr_number,
            ]);

            DB::commit();

            return back()->with('success', 'Document uploaded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to upload document: ' . $e->getMessage()]);
        }
    }

    /**
     * Upload document for RFQ
     */
    public function uploadForRFQ(Request $request, RFQ $rfq)
    {
        $validated = $request->validate([
            'file' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
            'document_type' => 'required|in:RFQ,QUOTATION,AOQ',
        ]);

        DB::beginTransaction();
        try {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents/rfq/' . $rfq->id, $fileName, 'public');

            $document = Document::create([
                'rfq_id' => $rfq->id,
                'document_type' => $validated['document_type'],
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'file_size' => $file->getSize(),
                'uploaded_by' => Auth::id(),
            ]);

            activity_log('UPLOADED', 'DOCUMENT', $document->id, [
                'document_type' => $validated['document_type'],
                'file_name' => $file->getClientOriginalName(),
                'related_to' => 'RFQ-' . $rfq->rfq_number,
            ]);

            DB::commit();

            return back()->with('success', 'Document uploaded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to upload document: ' . $e->getMessage()]);
        }
    }

    /**
     * Upload document for Purchase Order
     */
    public function uploadForPO(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $request->validate([
            'file' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
            'document_type' => 'required|in:PO,CONFORME,COA_PACKET',
        ]);

        DB::beginTransaction();
        try {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents/po/' . $purchaseOrder->id, $fileName, 'public');

            $document = Document::create([
                'po_id' => $purchaseOrder->id,
                'document_type' => $validated['document_type'],
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'file_size' => $file->getSize(),
                'uploaded_by' => Auth::id(),
            ]);

            activity_log('UPLOADED', 'DOCUMENT', $document->id, [
                'document_type' => $validated['document_type'],
                'file_name' => $file->getClientOriginalName(),
                'related_to' => 'PO-' . $purchaseOrder->po_number,
            ]);

            DB::commit();

            return back()->with('success', 'Document uploaded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to upload document: ' . $e->getMessage()]);
        }
    }

    /**
     * Download document
     */
    public function download(Document $document)
    {
        // Check authorization
        $user = Auth::user();
        $canAccess = false;

        if ($document->purchaseRequest) {
            $canAccess = $user->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN']) || 
                        ($user->hasRole('END_USER') && $document->purchaseRequest->end_user_id === $user->id);
        } elseif ($document->rfq) {
            $canAccess = $user->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN']) || 
                        $document->rfq->procurement_officer_id === $user->id;
        } elseif ($document->purchaseOrder) {
            $canAccess = $user->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN']);
        }

        if (!$canAccess) {
            abort(403, 'Unauthorized to access this document.');
        }

        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'Document file not found.');
        }

        activity_log('DOWNLOADED', 'DOCUMENT', $document->id, [
            'file_name' => $document->file_name,
        ]);

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    /**
     * Preview document
     */
    public function preview(Document $document)
    {
        // Check authorization (same as download)
        $user = Auth::user();
        $canAccess = false;

        if ($document->purchaseRequest) {
            $canAccess = $user->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN']) || 
                        ($user->hasRole('END_USER') && $document->purchaseRequest->end_user_id === $user->id);
        } elseif ($document->rfq) {
            $canAccess = $user->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN']) || 
                        $document->rfq->procurement_officer_id === $user->id;
        } elseif ($document->purchaseOrder) {
            $canAccess = $user->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN']);
        }

        if (!$canAccess) {
            abort(403, 'Unauthorized to access this document.');
        }

        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'Document file not found.');
        }

        $mimeType = Storage::disk('public')->mimeType($document->file_path);
        $fileContent = Storage::disk('public')->get($document->file_path);

        return response($fileContent, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $document->file_name . '"',
        ]);
    }

    /**
     * Delete document
     */
    public function destroy(Document $document)
    {
        // Check authorization
        $user = Auth::user();
        $canDelete = false;

        if ($document->purchaseRequest) {
            $canDelete = $user->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN']) || 
                        ($user->hasRole('END_USER') && $document->purchaseRequest->end_user_id === $user->id);
        } elseif ($document->rfq) {
            $canDelete = $user->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN']) || 
                        $document->rfq->procurement_officer_id === $user->id;
        } elseif ($document->purchaseOrder) {
            $canDelete = $user->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN']);
        }

        if (!$canDelete) {
            abort(403, 'Unauthorized to delete this document.');
        }

        DB::beginTransaction();
        try {
            // Delete file from storage
            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            activity_log('DELETED', 'DOCUMENT', $document->id, [
                'file_name' => $document->file_name,
            ]);

            $document->delete();

            DB::commit();

            return back()->with('success', 'Document deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to delete document: ' . $e->getMessage()]);
        }
    }
}

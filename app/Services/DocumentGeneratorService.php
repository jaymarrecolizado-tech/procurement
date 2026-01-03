<?php

namespace App\Services;

use App\Models\PurchaseRequest;
use App\Models\PurchaseOrder;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\View;

class DocumentGeneratorService
{
    /**
     * Generate PDF for Purchase Request
     */
    public function generatePurchaseRequestPDF(PurchaseRequest $pr): string
    {
        $pr->load(['prItems', 'endUser']);
        
        $html = View::make('documents.pr.template', [
            'pr' => $pr,
        ])->render();
        
        return $this->generatePDF($html);
    }
    
    /**
     * Generate PDF from HTML
     */
    private function generatePDF(string $html): string
    {
        try {
            // DomPDF v3 accepts options as array in constructor
            $options = [
                'defaultFont' => 'Times New Roman',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'chroot' => [public_path()], // chroot should be an array
            ];
            
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('letter', 'portrait');
            $dompdf->render();
            
            return $dompdf->output();
        } catch (\Exception $e) {
            // If DomPDF is not installed or has issues, throw a more helpful error
            throw new \RuntimeException('PDF generation failed. Please ensure DomPDF is properly installed: ' . $e->getMessage());
        }
    }
    
    /**
     * Download PDF
     */
    public function downloadPurchaseRequestPDF(PurchaseRequest $pr, string $filename = null): \Symfony\Component\HttpFoundation\Response
    {
        $pdfContent = $this->generatePurchaseRequestPDF($pr);
        $filename = $filename ?? "PR-{$pr->pr_number}.pdf";
        
        return response()->streamDownload(function () use ($pdfContent) {
            echo $pdfContent;
        }, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }
    
    /**
     * Stream PDF in browser
     */
    public function streamPurchaseRequestPDF(PurchaseRequest $pr, string $filename = null): \Symfony\Component\HttpFoundation\Response
    {
        $pdfContent = $this->generatePurchaseRequestPDF($pr);
        $filename = $filename ?? "PR-{$pr->pr_number}.pdf";
        
        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    /**
     * Generate PDF for Purchase Order
     */
    public function generatePurchaseOrderPDF(PurchaseOrder $po): string
    {
        $po->load(['purchaseRequest.prItems', 'purchaseRequest.endUser']);
        
        $html = View::make('documents.po.template', [
            'po' => $po,
        ])->render();
        
        return $this->generatePDF($html);
    }

    /**
     * Download Purchase Order PDF
     */
    public function downloadPurchaseOrderPDF(PurchaseOrder $po, string $filename = null): \Symfony\Component\HttpFoundation\Response
    {
        $pdfContent = $this->generatePurchaseOrderPDF($po);
        $filename = $filename ?? "PO-{$po->po_number}.pdf";
        
        return response()->streamDownload(function () use ($pdfContent) {
            echo $pdfContent;
        }, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Stream Purchase Order PDF in browser
     */
    public function streamPurchaseOrderPDF(PurchaseOrder $po, string $filename = null): \Symfony\Component\HttpFoundation\Response
    {
        $pdfContent = $this->generatePurchaseOrderPDF($po);
        $filename = $filename ?? "PO-{$po->po_number}.pdf";
        
        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }
}


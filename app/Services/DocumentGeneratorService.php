<?php

namespace App\Services;

use App\Models\PurchaseRequest;
use Dompdf\Dompdf;
use Dompdf\Options;
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
        $options = new Options();
        $options->set('defaultFont', 'Times New Roman');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('chroot', public_path());
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('letter', 'portrait');
        $dompdf->render();
        
        return $dompdf->output();
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
}


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order - {{ $po->po_number }}</title>
    <style>
        @page {
            margin: 1cm;
            size: letter;
        }
        
        body {
            font-family: 'Times New Roman', serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .header h1 {
            font-size: 14pt;
            font-weight: bold;
            margin: 5px 0;
            text-transform: uppercase;
        }
        
        .header h2 {
            font-size: 12pt;
            font-weight: bold;
            margin: 5px 0;
            text-transform: uppercase;
        }
        
        .header .office-address {
            font-size: 10pt;
            margin: 5px 0;
        }
        
        .document-title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin: 20px 0;
            text-transform: uppercase;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 10pt;
        }
        
        table th, table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }
        
        table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .signature-section {
            margin-top: 30px;
            display: table;
            width: 100%;
        }
        
        .signature-box {
            display: table-cell;
            width: 50%;
            padding: 10px;
            vertical-align: top;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 50px;
            padding-top: 5px;
        }
        
        .info-section {
            margin: 15px 0;
        }
        
        .info-label {
            font-weight: bold;
        }
        
        .total-row {
            font-weight: bold;
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>REPUBLIC OF THE PHILIPPINES</h1>
        <h2>DEPARTMENT OF INFORMATION AND COMMUNICATIONS TECHNOLOGY</h2>
        <h2>REGIONAL OFFICE 02</h2>
        <div class="office-address">02 Bagay Road, San Gabriel Village, Tuguegarao City, Cagayan 3500</div>
    </div>
    
    <!-- Document Title -->
    <div class="document-title">PURCHASE ORDER</div>
    
    <!-- PO Information -->
    <table>
        <tr>
            <td style="width: 20%;"><strong>PO Number:</strong></td>
            <td style="width: 30%;">{{ $po->po_number }}</td>
            <td style="width: 20%;"><strong>Date:</strong></td>
            <td style="width: 30%;">{{ $po->created_at->format('F d, Y') }}</td>
        </tr>
        <tr>
            <td><strong>PR Number:</strong></td>
            <td>{{ $po->purchaseRequest->pr_number }}</td>
            <td><strong>Project Title:</strong></td>
            <td>{{ $po->purchaseRequest->project_title }}</td>
        </tr>
    </table>
    
    <!-- Supplier Information -->
    <div class="info-section">
        <table>
            <tr>
                <td style="width: 20%;"><strong>Supplier:</strong></td>
                <td style="width: 80%;">{{ $po->supplier_name }}</td>
            </tr>
            @if($po->supplier_address)
            <tr>
                <td><strong>Address:</strong></td>
                <td>{{ $po->supplier_address }}</td>
            </tr>
            @endif
            @if($po->supplier_contact)
            <tr>
                <td><strong>Contact:</strong></td>
                <td>{{ $po->supplier_contact }}</td>
            </tr>
            @endif
        </table>
    </div>
    
    <!-- Items Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">Item No.</th>
                <th style="width: 8%;">Unit</th>
                <th style="width: 47%;">Item Description</th>
                <th style="width: 10%;">Quantity</th>
                <th style="width: 15%;">Unit Price</th>
                <th style="width: 15%;">Total Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($po->purchaseRequest->prItems as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $item->unit_of_measure }}</td>
                    <td>
                        <strong>{{ $item->item_name }}</strong>
                        @if($item->item_description)
                            <div style="font-size: 9pt; margin-top: 5px;">
                                {!! nl2br(e($item->item_description)) !!}
                            </div>
                        @endif
                    </td>
                    <td class="text-center">{{ number_format($item->quantity, 0) }}</td>
                    <td class="text-right">₱{{ number_format($item->estimated_price, 2) }}</td>
                    <td class="text-right">₱{{ number_format($item->total_estimated, 2) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4"></td>
                <td class="text-right"><strong>TOTAL</strong></td>
                <td class="text-right total-row">₱{{ number_format($po->contract_amount, 2) }}</td>
            </tr>
        </tbody>
    </table>
    
    <!-- Terms and Conditions -->
    <div class="info-section">
        @if($po->delivery_instructions)
        <table>
            <tr>
                <td style="width: 20%;"><strong>Delivery Instructions:</strong></td>
                <td style="width: 80%;">{{ $po->delivery_instructions }}</td>
            </tr>
        </table>
        @endif
        
        @if($po->payment_terms)
        <table>
            <tr>
                <td style="width: 20%;"><strong>Payment Terms:</strong></td>
                <td style="width: 80%;">{{ $po->payment_terms }}</td>
            </tr>
        </table>
        @endif
        
        <table>
            <tr>
                <td style="width: 20%;"><strong>Delivery Deadline:</strong></td>
                <td style="width: 80%;">{{ $po->delivery_deadline->format('F d, Y') }}</td>
            </tr>
        </table>
    </div>
    
    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-box">
            <div><strong>Prepared by:</strong></div>
            <div class="signature-line">
                <div style="text-align: center;">
                    <div style="margin-bottom: 5px;"><strong>PROCUREMENT OFFICER</strong></div>
                    <div style="font-size: 9pt;">Department of ICT</div>
                </div>
            </div>
        </div>
        <div class="signature-box">
            <div><strong>Approved by:</strong></div>
            <div class="signature-line">
                <div style="text-align: center;">
                    <div style="margin-bottom: 5px;"><strong>REGIONAL DIRECTOR</strong></div>
                    <div style="font-size: 9pt;">Department of ICT</div>
                </div>
            </div>
        </div>
    </div>
    
    @if($po->notes)
    <div class="info-section" style="margin-top: 20px;">
        <table>
            <tr>
                <td><strong>Notes:</strong></td>
                <td>{{ $po->notes }}</td>
            </tr>
        </table>
    </div>
    @endif
</body>
</html>


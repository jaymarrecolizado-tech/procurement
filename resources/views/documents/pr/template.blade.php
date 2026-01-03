<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Request - {{ $pr->pr_number }}</title>
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
        
        .item-description {
            text-align: left;
        }
        
        .item-specs {
            font-size: 9pt;
            margin-top: 5px;
            padding-left: 10px;
        }
        
        .item-specs ul {
            margin: 5px 0;
            padding-left: 20px;
        }
        
        .item-specs li {
            margin: 2px 0;
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
        
        .purpose-section {
            margin: 15px 0;
        }
        
        .purpose-label {
            font-weight: bold;
        }
        
        .funds-available {
            margin-top: 30px;
            text-align: right;
        }
        
        .funds-available-line {
            border-top: 1px solid #000;
            margin-top: 50px;
            padding-top: 5px;
            width: 200px;
            margin-left: auto;
        }
        
        .total-row {
            font-weight: bold;
            background-color: #f0f0f0;
        }
        
        .nothing-follows {
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>REPUBLIC OF THE PHILIPPINES</h1>
        <h2>DEPARTMENT OF INFORMATION AND COMMUNICATIONS TECHNOLOGY</h2>
        @if($pr->office_name)
            <h2>{{ strtoupper($pr->office_name) }}</h2>
        @else
            <h2>REGIONAL OFFICE 02</h2>
        @endif
        @if($pr->office_address)
            <div class="office-address">{{ $pr->office_address }}</div>
        @else
            <div class="office-address">02 Bagay Road, San Gabriel Village, Tuguegarao City, Cagayan 3500</div>
        @endif
    </div>
    
    <!-- Document Title -->
    <div class="document-title">PURCHASE REQUEST</div>
    
    <!-- Main Table -->
    <table>
        <tr>
            <td colspan="2"><strong>Entity Department of Information and Communications Technology</strong></td>
            <td colspan="2"></td>
            <td><strong>Fund Cluster:</strong></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="2"><strong>Office/Sec:</strong> {{ $pr->office_name ?? 'DICT - Region 02' }}</td>
            <td colspan="2"></td>
            <td><strong>Purchase Request:</strong> {{ $pr->pr_number }}</td>
            <td><strong>Date:</strong> {{ $pr->approval_date->format('F d, Y') }}</td>
        </tr>
        <tr>
            <td colspan="6"><strong>Responsibility Center:</strong> {{ $pr->responsibility_center ?? '310201100001000A' }}</td>
        </tr>
    </table>
    
    <!-- Items Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">Item No.</th>
                <th style="width: 8%;">Unit</th>
                <th style="width: 45%;">Item Description</th>
                <th style="width: 10%;">Quantity</th>
                <th style="width: 12%;">Unit Cost</th>
                <th style="width: 20%;">Total Cost</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pr->prItems as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $item->unit_of_measure }}</td>
                    <td class="item-description">
                        <strong>{{ $item->item_name }}</strong>
                        @if($item->item_description)
                            <div class="item-specs">
                                <strong>Specifications:</strong>
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
                <td colspan="3" class="nothing-follows text-center"><em>*nothing follows*</em></td>
                <td></td>
                <td class="text-right"><strong>TOTAL</strong></td>
                <td class="text-right total-row">₱{{ number_format($pr->estimated_budget, 2) }}</td>
            </tr>
        </tbody>
    </table>
    
    <!-- Purpose Section -->
    <div class="purpose-section">
        <table>
            <tr>
                <td style="width: 15%;"><strong>Purpose:</strong></td>
                <td style="width: 85%;">{{ $pr->purpose ?? 'Additional Procurement of ICT Equipment to support the day-to-day activities of eLGU/eGovPH Personnel for the implementation of eLGU System and for the conduct of eGovPH Orientation and Marketing.' }}</td>
            </tr>
        </table>
    </div>
    
    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-box">
            <div><strong>Requested by:</strong></div>
            <div class="signature-line">
                <div style="text-align: center;">
                    <div style="margin-bottom: 5px;"><strong>{{ $pr->requested_by_name ?? $pr->endUser->name }}</strong></div>
                    <div style="font-size: 9pt;">{{ $pr->requested_by_designation ?? 'eLGU / eGov Focal' }}</div>
                </div>
            </div>
        </div>
        <div class="signature-box">
            <div><strong>Approved by:</strong></div>
            <div class="signature-line">
                <div style="text-align: center;">
                    <div style="margin-bottom: 5px;"><strong>{{ $pr->approved_by_name ?? 'ENGR. PINKY T. JIMENEZ' }}</strong></div>
                    <div style="font-size: 9pt;">{{ $pr->approved_by_designation ?? 'Regional Director' }}</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Funds Available Section -->
    <div class="funds-available">
        <div><strong>FUNDS AVAILABLE</strong></div>
        <div class="funds-available-line">
            <div style="text-align: center;">
                <div style="margin-bottom: 5px;"><strong>{{ $pr->budget_officer_name ?? 'MINA FLOR T. VILLAFUERTE' }}</strong></div>
                <div style="font-size: 9pt;">{{ $pr->budget_officer_designation ?? 'OIC, Budget Officer' }}</div>
            </div>
        </div>
    </div>
</body>
</html>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Asset Label - {{ $asset->asset_tag }}</title>
    
    {{-- All styles inline for print optimization --}}
    <style>
        /* ============================================
           PRINT-OPTIMIZED STYLES
           ============================================ */
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #000;
            background: #fff;
        }
        
        .print-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Header Section */
        .print-header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .print-header h1 {
            font-size: 24pt;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }
        
        .print-header .company-name {
            font-size: 14pt;
            color: #666;
            margin-bottom: 3px;
        }
        
        .print-header .print-date {
            font-size: 9pt;
            color: #999;
        }
        
        /* Asset Tag Badge */
        .asset-tag-badge {
            background: #000;
            color: #fff;
            font-size: 28pt;
            font-weight: bold;
            padding: 15px 25px;
            text-align: center;
            border-radius: 8px;
            margin: 20px 0;
            letter-spacing: 3px;
        }
        
        /* QR Code Section */
        .qr-section {
            text-align: center;
            margin: 25px 0;
            padding: 20px;
            border: 2px dashed #ccc;
            border-radius: 8px;
        }
        
        .qr-code-container {
            display: inline-block;
            padding: 15px;
            background: #fff;
            border: 2px solid #333;
            border-radius: 5px;
        }
        
        #qrcode {
            margin: 0 auto;
        }
        
        .qr-label {
            font-size: 9pt;
            color: #666;
            margin-top: 10px;
        }
        
        /* Information Grid */
        .info-grid {
            display: table;
            width: 100%;
            margin: 20px 0;
        }
        
        .info-row {
            display: table-row;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            display: table-cell;
            font-weight: bold;
            padding: 10px 15px 10px 0;
            width: 35%;
            vertical-align: top;
            color: #333;
        }
        
        .info-value {
            display: table-cell;
            padding: 10px 0;
            color: #000;
            vertical-align: top;
        }
        
        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 3px;
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-active { background: #d4edda; color: #155724; }
        .status-deployed { background: #cce5ff; color: #004085; }
        .status-maintenance { background: #fff3cd; color: #856404; }
        .status-retired { background: #f8d7da; color: #721c24; }
        
        /* Sections */
        .print-section {
            margin: 25px 0;
            page-break-inside: avoid;
        }
        
        .section-title {
            font-size: 14pt;
            font-weight: bold;
            color: #333;
            border-bottom: 2px solid #333;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        
        /* Specifications Table */
        .specs-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        
        .specs-table td {
            padding: 8px 12px;
            border: 1px solid #ddd;
        }
        
        .specs-table td:first-child {
            font-weight: bold;
            background: #f5f5f5;
            width: 35%;
        }
        
        /* Footer */
        .print-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #333;
            text-align: center;
            font-size: 8pt;
            color: #999;
        }
        
        /* Print Actions */
        .print-actions {
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 9999;
        }
        
        .btn-print, .btn-close {
            border: none;
            padding: 12px 24px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .btn-print {
            background: #007bff;
            color: #fff;
        }
        
        .btn-print:hover {
            background: #0056b3;
        }
        
        .btn-close {
            background: #6c757d;
            color: #fff;
            margin-left: 10px;
        }
        
        .btn-close:hover {
            background: #545b62;
        }
        
        /* Print Media Query */
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            
            .print-container {
                max-width: 100%;
                padding: 10mm;
            }
            
            .print-actions {
                display: none !important;
            }
            
            /* Ensure good print quality */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            /* Prevent breaking inside sections */
            .print-section {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    {{-- Print Actions (Hidden on Print) --}}
    <div class="print-actions">
        <button onclick="window.print()" class="btn-print">
            Print
        </button>
        <button onclick="window.close()" class="btn-close">
            Close
        </button>
    </div>

    <div class="print-container">
        {{-- Header --}}
        <div class="print-header">
            <div class="company-name">IT Asset Management System</div>
            <h1>Asset Label</h1>
            <div class="print-date">Printed: {{ now()->format('F d, Y - h:i A') }}</div>
        </div>

        {{-- Asset Tag Badge --}}
        <div class="asset-tag-badge">
            {{ $asset->asset_tag }}
        </div>

        {{-- QR Code Section --}}
        @if($asset->qr_code && isset($qrCode))
        <div class="qr-section">
            <div class="qr-code-container">
                <img src="data:image/png;base64,{{ $qrCode }}" alt="QR Code" style="display: block; margin: 0 auto;">
            </div>
            <div class="qr-label">Scan QR code to view asset details</div>
        </div>
        @endif

        {{-- Basic Information --}}
        <div class="print-section">
            <div class="section-title">Asset Information</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Asset Tag:</div>
                    <div class="info-value">{{ $asset->asset_tag }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Model:</div>
                    <div class="info-value">{{ optional($asset->model)->asset_model ?? 'N/A' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Serial Number:</div>
                    <div class="info-value">{{ $asset->serial_number ?: 'N/A' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Status:</div>
                    <div class="info-value">
                        <span class="status-badge status-{{ strtolower(optional($asset->status)->status ?? 'active') }}">
                            {{ optional($asset->status)->status ?? 'Unknown' }}
                        </span>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Location:</div>
                    <div class="info-value">{{ optional($asset->location)->location_name ?? 'N/A' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Division:</div>
                    <div class="info-value">{{ optional($asset->division)->division_name ?? 'N/A' }}</div>
                </div>
                @if($asset->assignedTo)
                <div class="info-row">
                    <div class="info-label">Assigned To:</div>
                    <div class="info-value">{{ $asset->assignedTo->name }}</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Purchase & Warranty Information --}}
        <div class="print-section">
            <div class="section-title">Purchase & Warranty</div>
            <div class="info-grid">
                @if($asset->purchase_date)
                <div class="info-row">
                    <div class="info-label">Purchase Date:</div>
                    <div class="info-value">{{ $asset->purchase_date->format('F d, Y') }}</div>
                </div>
                @endif
                @if($asset->supplier)
                <div class="info-row">
                    <div class="info-label">Supplier:</div>
                    <div class="info-value">{{ $asset->supplier->name }}</div>
                </div>
                @endif
                @if($asset->purchase_cost)
                <div class="info-row">
                    <div class="info-label">Purchase Cost:</div>
                    <div class="info-value">R{{ number_format($asset->purchase_cost, 2) }}</div>
                </div>
                @endif
                @if($asset->warranty_months)
                <div class="info-row">
                    <div class="info-label">Warranty:</div>
                    <div class="info-value">{{ $asset->warranty_months }} months</div>
                </div>
                @php
                    $warrantyEnd = $asset->purchase_date ? $asset->purchase_date->copy()->addMonths($asset->warranty_months) : null;
                    $isWarrantyActive = $warrantyEnd && $warrantyEnd->isFuture();
                @endphp
                @if($warrantyEnd)
                <div class="info-row">
                    <div class="info-label">Warranty Expires:</div>
                    <div class="info-value">
                        {{ $warrantyEnd->format('F d, Y') }}
                        @if($isWarrantyActive)
                            <span class="status-badge status-active">Active</span>
                        @else
                            <span class="status-badge status-retired">Expired</span>
                        @endif
                    </div>
                </div>
                @endif
                @endif
            </div>
        </div>

        {{-- Specifications (if available) --}}
        @if($asset->pcspec)
        <div class="print-section">
            <div class="section-title">Technical Specifications</div>
            <table class="specs-table">
                @if($asset->pcspec->processor)
                <tr>
                    <td>Processor</td>
                    <td>{{ $asset->pcspec->processor }}</td>
                </tr>
                @endif
                @if($asset->pcspec->ram)
                <tr>
                    <td>RAM</td>
                    <td>{{ $asset->pcspec->ram }}</td>
                </tr>
                @endif
                @if($asset->pcspec->storage)
                <tr>
                    <td>Storage</td>
                    <td>{{ $asset->pcspec->storage }}</td>
                </tr>
                @endif
                @if($asset->pcspec->graphics)
                <tr>
                    <td>Graphics</td>
                    <td>{{ $asset->pcspec->graphics }}</td>
                </tr>
                @endif
                @if($asset->pcspec->display)
                <tr>
                    <td>Display</td>
                    <td>{{ $asset->pcspec->display }}</td>
                </tr>
                @endif
                @if($asset->pcspec->os)
                <tr>
                    <td>Operating System</td>
                    <td>{{ $asset->pcspec->os }}</td>
                </tr>
                @endif
            </table>
        </div>
        @endif

        {{-- Network Information (if available) --}}
        @if($asset->ip_address || $asset->mac_address || $asset->computer_name)
        <div class="print-section">
            <div class="section-title">Network Information</div>
            <div class="info-grid">
                @if($asset->ip_address)
                <div class="info-row">
                    <div class="info-label">IP Address:</div>
                    <div class="info-value">{{ $asset->ip_address }}</div>
                </div>
                @endif
                @if($asset->mac_address)
                <div class="info-row">
                    <div class="info-label">MAC Address:</div>
                    <div class="info-value">{{ $asset->mac_address }}</div>
                </div>
                @endif
                @if($asset->computer_name)
                <div class="info-row">
                    <div class="info-label">Computer Name:</div>
                    <div class="info-value">{{ $asset->computer_name }}</div>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Notes (if available) --}}
        @if($asset->notes)
        <div class="print-section">
            <div class="section-title">Notes</div>
            <div style="padding: 10px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 4px;">
                {!! nl2br(e($asset->notes)) !!}
            </div>
        </div>
        @endif

        {{-- Footer --}}
        <div class="print-footer">
            <p><strong>IT Asset Management System</strong></p>
            <p>This document is property of the organization. Unauthorized use is prohibited.</p>
            <p>Generated on {{ now()->format('F d, Y') }} at {{ now()->format('h:i A') }}</p>
        </div>
    </div>

</body>
</html>
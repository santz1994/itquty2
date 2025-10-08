<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Asset Details - {{ $asset->asset_tag }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .asset-info {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            width: 30%;
            font-weight: bold;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .info-value {
            display: table-cell;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin: 20px 0 10px 0;
            color: #007bff;
            border-bottom: 1px solid #007bff;
            padding-bottom: 5px;
        }
        .qr-code {
            text-align: center;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">IT Asset Management System</div>
        <div class="title">Asset Detail Report</div>
        <div>Generated on: {{ now()->format('d F Y, H:i:s') }}</div>
    </div>

    <div class="section-title">Basic Information</div>
    <div class="asset-info">
        <div class="info-row">
            <div class="info-label">Asset Tag:</div>
            <div class="info-value">{{ $asset->asset_tag }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Serial Number:</div>
            <div class="info-value">{{ $asset->serial_number ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Model:</div>
            <div class="info-value">{{ $asset->model->name ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Division:</div>
            <div class="info-value">{{ $asset->division->name ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Status:</div>
            <div class="info-value">{{ $asset->status->name ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Assigned To:</div>
            <div class="info-value">{{ $asset->assignedTo->name ?? 'Unassigned' }}</div>
        </div>
    </div>

    <div class="section-title">Technical Specifications</div>
    <div class="asset-info">
        <div class="info-row">
            <div class="info-label">IP Address:</div>
            <div class="info-value">{{ $asset->ip_address ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">MAC Address:</div>
            <div class="info-value">{{ $asset->mac_address ?? 'N/A' }}</div>
        </div>
    </div>

    <div class="section-title">Purchase Information</div>
    <div class="asset-info">
        <div class="info-row">
            <div class="info-label">Supplier:</div>
            <div class="info-value">{{ $asset->supplier->name ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Purchase Date:</div>
            <div class="info-value">{{ $asset->purchase_date ? $asset->purchase_date->format('d F Y') : 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Warranty:</div>
            <div class="info-value">{{ $asset->warranty_months ? $asset->warranty_months . ' months' : 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Warranty Type:</div>
            <div class="info-value">{{ $asset->warranty_type->name ?? 'N/A' }}</div>
        </div>
    </div>

    @if($asset->notes)
    <div class="section-title">Notes</div>
    <div style="margin-bottom: 20px;">
        {{ $asset->notes }}
    </div>
    @endif

    <div class="qr-code">
        <div class="section-title">QR Code</div>
        <div>QR Code: {{ $asset->qr_code }}</div>
        <div style="font-size: 10px; color: #666; margin-top: 5px;">
            Scan this QR code to quickly access asset information
        </div>
    </div>

    <div class="footer">
        <div>IT Asset Management System - Asset Report</div>
        <div>This document was generated automatically on {{ now()->format('d F Y \a\t H:i:s') }}</div>
    </div>
</body>
</html>
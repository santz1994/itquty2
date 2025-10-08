@extends('layouts.app')

@section('main-content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Detail Aktivitas Harian</h3>
                    <div class="card-tools">
                        <a href="{{ route('daily-activities.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        @can('edit daily activities')
                            <a href="{{ route('daily-activities.edit', $dailyActivity->id) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="35%">Tanggal Aktivitas:</th>
                                    <td>{{ $dailyActivity->activity_date->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Tipe Aktivitas:</th>
                                    <td>
                                        @php
                                            $activityTypes = [
                                                'ticket_handling' => 'Penanganan Ticket',
                                                'asset_management' => 'Manajemen Asset',
                                                'user_support' => 'Dukungan User',
                                                'system_maintenance' => 'Maintenance Sistem',
                                                'training' => 'Pelatihan',
                                                'documentation' => 'Dokumentasi',
                                                'meeting' => 'Meeting/Rapat',
                                                'other' => 'Lainnya'
                                            ];
                                        @endphp
                                        <span class="badge badge-info">
                                            {{ $activityTypes[$dailyActivity->activity_type] ?? $dailyActivity->activity_type }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        @php
                                            $statusTypes = [
                                                'in_progress' => ['label' => 'Sedang Berlangsung', 'class' => 'badge-warning'],
                                                'completed' => ['label' => 'Selesai', 'class' => 'badge-success'],
                                                'paused' => ['label' => 'Ditunda', 'class' => 'badge-secondary'],
                                                'cancelled' => ['label' => 'Dibatalkan', 'class' => 'badge-danger']
                                            ];
                                            $status = $statusTypes[$dailyActivity->status] ?? ['label' => $dailyActivity->status, 'class' => 'badge-secondary'];
                                        @endphp
                                        <span class="badge {{ $status['class'] }}">
                                            {{ $status['label'] }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Judul Aktivitas:</th>
                                    <td><strong>{{ $dailyActivity->title }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Waktu:</th>
                                    <td>
                                        @if($dailyActivity->start_time && $dailyActivity->end_time)
                                            {{ $dailyActivity->start_time->format('H:i') }} - {{ $dailyActivity->end_time->format('H:i') }}
                                            @php
                                                $duration = $dailyActivity->start_time->diff($dailyActivity->end_time);
                                                $hours = $duration->h;
                                                $minutes = $duration->i;
                                            @endphp
                                            <small class="text-muted">
                                                ({{ $hours }}j {{ $minutes }}m)
                                            </small>
                                        @elseif($dailyActivity->start_time)
                                            Mulai: {{ $dailyActivity->start_time->format('H:i') }}
                                        @elseif($dailyActivity->end_time)
                                            Selesai: {{ $dailyActivity->end_time->format('H:i') }}
                                        @else
                                            <span class="text-muted">Waktu tidak tercatat</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Prioritas:</th>
                                    <td>
                                        @if($dailyActivity->is_priority)
                                            <span class="badge badge-danger">
                                                <i class="fas fa-star"></i> Prioritas Tinggi
                                            </span>
                                        @else
                                            <span class="badge badge-light">Normal</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="35%">Petugas:</th>
                                    <td>{{ $dailyActivity->user->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Asset Terkait:</th>
                                    <td>
                                        @if($dailyActivity->relatedAsset)
                                            <a href="{{ route('assets.show', $dailyActivity->relatedAsset->id) }}" class="text-decoration-none">
                                                <span class="badge badge-outline-primary">
                                                    {{ $dailyActivity->relatedAsset->asset_tag }} - {{ $dailyActivity->relatedAsset->name }}
                                                </span>
                                            </a>
                                        @else
                                            <span class="text-muted">Tidak ada</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Ticket Terkait:</th>
                                    <td>
                                        @if($dailyActivity->relatedTicket)
                                            <a href="{{ route('tickets.show', $dailyActivity->relatedTicket->id) }}" class="text-decoration-none">
                                                <span class="badge badge-outline-info">
                                                    #{{ $dailyActivity->relatedTicket->id }} - {{ Str::limit($dailyActivity->relatedTicket->subject, 30) }}
                                                </span>
                                            </a>
                                        @else
                                            <span class="text-muted">Tidak ada</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Dibuat:</th>
                                    <td>{{ $dailyActivity->created_at->format('d M Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Terakhir Update:</th>
                                    <td>{{ $dailyActivity->updated_at->format('d M Y H:i') }}</td>
                                </tr>
                                @if($dailyActivity->created_at != $dailyActivity->updated_at)
                                <tr>
                                    <th>Diupdate oleh:</th>
                                    <td>{{ $dailyActivity->updatedBy->name ?? 'System' }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-12">
                            <h5>Deskripsi Aktivitas</h5>
                            <div class="card card-outline card-secondary">
                                <div class="card-body">
                                    <p class="mb-0">{{ $dailyActivity->description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($dailyActivity->notes)
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h5>Catatan Tambahan</h5>
                            <div class="card card-outline card-info">
                                <div class="card-body">
                                    <p class="mb-0">{{ $dailyActivity->notes }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($dailyActivity->relatedAsset && $dailyActivity->relatedAsset->movements->count() > 0)
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h5>Riwayat Asset Terkait</h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Tipe Movement</th>
                                            <th>Lokasi</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($dailyActivity->relatedAsset->movements->take(5) as $movement)
                                        <tr>
                                            <td>{{ $movement->created_at->format('d M Y') }}</td>
                                            <td>
                                                <span class="badge badge-sm badge-secondary">
                                                    {{ ucfirst(str_replace('_', ' ', $movement->movement_type)) }}
                                                </span>
                                            </td>
                                            <td>{{ $movement->location->name ?? 'N/A' }}</td>
                                            <td>{{ Str::limit($movement->notes, 50) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @if($dailyActivity->relatedAsset->movements->count() > 5)
                                <small class="text-muted">
                                    Menampilkan 5 dari {{ $dailyActivity->relatedAsset->movements->count() }} movement terbaru
                                </small>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            @can('edit daily activities')
                                <a href="{{ route('daily-activities.edit', $dailyActivity->id) }}" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Edit Aktivitas
                                </a>
                            @endcan
                        </div>
                        <div class="col-md-6 text-right">
                            @can('delete daily activities')
                                <form action="{{ route('daily-activities.destroy', $dailyActivity->id) }}" method="POST" class="d-inline" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus aktivitas ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            @endcan
                            <button onclick="window.print()" class="btn btn-outline-secondary">
                                <i class="fas fa-print"></i> Print
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
@media print {
    .card-header .card-tools,
    .card-footer,
    .btn {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .badge-outline-primary {
        border: 1px solid #007bff !important;
        color: #007bff !important;
    }
    
    .badge-outline-info {
        border: 1px solid #17a2b8 !important;
        color: #17a2b8 !important;
    }
}

.badge-outline-primary {
    background-color: transparent;
    border: 1px solid #007bff;
    color: #007bff;
}

.badge-outline-info {
    background-color: transparent;
    border: 1px solid #17a2b8;
    color: #17a2b8;
}

.table-borderless th,
.table-borderless td {
    border: none;
    padding: 0.5rem 0.75rem;
}

.table-borderless th {
    font-weight: 600;
    color: #495057;
}
</style>
@endpush
@endsection
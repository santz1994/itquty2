# 🎨 UI/UX IMPROVEMENT CODE EXAMPLES
## Ready-to-Implement Solutions for ITQuty

---

## 1. 🎯 MODERN DASHBOARD REDESIGN

### Current Dashboard Issues:
- Static metric boxes
- No interactivity
- No trend indicators
- Cluttered layout

### Solution: Interactive Metric Cards

```blade
{{-- resources/views/dashboard/widgets/metric-card.blade.php --}}
<div class="col-xl-3 col-md-6 mb-4">
    <div class="card metric-card border-left-{{ $color }} shadow h-100 py-2" 
         onclick="navigateTo('{{ $link }}')">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-{{ $color }} text-uppercase mb-1">
                        {{ $title }}
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ $value }}
                    </div>
                    @if(isset($trend))
                    <div class="mt-2">
                        <small class="text-{{ $trend > 0 ? 'success' : 'danger' }}">
                            <i class="fas fa-arrow-{{ $trend > 0 ? 'up' : 'down' }}"></i>
                            {{ abs($trend) }}% vs last month
                        </small>
                    </div>
                    @endif
                </div>
                <div class="col-auto">
                    <i class="fas fa-{{ $icon }} fa-2x text-gray-300"></i>
                </div>
            </div>
            
            {{-- Mini sparkline chart --}}
            @if(isset($sparklineData))
            <div class="mt-3">
                <canvas id="sparkline-{{ $id }}" height="30"></canvas>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.metric-card {
    cursor: pointer;
    transition: all 0.3s ease;
    border-left: 4px solid;
}

.metric-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.border-left-primary { border-left-color: #4e73df; }
.border-left-success { border-left-color: #1cc88a; }
.border-left-warning { border-left-color: #f6c23e; }
.border-left-danger { border-left-color: #e74a3b; }
</style>

<script>
function navigateTo(url) {
    window.location.href = url;
}

// Initialize sparkline
@if(isset($sparklineData))
new Chart(document.getElementById('sparkline-{{ $id }}'), {
    type: 'line',
    data: {
        labels: {!! json_encode($sparklineData['labels']) !!},
        datasets: [{
            data: {!! json_encode($sparklineData['values']) !!},
            borderColor: '{{ $sparklineColor ?? "#4e73df" }}',
            backgroundColor: 'rgba(78, 115, 223, 0.05)',
            borderWidth: 2,
            pointRadius: 0,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { display: false },
            y: { display: false }
        }
    }
});
@endif
</script>
```

**Usage:**
```blade
{{-- In your dashboard view --}}
<div class="row">
    @include('dashboard.widgets.metric-card', [
        'id' => 'total-tickets',
        'title' => 'Total Tickets',
        'value' => $totalTickets,
        'trend' => 12,
        'icon' => 'ticket',
        'color' => 'primary',
        'link' => '/tickets',
        'sparklineData' => [
            'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            'values' => [12, 19, 15, 25, 22, 30, 28]
        ]
    ])
    
    @include('dashboard.widgets.metric-card', [
        'id' => 'open-tickets',
        'title' => 'Open Tickets',
        'value' => $openTickets,
        'trend' => -5,
        'icon' => 'folder-open',
        'color' => 'warning',
        'link' => '/tickets?status=open'
    ])
</div>
```

---

## 2. 📋 ADVANCED FORM WITH LIVE VALIDATION

### Problem: Basic forms with no feedback

### Solution: Smart Form Component

```blade
{{-- resources/views/components/smart-form.blade.php --}}
<form id="{{ $formId }}" 
      method="{{ $method ?? 'POST' }}" 
      action="{{ $action }}"
      x-data="smartForm()"
      @submit.prevent="submitForm">
    @csrf
    @if(isset($method) && strtoupper($method) !== 'POST')
        @method($method)
    @endif
    
    <div class="form-loading" x-show="loading">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    
    {{ $slot }}
</form>

<script>
function smartForm() {
    return {
        loading: false,
        errors: {},
        
        async submitForm(event) {
            this.loading = true;
            this.errors = {};
            
            const formData = new FormData(event.target);
            
            try {
                const response = await fetch(event.target.action, {
                    method: event.target.method,
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    // Success
                    this.showSuccess(data.message || 'Success!');
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    }
                } else {
                    // Validation errors
                    this.errors = data.errors || {};
                    this.showError('Please fix the errors below');
                }
            } catch (error) {
                this.showError('An error occurred. Please try again.');
            } finally {
                this.loading = false;
            }
        },
        
        showSuccess(message) {
            toastr.success(message);
        },
        
        showError(message) {
            toastr.error(message);
        }
    }
}
</script>

<style>
.form-loading {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}
</style>
```

### Smart Input Field with Validation

```blade
{{-- resources/views/components/form-input.blade.php --}}
<div class="form-group" x-data="{ 
    value: '{{ old($name, $value ?? '') }}',
    checking: false,
    valid: null,
    error: ''
}">
    <label for="{{ $name }}" class="form-label">
        {{ $label }}
        @if($required ?? false)
            <span class="text-danger">*</span>
        @endif
    </label>
    
    <div class="input-group">
        <input 
            type="{{ $type ?? 'text' }}"
            name="{{ $name }}"
            id="{{ $name }}"
            class="form-control @error($name) is-invalid @enderror"
            placeholder="{{ $placeholder ?? '' }}"
            value="{{ old($name, $value ?? '') }}"
            {{ ($required ?? false) ? 'required' : '' }}
            x-model="value"
            @if($liveValidate ?? false)
                @blur="validateField"
            @endif
            :class="{ 
                'is-valid': valid === true, 
                'is-invalid': valid === false 
            }"
        >
        
        @if($liveValidate ?? false)
        <div class="input-group-append">
            <span class="input-group-text">
                <i class="fas fa-spinner fa-spin" x-show="checking"></i>
                <i class="fas fa-check text-success" x-show="valid === true"></i>
                <i class="fas fa-times text-danger" x-show="valid === false"></i>
            </span>
        </div>
        @endif
    </div>
    
    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
    
    <div class="invalid-feedback" x-show="valid === false" x-text="error"></div>
    
    @if($help ?? false)
        <small class="form-text text-muted">
            <i class="fas fa-info-circle"></i> {{ $help }}
        </small>
    @endif
</div>

<script>
function validateField() {
    if (!this.value) return;
    
    this.checking = true;
    this.valid = null;
    
    // Example: Check if asset tag is unique
    fetch(`/api/validate/{{ $name }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ value: this.value })
    })
    .then(response => response.json())
    .then(data => {
        this.valid = data.valid;
        this.error = data.message || '';
        this.checking = false;
    })
    .catch(() => {
        this.checking = false;
    });
}
</script>
```

**Usage:**
```blade
<x-smart-form formId="createAssetForm" action="{{ route('assets.store') }}">
    <x-form-input 
        name="asset_tag" 
        label="Asset Tag" 
        type="text"
        placeholder="e.g., AST-001"
        required
        liveValidate
        help="Must be unique, max 10 characters"
    />
    
    <x-form-input 
        name="serial_number" 
        label="Serial Number" 
        type="text"
        required
    />
    
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> Create Asset
    </button>
</x-smart-form>
```

---

## 3. 🔍 GLOBAL SEARCH WITH LIVE RESULTS

### Problem: No global search across entities

### Solution: Instant Search Component

```blade
{{-- resources/views/components/global-search.blade.php --}}
<div class="global-search" x-data="globalSearch()">
    <div class="search-wrapper">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">
                    <i class="fas fa-search"></i>
                </span>
            </div>
            <input 
                type="text" 
                class="form-control" 
                placeholder="Search tickets, assets, users... (Ctrl+K)"
                x-model="query"
                @input.debounce.300ms="search"
                @keydown.escape="closeResults"
                @keydown.arrow-down.prevent="selectNext"
                @keydown.arrow-up.prevent="selectPrev"
                @keydown.enter.prevent="openSelected"
                x-ref="searchInput"
            >
            <div class="input-group-append" x-show="loading">
                <span class="input-group-text">
                    <i class="fas fa-spinner fa-spin"></i>
                </span>
            </div>
        </div>
        
        {{-- Results Dropdown --}}
        <div class="search-results" x-show="showResults" @click.away="closeResults">
            <div x-show="results.length === 0 && !loading && query.length > 0" 
                 class="text-center py-4 text-muted">
                <i class="fas fa-search fa-2x mb-2"></i>
                <p>No results found for "{{ query }}"</p>
            </div>
            
            <template x-for="(group, index) in groupedResults" :key="index">
                <div class="result-group">
                    <div class="result-group-title" x-text="group.title"></div>
                    <template x-for="item in group.items" :key="item.id">
                        <a 
                            :href="item.url" 
                            class="result-item"
                            :class="{ 'active': item === selectedItem }"
                            @mouseenter="selectedItem = item"
                        >
                            <div class="result-icon">
                                <i :class="item.icon"></i>
                            </div>
                            <div class="result-content">
                                <div class="result-title" x-html="highlightQuery(item.title)"></div>
                                <div class="result-description" x-text="item.description"></div>
                            </div>
                            <div class="result-meta">
                                <span class="badge" :class="'badge-' + item.badge" x-text="item.badgeText"></span>
                            </div>
                        </a>
                    </template>
                </div>
            </template>
        </div>
    </div>
</div>

<style>
.global-search {
    position: relative;
    max-width: 600px;
    margin: 0 auto;
}

.search-results {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-radius: 0 0 4px 4px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    max-height: 500px;
    overflow-y: auto;
    z-index: 1000;
    margin-top: 5px;
}

.result-group {
    border-bottom: 1px solid #eee;
}

.result-group-title {
    padding: 8px 15px;
    background: #f8f9fa;
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
    color: #6c757d;
}

.result-item {
    display: flex;
    align-items: center;
    padding: 12px 15px;
    border-bottom: 1px solid #f0f0f0;
    text-decoration: none;
    color: inherit;
    transition: background 0.2s;
}

.result-item:hover,
.result-item.active {
    background: #f8f9fa;
}

.result-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #e9ecef;
    border-radius: 50%;
    margin-right: 12px;
}

.result-content {
    flex: 1;
}

.result-title {
    font-weight: 600;
    margin-bottom: 2px;
}

.result-description {
    font-size: 13px;
    color: #6c757d;
}

.result-meta {
    margin-left: 10px;
}

mark {
    background: #fff3cd;
    padding: 2px 4px;
    border-radius: 2px;
}
</style>

<script>
function globalSearch() {
    return {
        query: '',
        results: [],
        loading: false,
        showResults: false,
        selectedItem: null,
        selectedIndex: 0,
        
        init() {
            // Keyboard shortcut Ctrl+K
            document.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    this.$refs.searchInput.focus();
                }
            });
        },
        
        async search() {
            if (this.query.length < 2) {
                this.results = [];
                this.showResults = false;
                return;
            }
            
            this.loading = true;
            this.showResults = true;
            
            try {
                const response = await fetch(`/api/search?q=${encodeURIComponent(this.query)}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await response.json();
                this.results = data.results || [];
                this.selectedIndex = 0;
                if (this.results.length > 0) {
                    this.selectedItem = this.results[0];
                }
            } catch (error) {
                console.error('Search error:', error);
            } finally {
                this.loading = false;
            }
        },
        
        get groupedResults() {
            const groups = {
                tickets: { title: 'Tickets', items: [], icon: 'fas fa-ticket' },
                assets: { title: 'Assets', items: [], icon: 'fas fa-desktop' },
                users: { title: 'Users', items: [], icon: 'fas fa-user' }
            };
            
            this.results.forEach(item => {
                if (groups[item.type]) {
                    groups[item.type].items.push(item);
                }
            });
            
            return Object.values(groups).filter(g => g.items.length > 0);
        },
        
        highlightQuery(text) {
            if (!this.query) return text;
            const regex = new RegExp(`(${this.query})`, 'gi');
            return text.replace(regex, '<mark>$1</mark>');
        },
        
        selectNext() {
            if (this.selectedIndex < this.results.length - 1) {
                this.selectedIndex++;
                this.selectedItem = this.results[this.selectedIndex];
            }
        },
        
        selectPrev() {
            if (this.selectedIndex > 0) {
                this.selectedIndex--;
                this.selectedItem = this.results[this.selectedIndex];
            }
        },
        
        openSelected() {
            if (this.selectedItem) {
                window.location.href = this.selectedItem.url;
            }
        },
        
        closeResults() {
            this.showResults = false;
        }
    }
}
</script>
```

**Backend API Controller:**
```php
<?php
// app/Http/Controllers/API/SearchController.php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Ticket;
use App\Asset;
use App\User;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q');
        $results = [];
        
        // Search Tickets
        $tickets = Ticket::where('ticket_code', 'like', "%{$query}%")
            ->orWhere('subject', 'like', "%{$query}%")
            ->with(['ticket_status', 'ticket_priority'])
            ->limit(5)
            ->get();
            
        foreach ($tickets as $ticket) {
            $results[] = [
                'id' => $ticket->id,
                'type' => 'tickets',
                'title' => $ticket->ticket_code . ' - ' . $ticket->subject,
                'description' => \Str::limit($ticket->description, 60),
                'url' => route('tickets.show', $ticket->id),
                'icon' => 'fas fa-ticket',
                'badge' => $ticket->ticket_status->color ?? 'secondary',
                'badgeText' => $ticket->ticket_status->status ?? 'Unknown'
            ];
        }
        
        // Search Assets
        $assets = Asset::where('asset_tag', 'like', "%{$query}%")
            ->orWhere('serial_number', 'like', "%{$query}%")
            ->with(['model', 'status'])
            ->limit(5)
            ->get();
            
        foreach ($assets as $asset) {
            $results[] = [
                'id' => $asset->id,
                'type' => 'assets',
                'title' => $asset->asset_tag . ' - ' . ($asset->model->asset_model ?? 'Unknown'),
                'description' => 'S/N: ' . $asset->serial_number,
                'url' => route('assets.show', $asset->id),
                'icon' => 'fas fa-desktop',
                'badge' => $asset->status->color ?? 'secondary',
                'badgeText' => $asset->status->name ?? 'Unknown'
            ];
        }
        
        // Search Users (admin only)
        if (auth()->user()->hasRole(['admin', 'super-admin'])) {
            $users = User::where('name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->limit(5)
                ->get();
                
            foreach ($users as $user) {
                $results[] = [
                    'id' => $user->id,
                    'type' => 'users',
                    'title' => $user->name,
                    'description' => $user->email,
                    'url' => route('users.show', $user->id),
                    'icon' => 'fas fa-user',
                    'badge' => 'info',
                    'badgeText' => $user->roles->first()->name ?? 'user'
                ];
            }
        }
        
        return response()->json(['results' => $results]);
    }
}
```

---

## 4. 📊 INTERACTIVE DATA TABLE

### Problem: Static tables with poor UX

### Solution: Enhanced DataTable Component

```blade
{{-- resources/views/components/enhanced-table.blade.php --}}
<div class="enhanced-table-wrapper" x-data="enhancedTable()">
    {{-- Toolbar --}}
    <div class="table-toolbar mb-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="btn-group">
                    <button 
                        class="btn btn-primary" 
                        x-show="selectedRows.length > 0"
                        @click="showBulkActions = true"
                    >
                        <i class="fas fa-tasks"></i> 
                        Bulk Actions (<span x-text="selectedRows.length"></span>)
                    </button>
                    
                    <div class="btn-group">
                        <button 
                            class="btn btn-outline-secondary dropdown-toggle" 
                            data-toggle="dropdown"
                        >
                            <i class="fas fa-columns"></i> Columns
                        </button>
                        <div class="dropdown-menu">
                            <template x-for="column in columns" :key="column.field">
                                <label class="dropdown-item">
                                    <input 
                                        type="checkbox" 
                                        :checked="column.visible"
                                        @change="toggleColumn(column.field)"
                                    >
                                    <span x-text="column.label"></span>
                                </label>
                            </template>
                        </div>
                    </div>
                    
                    <div class="btn-group">
                        <button 
                            class="btn btn-outline-secondary dropdown-toggle" 
                            data-toggle="dropdown"
                        >
                            <i class="fas fa-download"></i> Export
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" @click="exportData('excel')">
                                <i class="fas fa-file-excel text-success"></i> Excel
                            </a>
                            <a class="dropdown-item" @click="exportData('csv')">
                                <i class="fas fa-file-csv text-info"></i> CSV
                            </a>
                            <a class="dropdown-item" @click="exportData('pdf')">
                                <i class="fas fa-file-pdf text-danger"></i> PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="d-flex justify-content-end">
                    <div class="input-group" style="max-width: 300px;">
                        <input 
                            type="text" 
                            class="form-control" 
                            placeholder="Search..."
                            x-model="searchQuery"
                            @input.debounce.300ms="filterData"
                        >
                        <div class="input-group-append">
                            <button 
                                class="btn btn-outline-secondary" 
                                @click="showAdvancedFilters = !showAdvancedFilters"
                            >
                                <i class="fas fa-filter"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Advanced Filters --}}
        <div class="advanced-filters mt-3" x-show="showAdvancedFilters" x-collapse>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        {{ $filters ?? '' }}
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-primary btn-sm" @click="applyFilters">
                            Apply Filters
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" @click="clearFilters">
                            Clear All
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Table --}}
    <div class="table-responsive">
        <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th style="width: 40px;">
                        <input 
                            type="checkbox" 
                            @change="toggleSelectAll($event.target.checked)"
                            :checked="allSelected"
                        >
                    </th>
                    {{ $thead }}
                </tr>
            </thead>
            <tbody>
                {{ $tbody }}
            </tbody>
        </table>
    </div>
    
    {{-- Pagination --}}
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div>
            Showing <strong><span x-text="startRow"></span></strong> to 
            <strong><span x-text="endRow"></span></strong> of 
            <strong><span x-text="totalRows"></span></strong> entries
        </div>
        <nav>
            {{ $pagination ?? '' }}
        </nav>
    </div>
    
    {{-- Bulk Actions Modal --}}
    <div class="modal fade" x-show="showBulkActions" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bulk Actions</h5>
                    <button type="button" class="close" @click="showBulkActions = false">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Selected <strong><span x-text="selectedRows.length"></span></strong> items</p>
                    {{ $bulkActions ?? '' }}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function enhancedTable() {
    return {
        selectedRows: [],
        allSelected: false,
        showBulkActions: false,
        showAdvancedFilters: false,
        searchQuery: '',
        columns: [],
        
        init() {
            // Initialize column visibility
            this.columns = this.getColumns();
        },
        
        toggleSelectAll(checked) {
            this.allSelected = checked;
            const checkboxes = document.querySelectorAll('.row-select');
            checkboxes.forEach(cb => {
                cb.checked = checked;
                if (checked) {
                    this.selectedRows.push(cb.value);
                } else {
                    this.selectedRows = [];
                }
            });
        },
        
        toggleColumn(field) {
            const column = this.columns.find(c => c.field === field);
            if (column) {
                column.visible = !column.visible;
                // Toggle column visibility in table
                document.querySelectorAll(`[data-column="${field}"]`).forEach(el => {
                    el.style.display = column.visible ? '' : 'none';
                });
            }
        },
        
        exportData(format) {
            window.location.href = `{{ $exportUrl }}?format=${format}&ids=${this.selectedRows.join(',')}`;
        },
        
        filterData() {
            // Implement filtering logic
            console.log('Filtering:', this.searchQuery);
        },
        
        getColumns() {
            // Extract column definitions from table
            return [
                { field: 'tag', label: 'Tag', visible: true },
                { field: 'type', label: 'Type', visible: true },
                { field: 'serial', label: 'Serial', visible: true },
                // ... more columns
            ];
        }
    }
}
</script>

<style>
.enhanced-table-wrapper {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.table-toolbar {
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
}

.advanced-filters .card {
    background: #f8f9fa;
    border: none;
}

tbody tr {
    cursor: pointer;
    transition: background 0.2s;
}

tbody tr:hover {
    background: #f8f9fa !important;
}
</style>
```

**Usage:**
```blade
<x-enhanced-table exportUrl="{{ route('assets.export') }}">
    <x-slot name="thead">
        <th>Tag</th>
        <th>Type</th>
        <th>Status</th>
        <th>Actions</th>
    </x-slot>
    
    <x-slot name="tbody">
        @foreach($assets as $asset)
        <tr>
            <td>
                <input type="checkbox" class="row-select" value="{{ $asset->id }}">
            </td>
            <td>{{ $asset->asset_tag }}</td>
            <td>{{ $asset->type->name }}</td>
            <td>
                <span class="badge badge-{{ $asset->status->color }}">
                    {{ $asset->status->name }}
                </span>
            </td>
            <td>
                <div class="btn-group btn-group-sm">
                    <a href="{{ route('assets.show', $asset) }}" class="btn btn-outline-info">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('assets.edit', $asset) }}" class="btn btn-outline-warning">
                        <i class="fas fa-edit"></i>
                    </a>
                </div>
            </td>
        </tr>
        @endforeach
    </x-slot>
    
    <x-slot name="bulkActions">
        <button class="btn btn-warning btn-block" @click="bulkAction('assign')">
            <i class="fas fa-user-check"></i> Assign Selected
        </button>
        <button class="btn btn-success btn-block" @click="bulkAction('export')">
            <i class="fas fa-download"></i> Export Selected
        </button>
        <button class="btn btn-danger btn-block" @click="bulkAction('delete')">
            <i class="fas fa-trash"></i> Delete Selected
        </button>
    </x-slot>
</x-enhanced-table>
```

---

## 5. 📱 MOBILE-RESPONSIVE NAVIGATION

### Problem: Sidebar not mobile-friendly

### Solution: Mobile-Optimized Navigation

```blade
{{-- resources/views/layouts/partials/mobile-nav.blade.php --}}
<nav class="mobile-nav" x-data="{ open: false, activeMenu: null }">
    {{-- Mobile Header --}}
    <div class="mobile-header">
        <button @click="open = !open" class="menu-toggle">
            <i class="fas" :class="open ? 'fa-times' : 'fa-bars'"></i>
        </button>
        <div class="logo">
            <img src="/img/logo.png" alt="ITQuty">
        </div>
        <div class="header-actions">
            <button class="btn-icon" @click="$dispatch('open-search')">
                <i class="fas fa-search"></i>
            </button>
            <button class="btn-icon" @click="$dispatch('open-notifications')">
                <i class="fas fa-bell"></i>
                <span class="badge">3</span>
            </button>
        </div>
    </div>
    
    {{-- Slide-out Menu --}}
    <div class="mobile-menu" :class="{ 'open': open }">
        <div class="menu-overlay" @click="open = false" x-show="open"></div>
        <div class="menu-content" x-show="open" x-transition>
            {{-- User Profile --}}
            <div class="menu-profile">
                <img src="{{ auth()->user()->avatar ?? '/img/default-avatar.png' }}" 
                     alt="{{ auth()->user()->name }}" 
                     class="profile-avatar">
                <div>
                    <div class="profile-name">{{ auth()->user()->name }}</div>
                    <div class="profile-role">{{ auth()->user()->roles->first()->name ?? 'User' }}</div>
                </div>
            </div>
            
            {{-- Menu Items --}}
            <div class="menu-items">
                {{-- Dashboard --}}
                <a href="{{ url('home') }}" class="menu-item">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                
                {{-- Tickets --}}
                <div class="menu-item" @click="activeMenu = activeMenu === 'tickets' ? null : 'tickets'">
                    <div class="menu-item-header">
                        <i class="fas fa-ticket"></i>
                        <span>Tickets</span>
                        <i class="fas fa-chevron-down" 
                           :class="{ 'rotate-180': activeMenu === 'tickets' }"></i>
                    </div>
                </div>
                <div x-show="activeMenu === 'tickets'" x-collapse class="submenu">
                    <a href="{{ url('/tickets') }}" class="submenu-item">All Tickets</a>
                    <a href="{{ url('/tickets/create') }}" class="submenu-item">Create Ticket</a>
                    <a href="{{ url('/tickets/unassigned') }}" class="submenu-item">Unassigned</a>
                </div>
                
                {{-- Assets --}}
                <div class="menu-item" @click="activeMenu = activeMenu === 'assets' ? null : 'assets'">
                    <div class="menu-item-header">
                        <i class="fas fa-desktop"></i>
                        <span>Assets</span>
                        <i class="fas fa-chevron-down" 
                           :class="{ 'rotate-180': activeMenu === 'assets' }"></i>
                    </div>
                </div>
                <div x-show="activeMenu === 'assets'" x-collapse class="submenu">
                    <a href="{{ url('/assets') }}" class="submenu-item">All Assets</a>
                    <a href="{{ url('/assets/create') }}" class="submenu-item">Add Asset</a>
                    <a href="{{ url('/asset-maintenance') }}" class="submenu-item">Maintenance</a>
                </div>
                
                {{-- Daily Activities --}}
                <a href="{{ url('/daily-activities') }}" class="menu-item">
                    <i class="fas fa-calendar-check"></i>
                    <span>Daily Activities</span>
                </a>
                
                {{-- Reports --}}
                <a href="{{ url('/kpi/dashboard') }}" class="menu-item">
                    <i class="fas fa-chart-line"></i>
                    <span>KPI Dashboard</span>
                </a>
            </div>
            
            {{-- Footer Actions --}}
            <div class="menu-footer">
                <a href="{{ url('/profile') }}" class="footer-link">
                    <i class="fas fa-user-cog"></i> Profile Settings
                </a>
                <a href="{{ url('/logout') }}" class="footer-link text-danger"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </div>
</nav>

<style>
.mobile-nav {
    display: none;
}

@media (max-width: 768px) {
    .mobile-nav {
        display: block;
    }
    
    .main-sidebar {
        display: none !important;
    }
}

.mobile-header {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 60px;
    background: white;
    border-bottom: 1px solid #eee;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 15px;
    z-index: 1000;
}

.menu-toggle {
    background: none;
    border: none;
    font-size: 24px;
    color: #333;
    padding: 10px;
}

.logo img {
    height: 40px;
}

.header-actions {
    display: flex;
    gap: 10px;
}

.btn-icon {
    position: relative;
    background: none;
    border: none;
    font-size: 20px;
    color: #666;
    padding: 10px;
}

.btn-icon .badge {
    position: absolute;
    top: 5px;
    right: 5px;
    background: #e74a3b;
    color: white;
    font-size: 10px;
    padding: 2px 6px;
    border-radius: 10px;
}

.menu-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1001;
}

.menu-content {
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    width: 280px;
    background: white;
    z-index: 1002;
    overflow-y: auto;
    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
}

.menu-profile {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.profile-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    border: 2px solid white;
}

.profile-name {
    font-weight: 600;
    font-size: 16px;
}

.profile-role {
    font-size: 13px;
    opacity: 0.9;
}

.menu-items {
    padding: 10px 0;
}

.menu-item {
    display: block;
    padding: 12px 20px;
    color: #333;
    text-decoration: none;
    transition: background 0.2s;
}

.menu-item:hover {
    background: #f8f9fa;
}

.menu-item-header {
    display: flex;
    align-items: center;
    gap: 12px;
}

.menu-item-header i:last-child {
    margin-left: auto;
    transition: transform 0.3s;
}

.menu-item-header i.rotate-180 {
    transform: rotate(180deg);
}

.submenu {
    background: #f8f9fa;
    border-left: 3px solid #4e73df;
}

.submenu-item {
    display: block;
    padding: 10px 20px 10px 50px;
    color: #666;
    text-decoration: none;
    font-size: 14px;
}

.submenu-item:hover {
    background: #e9ecef;
}

.menu-footer {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 20px;
    border-top: 1px solid #eee;
    background: white;
}

.footer-link {
    display: block;
    padding: 10px;
    color: #666;
    text-decoration: none;
    font-size: 14px;
}

.footer-link:hover {
    background: #f8f9fa;
}
</style>
```

---

## IMPLEMENTATION CHECKLIST

### Week 1: Foundation
- [ ] Install Alpine.js: `npm install alpinejs`
- [ ] Add CDN: Chart.js, SweetAlert2
- [ ] Create component directory structure
- [ ] Set up Tailwind CSS or continue with Bootstrap 5

### Week 2-3: Core Components
- [ ] Implement global search
- [ ] Create smart form components
- [ ] Build enhanced data table
- [ ] Add mobile navigation

### Week 4: Polish
- [ ] Add loading states
- [ ] Implement dark mode
- [ ] Add keyboard shortcuts
- [ ] Optimize for mobile

---

*Ready to implement? Start with the global search component - it will have the biggest immediate impact!*

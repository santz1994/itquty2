# Phase 3.4 - Advanced Filtering - Implementation Plan & Analysis

**Date:** October 30, 2025  
**Status:** Planning Phase  
**Estimated Duration:** 2-3 hours  
**Complexity:** Medium

---

## 🧠 Deep Analysis & Strategic Approach

### Current State Assessment

#### What Already Exists (From Phase 3.3 & Earlier)
```php
// Search endpoints with basic filtering
GET /api/v1/assets/search?q=keyword&status_id=1&division_id=2

// Sorting capabilities
GET /api/v1/assets?sort_by=status&sort_order=asc

// Pagination
GET /api/v1/assets?per_page=20&page=1

// Individual field filters
GET /api/v1/assets?status_id=1
GET /api/v1/assets?division_id=2
GET /api/v1/tickets?assigned_to=5
```

#### What We Need (Phase 3.4 Requirements)
```php
// Complex date range filtering
GET /api/v1/assets?date_from=2025-01-01&date_to=2025-12-31

// Multi-select filters (multiple IDs)
GET /api/v1/assets?status_id[]=1&status_id[]=2&status_id[]=3
GET /api/v1/tickets?priority_id[]=1&priority_id[]=2

// Location hierarchy filtering
GET /api/v1/assets?location_id=5&include_sublocation=true

// Combined complex queries
GET /api/v1/assets?date_from=2025-01-01&status_id[]=1,2,3&division_id=2&location_id=5

// Range filtering (price, warranty)
GET /api/v1/assets?price_min=1000&price_max=5000

// Filter presets
GET /api/v1/assets/filter-presets (list available presets)
POST /api/v1/assets/filter-presets (save custom filter)

// Filter builder
GET /api/v1/filters/builder (get filter building blocks)
```

---

## 🏗️ Architecture Design

### Component 1: FilterBuilder Trait
**Purpose:** Centralize complex filtering logic

```php
// Location: app/Traits/FilterBuilder.php

trait FilterBuilder {
    // Scope methods for each filter type
    public function filterByDateRange($startDate, $endDate, $column = 'created_at')
    public function filterByMultipleIds($ids, $column)
    public function filterByRangeValue($min, $max, $column)
    public function filterByLocationHierarchy($locationId, $includeSublocations = false)
    public function filterByStatus($statusIds)  // Single or multiple
    public function filterByPriority($priorityIds)
    public function filterByDivision($divisionIds)
    
    // Helper methods
    public function applyFilters($filterArray)  // Apply multiple filters at once
    public function getAvailableFilters()       // Get list of filterable columns
    public function getFilterOptions($column)   // Get possible values for filter
}
```

**Key Features:**
- Chainable scope methods
- Type conversion & validation
- NULL handling
- Complex relationship filtering
- Performance optimized (uses indexes where applicable)

### Component 2: FilterRequest Validators
**Purpose:** Validate filter parameters before processing

```php
// Location: app/Http/Requests/AssetFilterRequest.php
// Location: app/Http/Requests/TicketFilterRequest.php

class AssetFilterRequest extends FormRequest {
    public function rules(): array {
        return [
            'date_from' => 'nullable|date_format:Y-m-d|before_or_equal:date_to',
            'date_to' => 'nullable|date_format:Y-m-d|after_or_equal:date_from',
            'status_id' => 'nullable|array',
            'status_id.*' => 'integer|exists:statuses,id',
            'division_id' => 'nullable|array',
            'division_id.*' => 'integer|exists:divisions,id',
            'location_id' => 'nullable|integer|exists:locations,id',
            'include_sublocation' => 'nullable|boolean',
            'manufacturer_id' => 'nullable|array',
            'manufacturer_id.*' => 'integer|exists:manufacturers,id',
            'price_min' => 'nullable|numeric|min:0',
            'price_max' => 'nullable|numeric|min:0|gte:price_min',
            'warranty_months_min' => 'nullable|integer|min:0',
            'warranty_months_max' => 'nullable|integer|min:0|gte:warranty_months_min',
            'per_page' => 'nullable|integer|between:1,50',
            'page' => 'nullable|integer|min:1',
        ];
    }
}
```

### Component 3: Enhanced Controllers
**Purpose:** Add advanced filtering to existing controllers

```php
// AssetController@index with advanced filters
public function index(AssetFilterRequest $request) {
    $filters = $request->validated();
    
    $query = Asset::withNestedRelations();
    
    // Apply filters
    $query->applyFilters($filters);
    
    // Sorting
    $query->sortBy(
        $request->get('sort_by', 'id'),
        $request->get('sort_order', 'desc')
    );
    
    // Pagination
    $perPage = min($filters['per_page'] ?? 15, 50);
    return response()->json($query->paginate($perPage));
}
```

### Component 4: Filter Options Endpoints
**Purpose:** Get available filter values for UI dropdowns

```php
// New endpoints:
GET /api/v1/assets/filter-options/status    → [{"id": 1, "name": "Active"}, ...]
GET /api/v1/assets/filter-options/division  → [{"id": 1, "name": "IT"}, ...]
GET /api/v1/assets/filter-options/location  → [{"id": 1, "name": "Building A"}, ...]
GET /api/v1/tickets/filter-options/priority → [{"id": 1, "name": "Critical"}, ...]
```

### Component 5: Filter Preset System
**Purpose:** Allow users to save and reuse complex filters

```php
// Tables needed:
- filter_presets (id, name, user_id, resource_type, filters_json, created_at)

// Routes:
GET /api/v1/filter-presets?resource_type=asset    → List saved presets
POST /api/v1/filter-presets                        → Save new preset
GET /api/v1/filter-presets/{id}                    → Get preset details
DELETE /api/v1/filter-presets/{id}                 → Delete preset
PUT /api/v1/filter-presets/{id}                    → Update preset
```

---

## 📋 Implementation Tasks

### Task 1: Create FilterBuilder Trait (45 minutes)

**File:** `app/Traits/FilterBuilder.php`

**Methods to Implement:**

```php
/**
 * Filter by date range on specified column
 * @param Builder $query
 * @param string $startDate (Y-m-d format)
 * @param string $endDate (Y-m-d format)
 * @param string $column (default: created_at)
 * @return Builder
 */
public function scopeFilterByDateRange($query, $startDate, $endDate, $column = 'created_at')

/**
 * Filter by multiple IDs (IN clause)
 * @param Builder $query
 * @param array|string $ids (array or comma-separated string)
 * @param string $column
 * @return Builder
 */
public function scopeFilterByMultipleIds($query, $ids, $column)

/**
 * Filter by numeric range (MIN-MAX)
 * @param Builder $query
 * @param int|float $min
 * @param int|float $max
 * @param string $column
 * @return Builder
 */
public function scopeFilterByRangeValue($query, $min, $max, $column)

/**
 * Filter by location with optional sublocation inclusion
 * @param Builder $query
 * @param int $locationId
 * @param bool $includeSublocations
 * @return Builder
 */
public function scopeFilterByLocationHierarchy($query, $locationId, $includeSublocations = false)

/**
 * Apply multiple filters at once
 * @param Builder $query
 * @param array $filters
 * @return Builder
 */
public function scopeApplyFilters($query, $filters = [])

/**
 * Get available filters for this model
 * @return array
 */
public function getAvailableFilters()

/**
 * Get options for a filter (for dropdown)
 * @param string $filterName
 * @return Collection
 */
public function getFilterOptions($filterName)
```

### Task 2: Create Filter Request Validators (30 minutes)

**Files:**
- `app/Http/Requests/AssetFilterRequest.php`
- `app/Http/Requests/TicketFilterRequest.php`

**Features:**
- Date validation with cross-field checks
- Array validation for multi-select
- Foreign key existence checks
- Range validation (min <= max)
- Custom error messages

### Task 3: Create Filter Controller (45 minutes)

**File:** `app/Http/Controllers/API/FilterController.php`

**Methods:**
- `filterOptions()` - Return available options for a filter
- `getPresets()` - Get saved filter presets
- `savePreset()` - Save custom filter preset
- `deletePreset()` - Delete saved preset

### Task 4: Enhance Existing Controllers (30 minutes)

**Files:**
- `app/Http/Controllers/API/AssetController.php` - Update index() method
- `app/Http/Controllers/API/TicketController.php` - Update index() method

**Changes:**
- Use new FilterRequest validators
- Call applyFilters() for advanced filtering
- Handle multi-select parameters
- Response format unchanged (backward compatible)

### Task 5: Add Routes (15 minutes)

**File:** `routes/api.php`

**Routes to Add:**
```php
// Filter options (for UI dropdowns)
Route::get('/assets/filter-options/{filter}', [FilterController::class, 'filterOptions']);
Route::get('/tickets/filter-options/{filter}', [FilterController::class, 'filterOptions']);

// Filter presets
Route::get('/filter-presets', [FilterController::class, 'getPresets']);
Route::post('/filter-presets', [FilterController::class, 'savePreset']);
Route::get('/filter-presets/{id}', [FilterController::class, 'getPreset']);
Route::put('/filter-presets/{id}', [FilterController::class, 'updatePreset']);
Route::delete('/filter-presets/{id}', [FilterController::class, 'deletePreset']);
```

### Task 6: Create Migration for Filter Presets (15 minutes)

**File:** `database/migrations/YYYY_MM_DD_create_filter_presets_table.php`

**Table Structure:**
```php
Schema::create('filter_presets', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('user_id');
    $table->string('name');
    $table->string('resource_type'); // 'asset', 'ticket'
    $table->json('filters');
    $table->timestamps();
    
    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    $table->index(['user_id', 'resource_type']);
});
```

### Task 7: Create Documentation (30 minutes)

**Files:**
- `docs/PHASE_3_4_PLAN.md` - Architecture and planning
- `docs/PHASE_3_4_TESTING.md` - Test cases with examples
- `docs/PHASE_3_4_COMPLETE.md` - Implementation report

---

## 🔍 Detailed Feature Analysis

### Feature 1: Date Range Filtering

**Use Cases:**
```
Assets purchased between dates:
GET /api/v1/assets?date_from=2024-01-01&date_to=2024-12-31&date_column=purchase_date

Tickets created in date range:
GET /api/v1/tickets?date_from=2025-10-01&date_to=2025-10-31&date_column=created_at

Warranties expiring soon:
GET /api/v1/assets?date_from=2025-11-01&date_to=2025-12-31&date_column=warranty_expiry
```

**Implementation:**
```php
public function scopeFilterByDateRange($query, $startDate, $endDate, $column = 'created_at') {
    if (empty($startDate) && empty($endDate)) {
        return $query;
    }
    
    if (!empty($startDate)) {
        $query->whereDate($column, '>=', $startDate);
    }
    
    if (!empty($endDate)) {
        $query->whereDate($column, '<=', $endDate);
    }
    
    return $query;
}
```

### Feature 2: Multi-Select Filtering

**Use Cases:**
```
Assets with multiple statuses:
GET /api/v1/assets?status_id[]=1&status_id[]=2&status_id[]=3

Tickets with multiple priorities:
GET /api/v1/tickets?priority_id[]=1&priority_id[]=2

Multiple divisions:
GET /api/v1/assets?division_id[]=1&division_id[]=2
```

**Implementation:**
```php
public function scopeFilterByMultipleIds($query, $ids, $column) {
    if (empty($ids)) {
        return $query;
    }
    
    // Handle comma-separated string
    if (is_string($ids)) {
        $ids = array_map('trim', explode(',', $ids));
    }
    
    // Ensure all are integers
    $ids = array_filter(array_map('intval', (array)$ids));
    
    if (empty($ids)) {
        return $query;
    }
    
    return $query->whereIn($column, $ids);
}
```

### Feature 3: Range Filtering

**Use Cases:**
```
Assets between price range:
GET /api/v1/assets?price_min=1000&price_max=5000

Warranties by months:
GET /api/v1/assets?warranty_months_min=12&warranty_months_max=36

Assets by age:
GET /api/v1/assets?age_years_min=1&age_years_max=5
```

**Implementation:**
```php
public function scopeFilterByRangeValue($query, $min, $max, $column) {
    if ($min !== null && $min !== '') {
        $query->where($column, '>=', (float)$min);
    }
    
    if ($max !== null && $max !== '') {
        $query->where($column, '<=', (float)$max);
    }
    
    return $query;
}
```

### Feature 4: Location Hierarchy Filtering

**Use Cases:**
```
Assets at specific location only:
GET /api/v1/assets?location_id=5

Assets at location and all sublocations:
GET /api/v1/assets?location_id=5&include_sublocation=true

Building A and all rooms inside:
GET /api/v1/assets?location_id=1&include_sublocation=true
```

**Implementation:**
```php
public function scopeFilterByLocationHierarchy($query, $locationId, $includeSublocations = false) {
    if (empty($locationId)) {
        return $query;
    }
    
    if (!$includeSublocations) {
        return $query->where('location_id', $locationId);
    }
    
    // Get location and all child locations
    $locationIds = Location::where('id', $locationId)
        ->orWhere('parent_location_id', $locationId)
        ->pluck('id')
        ->toArray();
    
    return $query->whereIn('location_id', $locationIds);
}
```

### Feature 5: Complex Multi-Filter Application

**Use Cases:**
```
All filters combined:
GET /api/v1/assets?date_from=2025-01-01&status_id[]=1,2&division_id[]=1&location_id=5&price_min=500&price_max=5000

Tickets complex filter:
GET /api/v1/tickets?date_from=2025-10-01&priority_id[]=1,2&assigned_to[]=3,4,5&status_id[]=1,2
```

**Implementation:**
```php
public function scopeApplyFilters($query, $filters = []) {
    // Date range filtering
    if (!empty($filters['date_from']) || !empty($filters['date_to'])) {
        $dateColumn = $filters['date_column'] ?? 'created_at';
        $query->filterByDateRange(
            $filters['date_from'] ?? null,
            $filters['date_to'] ?? null,
            $dateColumn
        );
    }
    
    // Multi-select filtering
    if (!empty($filters['status_id'])) {
        $query->filterByMultipleIds($filters['status_id'], 'status_id');
    }
    
    if (!empty($filters['division_id'])) {
        $query->filterByMultipleIds($filters['division_id'], 'division_id');
    }
    
    // Range filtering
    if (isset($filters['price_min']) || isset($filters['price_max'])) {
        $query->filterByRangeValue(
            $filters['price_min'] ?? null,
            $filters['price_max'] ?? null,
            'purchase_price' // or appropriate column
        );
    }
    
    // Location hierarchy
    if (!empty($filters['location_id'])) {
        $query->filterByLocationHierarchy(
            $filters['location_id'],
            $filters['include_sublocation'] ?? false
        );
    }
    
    return $query;
}
```

### Feature 6: Filter Options (For UI)

**Purpose:** Populate dropdowns with available filter values

**Endpoints:**
```
GET /api/v1/assets/filter-options/status
Response:
{
  "options": [
    {"id": 1, "name": "Active", "count": 45},
    {"id": 2, "name": "Inactive", "count": 12},
    {"id": 15, "name": "In Maintenance", "count": 3}
  ]
}

GET /api/v1/assets/filter-options/division
Response:
{
  "options": [
    {"id": 1, "name": "IT", "count": 32},
    {"id": 2, "name": "HR", "count": 18},
    {"id": 3, "name": "Finance", "count": 10}
  ]
}
```

**Implementation:**
```php
public function filterOptions(Request $request, $filter) {
    $resourceType = $request->get('resource_type', 'asset');
    $model = $resourceType === 'asset' ? new Asset() : new Ticket();
    
    return response()->json([
        'filter' => $filter,
        'options' => $model->getFilterOptions($filter),
    ]);
}
```

### Feature 7: Filter Presets

**Use Cases:**
```
User saves complex filter as "My Favorite Assets":
POST /api/v1/filter-presets
{
  "name": "My Favorite Assets",
  "resource_type": "asset",
  "filters": {
    "status_id": [1],
    "division_id": [1, 2],
    "location_id": 5,
    "date_from": "2025-01-01"
  }
}

User lists saved presets:
GET /api/v1/filter-presets?resource_type=asset

User applies saved preset:
GET /api/v1/assets?preset_id=42
```

**Implementation:**
```php
// Save preset
public function savePreset(FilterPresetRequest $request) {
    $preset = FilterPreset::create([
        'user_id' => auth()->id(),
        'name' => $request->name,
        'resource_type' => $request->resource_type,
        'filters' => $request->filters,
    ]);
    
    return response()->json($preset);
}

// Apply preset
public function index(AssetFilterRequest $request) {
    if ($request->has('preset_id')) {
        $preset = FilterPreset::find($request->preset_id);
        $filters = array_merge($preset->filters, $request->validated());
    } else {
        $filters = $request->validated();
    }
    
    // Continue with filtering...
}
```

---

## 📊 Query Optimization Strategy

### Index Strategy

**Existing Indexes:**
```
FULLTEXT (name, description, asset_tag, serial_number)
FULLTEXT (subject, description, ticket_code)
```

**New Indexes Needed:**
```sql
-- Asset indexes for filtering
INDEX (status_id)
INDEX (division_id)
INDEX (location_id)
INDEX (created_at, status_id)
INDEX (purchase_date, division_id)
INDEX (warranty_expiry)

-- Ticket indexes for filtering
INDEX (status_id)
INDEX (priority_id)
INDEX (created_at, status_id)
INDEX (assigned_to)
```

### Query Plan Optimization

**Before:** 
```sql
SELECT * FROM assets 
WHERE status_id IN (1,2,3) 
AND division_id IN (1,2) 
AND location_id = 5 
AND purchase_date >= '2025-01-01'
-- Queries all rows then filters
```

**After:**
```sql
SELECT * FROM assets 
WHERE purchase_date >= '2025-01-01'  -- Most selective first
AND location_id = 5
AND division_id IN (1,2)
AND status_id IN (1,2,3)
-- Uses index on purchase_date, then narrows down
```

---

## 🧪 Test Cases (Planned)

### Asset Filter Tests (10 cases)
```
1. Date range filter (valid range)
2. Date range filter (invalid range - date_from > date_to)
3. Multi-select status (multiple IDs)
4. Multi-select division (multiple IDs)
5. Combined date + multi-select
6. Location hierarchy (without sublocations)
7. Location hierarchy (with sublocations)
8. Range filtering (price min-max)
9. Complex multi-filter
10. Invalid filter parameters
```

### Ticket Filter Tests (5 cases)
```
11. Multi-select priority
12. Multi-select status
13. Date range (created_at)
14. Assigned to (multiple users)
15. Complex ticket filter
```

### Filter Options Tests (3 cases)
```
16. Get status filter options
17. Get division filter options
18. Invalid filter name
```

### Filter Preset Tests (4 cases)
```
19. Save filter preset
20. Load filter preset
21. Update filter preset
22. Delete filter preset
```

---

## 📈 Performance Benchmarks

**Target Performance:**
```
Date range query:        <50ms
Multi-select query:      <50ms
Complex filter query:    <100ms
Location hierarchy:      <75ms
Filter options:          <30ms
```

---

## 🚀 Implementation Timeline

```
Task 1: FilterBuilder Trait        - 45 min
Task 2: Request Validators         - 30 min
Task 3: FilterController           - 45 min
Task 4: Controller Updates         - 30 min
Task 5: Routes                     - 15 min
Task 6: Migration                  - 15 min
Task 7: Documentation              - 30 min
Testing & Validation               - 30 min
---
TOTAL:                            3 hours 15 min
```

---

## 🎯 Success Criteria

✅ All filter types working  
✅ Performance <100ms for complex queries  
✅ Backward compatible  
✅ Request validation active  
✅ Test cases passing  
✅ Documentation complete  
✅ No breaking changes  

---

## 📌 Implementation Order

1. **FilterBuilder Trait** - Core filtering logic
2. **Request Validators** - Input validation
3. **FilterController** - Filter options & presets
4. **Controller Updates** - Use new validators
5. **Routes** - Register all routes
6. **Migration** - Create presets table
7. **Documentation** - Comprehensive docs
8. **Testing** - Verify all features

---

*Plan Created: October 30, 2025*  
*Phase 3.4 Ready for Implementation*


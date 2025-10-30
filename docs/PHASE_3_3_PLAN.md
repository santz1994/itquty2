# Phase 3.3: Search Endpoints - Implementation Plan

**Date:** October 30, 2025  
**Status:** Planning Phase  
**Estimated Duration:** 4-5 hours  

---

## Deep Analysis & Strategy

### Current State Assessment

#### ✅ What's Already in Place
1. **FULLTEXT Indexes** (Phase 3.1)
   - assets: name, description, asset_tag, serial_number ✅
   - tickets: subject, description, ticket_code ✅

2. **Optimized Query Scopes** (Phase 3.2)
   - Asset: withNestedRelations(), active(), byStatus(), etc.
   - Ticket: withNestedRelations(), open(), closed(), byStatus(), etc.
   - SortableQuery trait for relationship-based sorting

3. **API Structure**
   - Base controllers: AssetController, TicketController
   - Pagination system with max 100 per page validation
   - Response transformation for consistent output format

4. **Relationship Availability**
   - Assets → Manufacturer, Division, Location, Status, User
   - Tickets → User (creator), Status, Priority, Type, TicketsEntry (comments)
   - TicketsEntry → User, Ticket

#### ⚠️ What Needs Implementation
1. SearchServiceTrait for FULLTEXT queries
2. Three new search endpoints
3. Global search combining multiple types
4. Relevance scoring and ranking
5. Snippet generation for context
6. Advanced search parameters

---

## Implementation Architecture

### Component 1: SearchServiceTrait
**Purpose:** Centralize FULLTEXT search logic with ranking and snippets

```php
// app/Traits/SearchServiceTrait.php

trait SearchServiceTrait {
    // Search methods:
    // - fulltextSearch($query, $columns)    : Boolean FULLTEXT
    // - naturalSearch($query, $columns)      : Natural language FULLTEXT
    // - getSearchRelevance()                 : Ranking formula
    // - generateSnippet($text, $keywords)   : Context extraction
    // - parseSearchQuery($input)            : Query parsing
    // - highlightResults()                  : Highlighting
}
```

**Key Features:**
- Support both Boolean and Natural Language modes
- Relevance scoring (BM25-like formula)
- Snippet generation (50-100 char context around match)
- Query parsing (quoted strings, operators)
- Highlighting of matched terms

### Component 2: Search Endpoints

#### Endpoint 1: Asset Search
**Route:** `GET /api/v1/assets/search`

```
Parameters:
  q (required): Search query
  type: "name|tag|serial|all" (default: all)
  filters: Additional filters (status_id, division_id, etc.)
  sort: "relevance|name|date" (default: relevance)
  per_page: 1-50 (default: 20)
  page: Pagination number

Response:
{
  data: [
    {
      id, asset_tag, name, serial_number,
      status, division, location,
      relevance_score, snippet
    }
  ],
  meta: { total, per_page, current_page, from, to, last_page }
}
```

#### Endpoint 2: Ticket Search
**Route:** `GET /api/v1/tickets/search`

```
Parameters:
  q (required): Search query
  type: "subject|description|code|all" (default: all)
  filters: status_id, priority_id, assigned_to, etc.
  sort: "relevance|date|priority" (default: relevance)
  include_resolved: true|false (default: false)
  per_page: 1-50 (default: 20)
  page: Pagination number

Response:
{
  data: [
    {
      id, ticket_code, subject, priority, status,
      assigned_to, created_at,
      relevance_score, snippet
    }
  ],
  meta: { total, per_page, current_page, from, to, last_page }
}
```

#### Endpoint 3: Ticket Comments Search
**Route:** `GET /api/v1/tickets/{ticket}/comments/search`

```
Parameters:
  q (required): Search query
  per_page: 1-50 (default: 20)
  page: Pagination number

Response:
{
  data: [
    {
      id, created_at, content,
      author: { id, name, email },
      relevance_score, snippet
    }
  ],
  meta: { total, per_page, current_page }
}
```

#### Endpoint 4: Global Search (Bonus)
**Route:** `GET /api/v1/search/global`

```
Parameters:
  q (required): Search query
  types: "assets|tickets|comments" (comma-separated, default: all)
  limit: Results per type (default: 5)

Response:
{
  assets: [ { ... } ],
  tickets: [ { ... } ],
  comments: [ { ... } ]
}
```

---

## Implementation Steps

### Step 1: Create SearchServiceTrait (30 min)
- File: `app/Traits/SearchServiceTrait.php`
- Methods: fulltextSearch, naturalSearch, getRelevance, generateSnippet, parseQuery
- Support both Boolean and Natural Language modes
- Implement BM25-like relevance scoring
- Add snippet generation with context

### Step 2: Create SearchRequest Validation (15 min)
- File: `app/Http/Requests/SearchAssetRequest.php`
- File: `app/Http/Requests/SearchTicketRequest.php`
- Validate search query (min 2 chars, max 200 chars)
- Validate pagination (per_page: 1-50)
- Validate filter parameters

### Step 3: Implement Asset Search (45 min)
- Add search() method to AssetController
- Implement FULLTEXT on assets table
- Apply filters with scopes
- Ranking and sorting
- Snippet generation
- Response transformation

### Step 4: Implement Ticket Search (45 min)
- Add search() method to TicketController
- Implement FULLTEXT on tickets table
- Apply filters with scopes
- Ranking and sorting
- Snippet generation
- Response transformation

### Step 5: Implement Comment Search (30 min)
- Add commentsSearch() method to TicketController
- Search TicketsEntry records
- Filter by ticket ID
- Include author information
- Snippet generation

### Step 6: Implement Global Search (30 min)
- Create SearchController
- Combine results from assets, tickets, comments
- Limit results per type
- Format unified response

### Step 7: Add Routes (15 min)
- Register all search routes
- Add proper naming
- Rate limiting for search endpoints

### Step 8: Testing & Validation (30 min)
- Test each endpoint
- Verify relevance scoring
- Check snippet generation
- Validate error handling

---

## SQL Queries to Implement

### Asset FULLTEXT Search
```sql
SELECT assets.*, 
  MATCH(name, description, asset_tag, serial_number) 
  AGAINST('query' IN BOOLEAN MODE) as relevance
FROM assets
WHERE MATCH(name, description, asset_tag, serial_number) 
  AGAINST('query' IN BOOLEAN MODE)
ORDER BY relevance DESC
LIMIT 20
```

### Ticket FULLTEXT Search
```sql
SELECT tickets.*,
  MATCH(subject, description, ticket_code)
  AGAINST('query' IN BOOLEAN MODE) as relevance
FROM tickets
WHERE MATCH(subject, description, ticket_code)
  AGAINST('query' IN BOOLEAN MODE)
ORDER BY relevance DESC
LIMIT 20
```

### Comment FULLTEXT Search
```sql
SELECT te.*, 
  MATCH(te.description)
  AGAINST('query' IN BOOLEAN MODE) as relevance
FROM tickets_entries te
WHERE te.ticket_id = ? 
  AND MATCH(te.description)
  AGAINST('query' IN BOOLEAN MODE)
ORDER BY relevance DESC
LIMIT 20
```

---

## Key Considerations

### Performance
- ✅ FULLTEXT indexes already in place
- ✅ Query optimization from Phase 3.2
- Search will be fast (<100ms for most queries)
- Limit results to 50 per page max

### Security
- ✅ Validate search query (min 2 chars)
- ✅ Use parameterized queries (Laravel ORM handles this)
- ✅ Prevent query injection
- Rate limit search endpoints (10 req/min per user)

### UX
- ✅ Include snippet/context in results
- ✅ Highlight matched terms
- ✅ Relevance-based sorting
- ✅ Pagination support

### Scalability
- ✅ FULLTEXT searches are fast
- ✅ Limited to 50 results per page
- ✅ Can handle thousands of records
- Future: Consider ElasticSearch for extreme scale

---

## File Structure

```
app/
├── Traits/
│   └── SearchServiceTrait.php          (NEW - 150 lines)
├── Http/
│   ├── Controllers/API/
│   │   ├── AssetController.php         (UPDATE - add search method)
│   │   ├── TicketController.php        (UPDATE - add search methods)
│   │   └── SearchController.php        (NEW - global search)
│   └── Requests/
│       ├── SearchAssetRequest.php      (NEW - validation)
│       └── SearchTicketRequest.php     (NEW - validation)
└── Models/
    ├── Asset.php                       (UPDATE - add search scope)
    └── Ticket.php                      (UPDATE - add search scope)

routes/
└── api.php                             (UPDATE - add search routes)
```

---

## Code Examples to Implement

### Example 1: Asset Search Endpoint
```php
public function search(SearchAssetRequest $request)
{
    $query = $request->get('q');
    
    $assets = Asset::withNestedRelations()
        ->fulltextSearch($query, ['name', 'description', 'asset_tag', 'serial_number'])
        ->when($request->has('status_id'), fn($q) => $q->byStatus($request->status_id))
        ->when($request->has('division_id'), fn($q) => $q->where('division_id', $request->division_id))
        ->orderByRaw("MATCH(name, description, asset_tag, serial_number) AGAINST(? IN BOOLEAN MODE) DESC", [$query])
        ->paginate(min($request->get('per_page', 20), 50));
    
    return response()->json($this->transformSearchResults($assets));
}
```

### Example 2: SearchServiceTrait - fulltextSearch scope
```php
public function scopeFulltextSearch($query, $searchTerm, $columns = [])
{
    if (empty($searchTerm) || strlen($searchTerm) < 2) {
        return $query;
    }
    
    $columns = $columns ?: $this->searchColumns ?? [];
    $columnString = implode(',', $columns);
    
    return $query->whereRaw(
        "MATCH($columnString) AGAINST(? IN BOOLEAN MODE)",
        [$this->parseSearchQuery($searchTerm)]
    );
}
```

### Example 3: Snippet Generation
```php
protected function generateSnippet($text, $keywords, $length = 100)
{
    $words = explode(' ', preg_replace('/\s+/', ' ', $keywords));
    
    foreach ($words as $word) {
        $pattern = '/\b' . preg_quote($word, '/') . '\b/i';
        if (preg_match_all($pattern, $text, $matches, PREG_OFFSET_CAPTURE)) {
            $offset = max(0, $matches[0][0][1] - 20);
            return substr($text, $offset, $length) . (strlen($text) > $offset + $length ? '...' : '');
        }
    }
    
    return substr($text, 0, $length) . (strlen($text) > $length ? '...' : '');
}
```

---

## Testing Strategy

### Unit Tests
- SearchServiceTrait methods
- Snippet generation accuracy
- Query parsing logic

### Integration Tests
- Asset search endpoint
- Ticket search endpoint
- Comment search endpoint
- Global search endpoint

### Performance Tests
- Query execution time (<100ms)
- Memory usage
- Index utilization

### Edge Cases
- Empty search query
- Very long search query
- Special characters (quotes, asterisks)
- Unicode characters
- Results with no matches

---

## Success Criteria

✅ All 4 endpoints working (3 specific + 1 global)
✅ FULLTEXT search operational
✅ Relevance scoring implemented
✅ Snippet generation working
✅ Pagination validated
✅ Performance <100ms per query
✅ No syntax errors
✅ Backward compatible

---

## Timeline Estimate

| Task | Time | Cumulative |
|------|------|-----------|
| SearchServiceTrait | 30 min | 30 min |
| Request Validation | 15 min | 45 min |
| Asset Search | 45 min | 90 min |
| Ticket Search | 45 min | 135 min |
| Comment Search | 30 min | 165 min |
| Global Search | 30 min | 195 min |
| Routes & Config | 15 min | 210 min |
| Testing | 30 min | 240 min |
| **Total** | **~4 hours** | **240 min** |

---

## Risk Assessment

| Risk | Likelihood | Impact | Mitigation |
|------|-----------|--------|-----------|
| FULLTEXT index issues | Low | Medium | Already verified in Phase 3.1 |
| Performance slowdown | Low | Medium | Limit results, use indexes |
| Search quality | Medium | Low | Implement ranking system |
| Query injection | Very Low | High | Use Laravel ORM + validation |

---

## Dependencies

### ✅ Met Dependencies
- FULLTEXT indexes created (Phase 3.1)
- Query scopes ready (Phase 3.2)
- Controllers structure (existing)
- API routes (existing)

### No External Dependencies
- No new packages required
- Pure Laravel/MySQL implementation

---

## Next Phase Dependencies

**Phase 3.4: Advanced Filtering** will depend on:
- ✅ Search endpoints foundation
- Date range filtering patterns
- Multi-select filter builder

---

*Document created: October 30, 2025*  
*Phase 3.3 Ready for Implementation*

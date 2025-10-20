<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;
use Illuminate\Support\Facades\Cache;

class ArticleController extends Controller
{
    /**
     * Retrieve articles with search, filter, and user preferences.
     */
    public function index(Request $request)
{
    $query = Article::query();

    /*
    |--------------------------------------------------------------------------
    | 1. Search Query
    |--------------------------------------------------------------------------
    */
    if ($request->filled('search')) {
        $search = trim($request->input('search'));
        $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('content', 'like', "%{$search}%");
        });
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Filtering Criteria (single-value filters)
    |--------------------------------------------------------------------------
    */
    $filters = [
        'from_date' => fn($q, $value) => $q->where('published_at', '>=', $value),
        'to_date'   => fn($q, $value) => $q->where('published_at', '<=', $value),
        'category'  => fn($q, $value) => $q->whereHas('categories', fn($qq) => $qq->where('name', $value)),
        'source'    => fn($q, $value) => $q->where('source', $value),
        'author'    => fn($q, $value) => $q->whereHas('authors', fn($qq) => $qq->where('name', $value)),
    ];

    foreach ($filters as $key => $callback) {
        if ($request->filled($key)) {
            $callback($query, $request->input($key));
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 3. User Preferences (multi-value filters)
    |--------------------------------------------------------------------------
    */
    $multiFilters = [
        'sources'    => fn($q, $values) => $q->whereIn('source', (array) $values),
        'categories' => fn($q, $values) => $q->whereHas('categories', fn($qq) => $qq->whereIn('name', (array) $values)),
        'authors'    => fn($q, $values) => $q->whereHas('authors', fn($qq) => $qq->whereIn('name', (array) $values)),
    ];

    foreach ($multiFilters as $key => $callback) {
        $values = $request->input($key);

        // ✅ apply filter only if array is non-empty after removing blank/null values
        if (is_array($values)) {
            $values = array_filter($values, fn($v) => !empty($v));
            if (count($values) > 0) {
                $callback($query, $values);
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 4. Pagination & Response
    |--------------------------------------------------------------------------
    */
    $perPage = (int) $request->input('per_page', 20);

    $articles = $query
        ->with(['authors', 'categories'])
        ->paginate($perPage);

    return response()->json($articles);
}
    

}
<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;


class ProductController extends Controller
{
    /**
     * WITHOUT cache - slow
     */

    public function indexWithoutCache(): JsonResponse
    {
        $products = \App\Models\Product::where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'count' => $products->count(),
            'data' => $products,
        ]);
    }

    /**
     * WITH cache - fast
     */
    public function indexWithCache(): JsonResponse
    {
        $products = cache()->remember('products:active', now()->addMinutes(15), function () {
            return Product::where('is_active', true)
                ->where('stock_quantity', '>', 0)
                ->orderBy('created_at', 'desc')
                ->get()
                ->toArray();  // <-- serialize as array, not Collection object
        });

        return response()->json([
            'count' => count($products),
            'cached' => true,
            'data' => $products
        ]);
    }

    /**
     * Clear cache when product updated
     */

    public function clearCache(): JsonResponse
    {
        cache()->forget('products:active');

        return response()->json([
            'message' => 'Product cache cleared',
        ]);
    }
}

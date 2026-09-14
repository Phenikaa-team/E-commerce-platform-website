<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Services\ProductFilterService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function __construct(
        protected ProductFilterService $filterService
    ) {}

    /**
     * Display product search results.
     */
    public function search(Request $request): View
    {
        $q = trim((string) $request->input('q', ''));
        $data = $this->filterService->paginate($request);

        $title = $q !== ''
            ? "Kết quả tìm kiếm cho \"{$q}\""
            : 'Tất cả sản phẩm';

        return view('catalog.listing', array_merge($data, [
            'pageType' => 'search',
            'pageTitle' => $title,
            'searchTerm' => $q,
            'breadcrumbs' => [
                ['label' => 'Trang chủ', 'url' => route('home')],
                ['label' => 'Tìm kiếm', 'url' => route('catalog.search', ['q' => $q])],
            ],
            'currentCategory' => null,
            'currentBrand' => null,
        ]));
    }

    /**
     * Display products for a specific category.
     */
    public function category(Request $request, string $slug): View
    {
        $category = Category::with('children')
            ->where('slug', $slug)
            ->firstOrFail();

        // Merge category slug into request so filter service processes it
        $request->merge(['category' => $category->slug]);
        $data = $this->filterService->paginate($request);

        // Build category breadcrumb chain if parent exists
        $breadcrumbs = [
            ['label' => 'Trang chủ', 'url' => route('home')],
        ];
        if ($category->parent_id && $category->parent) {
            $breadcrumbs[] = [
                'label' => $category->parent->name,
                'url' => route('catalog.category', $category->parent->slug),
            ];
        }
        $breadcrumbs[] = [
            'label' => $category->name,
            'url' => route('catalog.category', $category->slug),
        ];

        return view('catalog.listing', array_merge($data, [
            'pageType' => 'category',
            'pageTitle' => $category->name,
            'searchTerm' => (string) $request->input('q', ''),
            'currentCategory' => $category,
            'currentBrand' => null,
            'breadcrumbs' => $breadcrumbs,
        ]));
    }

    /**
     * Display products for a specific brand.
     */
    public function brand(Request $request, string $brand): View
    {
        // Decode brand from URL
        $brandName = urldecode($brand);

        // Merge brand into request so filter service processes it
        $request->merge(['brand' => $brandName]);
        $data = $this->filterService->paginate($request);

        return view('catalog.listing', array_merge($data, [
            'pageType' => 'brand',
            'pageTitle' => "Thương hiệu: {$brandName}",
            'searchTerm' => (string) $request->input('q', ''),
            'currentCategory' => null,
            'currentBrand' => $brandName,
            'breadcrumbs' => [
                ['label' => 'Trang chủ', 'url' => route('home')],
                ['label' => 'Thương hiệu', 'url' => route('catalog.search')],
                ['label' => $brandName, 'url' => route('catalog.brand', $brandName)],
            ],
        ]));
    }
}

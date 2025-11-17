<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Pool; // 👈 1. Import Pool

class MotorcycleController extends Controller
{
    private $apiUrl;

    public function __construct()
    {
        $this->apiUrl = rtrim(config('app.be_api_url'), '/');
    }

    /**
     * TỐI ƯU: Helper tạo API call (Client-side)
     */
    private function clientApi(): PendingRequest
    {
        return Http::baseUrl($this->apiUrl . '/api/client')
            ->timeout(10);
    }

    /**
     * TỐI ƯU: Chuẩn hóa logic lấy URL ảnh (fix bug/inconsistency)
     */
    private function formatProductImageUrl(array &$product)
    {
        // Ưu tiên image_url (đã có URL đầy đủ)
        if (!empty($product['image_url'])) {
            $product['image_url'] = $product['image_url'];
        }
        // Nếu không có, tự tạo từ cột 'image'
        elseif (!empty($product['image'])) {
            $product['image_url'] = $this->apiUrl . '/storage/' . $product['image'];
        }
        // Nếu không có cả hai
        else {
            $product['image_url'] = null; // hoặc ảnh placeholder
        }
    }


    /**
     * TỐI ƯU: Dùng Http::pool() chạy song song
     */
    public function index(Request $request)
    {
        $viewData = [
            'products' => [],
            'brands' => [],
            'categories' => [],
            'pagination' => null,
            'paginationLinks' => null,
            'error' => null,
            'minPrice' => 0,
            'maxPrice' => 100000000
        ];

        try {
            // Chuẩn bị params
            $limit = $request->get('limit', 15);
            $allowedLimits = [5, 10, 15, 20];
            $params = $request->query();
            
            // Backend API nhận 'per_page', không phải 'limit'
            unset($params['limit']); // Xóa 'limit' nếu có
            $params['per_page'] = in_array($limit, $allowedLimits) ? $limit : 15;
            
            Log::info('Motorcycles Controller - Request params:', [
                'original_limit' => $limit,
                'per_page' => $params['per_page'],
                'all_params' => $params
            ]);

            // Chạy 4 request CÙNG LÚC với retry và timeout
            $responses = Http::pool(fn(Pool $pool) => [
                $pool->as('products')
                    ->baseUrl($this->apiUrl . '/api/client')
                    ->timeout(15)
                    ->retry(2, 100)
                    ->get('/products', $params),
                $pool->as('brands')
                    ->baseUrl($this->apiUrl . '/api/client')
                    ->timeout(15)
                    ->retry(2, 100)
                    ->get('/brands'),
                $pool->as('categories')
                    ->baseUrl($this->apiUrl . '/api/client')
                    ->timeout(15)
                    ->retry(2, 100)
                    ->get('/categories'),
                $pool->as('priceRange')
                    ->baseUrl($this->apiUrl . '/api/client')
                    ->timeout(15)
                    ->retry(2, 100)
                    ->get('/products/price-range'),
            ]);

            // Xử lý Products
            $productsResponse = $responses['products'];
            if (!($productsResponse instanceof \Throwable) && $productsResponse->successful()) {
                $data = $productsResponse->json();
                $viewData['products'] = $data['data'] ?? [];
                $viewData['pagination'] = $data['meta'] ?? null;
                $viewData['paginationLinks'] = $data['links'] ?? null;
                
                Log::info('Motorcycles Controller - Response:', [
                    'total' => $data['meta']['total'] ?? 0,
                    'per_page' => $data['meta']['per_page'] ?? 0,
                    'current_page' => $data['meta']['current_page'] ?? 0,
                    'count' => count($data['data'] ?? [])
                ]);

                foreach ($viewData['products'] as &$product) {
                    $this->formatProductImageUrl($product);
                }
            } else {
                if ($productsResponse instanceof \Throwable) {
                    Log::error('Motorcycles API Error: ' . $productsResponse->getMessage());
                } else {
                    Log::error('Motorcycles API Error: HTTP ' . $productsResponse->status());
                }
                $viewData['error'] = 'Không thể tải danh sách xe máy';
            }

            // Xử lý Brands
            $brandsResponse = $responses['brands'];
            if (!($brandsResponse instanceof \Throwable) && $brandsResponse->successful()) {
                $viewData['brands'] = $brandsResponse->json('data', []);
            }

            // Xử lý Categories
            $categoriesResponse = $responses['categories'];
            if (!($categoriesResponse instanceof \Throwable) && $categoriesResponse->successful()) {
                $viewData['categories'] = $categoriesResponse->json('data', []);
            }

            // Xử lý Price Range
            $priceRangeResponse = $responses['priceRange'];
            if (!($priceRangeResponse instanceof \Throwable) && $priceRangeResponse->successful()) {
                $priceData = $priceRangeResponse->json('data', []);
                $viewData['minPrice'] = $priceData['min_price'] ?? 0;
                $viewData['maxPrice'] = $priceData['max_price'] ?? 100000000;
            } else {
                $viewData['minPrice'] = 0;
                $viewData['maxPrice'] = 100000000;
            }

            return view('client.motorcycles', $viewData);
        } catch (\Exception $e) {
            Log::error('Motorcycles Controller Error: ' . $e->getMessage());
            $viewData['error'] = 'Không thể tải dữ liệu từ server';
            return view('client.motorcycles', $viewData);
        }
    }

    /**
     * TỐI ƯU: Đã dọn dẹp, nhưng vẫn phải chạy tuần tự
     * (getRelatedProducts phụ thuộc vào $product)
     */
    public function show($id)
    {
        try {
            $response = $this->clientApi()->get("/products/{$id}");
            $product = null;

            if ($response->successful()) {
                $data = $response->json();

                // Đơn giản hóa logic lấy product
                if (isset($data['data']) && is_array($data['data'])) {
                    $product = $data['data'];
                } elseif (isset($data['id'])) { // API trả về 1 object
                    $product = $data;
                }

                if ($product) {
                    $this->formatProductImageUrl($product); // Chuẩn hóa URL ảnh

                    // Lấy sản phẩm liên quan (bắt buộc phải tuần tự)
                    $relatedProducts = $this->getRelatedProducts($product, $id);

                    return view('client.motorcycles.show', compact('product', 'relatedProducts'));
                }
            }

            // Lỗi 404 hoặc response rỗng
            Log::error('Product Detail API Error for ID: ' . $id . ', Status: ' . $response->status());
        } catch (ConnectionException $e) {
            Log::error('Product Detail Controller Error: ' . $e->getMessage());
        }

        // Trả về lỗi
        return view('client.motorcycles.show', [
            'product' => null,
            'relatedProducts' => [],
            'error' => 'Không tìm thấy sản phẩm.'
        ]);
    }

    /**
     * TỐI ƯU: Dùng clientApi helper
     */
    public function brands(Request $request)
    {
        try {
            // Gửi tất cả query (bao gồm page, limit...)
            $response = $this->clientApi()->get('/brands', $request->query());

            $brands = [];
            $pagination = null;
            $paginationLinks = null;

            if ($response->successful()) {
                $data = $response->json();
                $brands = $data['data'] ?? [];
                $pagination = $data['meta'] ?? null;
                $paginationLinks = $data['links'] ?? null;
            } else {
                Log::error('Brands API Error: ' . $response->status());
                $error = 'Không thể tải danh sách hãng xe';
            }

            return view('client.brands', compact('brands', 'pagination', 'paginationLinks'))
                ->with('error', $error ?? null);
        } catch (ConnectionException $e) {
            Log::error('Brands Controller Error: ' . $e->getMessage());
            return view('client.brands', [
                'brands' => [],
                'pagination' => null,
                'paginationLinks' => null,
                'error' => 'Không thể tải dữ liệu từ server'
            ]);
        }
    }

    /**
     * TỐI ƯU: Dùng Http::pool() chạy song song
     */
    public function brandDetail($id, Request $request)
    {
        $viewData = [
            'brand' => null,
            'products' => [],
            'pagination' => null,
            'paginationLinks' => null,
            'error' => null
        ];

        try {
            // Chuẩn bị params
            $productParams = $request->query();
            $productParams['brand_id'] = $id;

            // 3. Chạy 2 request CÙNG LÚC
            $responses = Http::pool(fn(Pool $pool) => [
                $pool->as('brand')->baseUrl($this->apiUrl . '/api/client')->get("/brands/{$id}"),
                $pool->as('products')->baseUrl($this->apiUrl . '/api/client')->get("/products", $productParams),
            ]);

            // Xử lý Brand
            if ($responses['brand']->successful()) {
                $viewData['brand'] = $responses['brand']->json('data', []);
            } else {
                abort(404, 'Không tìm thấy thương hiệu này.');
            }

            // Xử lý Products
            if ($responses['products']->successful()) {
                $data = $responses['products']->json();
                $viewData['products'] = $data['data'] ?? [];
                $viewData['pagination'] = $data['meta'] ?? null;
                $viewData['paginationLinks'] = $data['links'] ?? null;

                foreach ($viewData['products'] as &$product) {
                    $this->formatProductImageUrl($product);
                }
            } else {
                Log::error('Brand Detail API Error for Brand ID: ' . $id . ', Status: ' . $responses['products']->status());
                $viewData['error'] = 'Không thể tải sản phẩm của hãng';
            }

            return view('client.brand-detail', $viewData);
        } catch (ConnectionException $e) {
            Log::error('Brand Detail Controller Error: ' . $e->getMessage());
            $viewData['error'] = 'Không thể tải dữ liệu từ server';
            return view('client.brand-detail', $viewData);
        }
    }


    /**
     * Helper method to get related products (Đã được tối ưu)
     */
    private function getRelatedProducts(array $product, $currentProductId)
    {
        try {
            $params = ['limit' => 4]; // Lấy 4, phòng trường hợp trùng

            if (isset($product['brand']['id'])) {
                $params['brand_id'] = $product['brand']['id'];
            } elseif (isset($product['category']['id'])) {
                $params['category_id'] = $product['category']['id'];
            }

            // Dùng clientApi() cho nhất quán
            $response = $this->clientApi()->get('/products', $params);

            if ($response->successful()) {
                $relatedProducts = $response->json('data', []);

                // Lọc sản phẩm hiện tại
                $relatedProducts = array_filter($relatedProducts, function ($p) use ($currentProductId) {
                    return $p['id'] != $currentProductId;
                });

                // Chuẩn hóa URL ảnh
                foreach ($relatedProducts as &$relatedProduct) {
                    $this->formatProductImageUrl($relatedProduct);
                }

                return array_slice($relatedProducts, 0, 3); // Lấy 3
            }
        } catch (ConnectionException $e) {
            Log::warning('Get Related Products Error: ' . $e->getMessage());
        }
        return [];
    }

    // ĐÃ XÓA: getBrands() và getCategories() (vì đã gộp vào pool của index())
}

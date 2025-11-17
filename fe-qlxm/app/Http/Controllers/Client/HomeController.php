<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\PendingRequest; // 👈 1. Import
use Illuminate\Http\Client\ConnectionException; // 👈 2. Import
use Illuminate\Http\Client\Pool; // 👈 3. Import

class HomeController extends Controller
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
            ->timeout(10); // Đặt timeout chung
    }

    /**
     * TỐI ƯU: Chuẩn hóa logic lấy URL ảnh
     */
    private function formatProductImageUrl(array &$product)
    {
        // Nếu đã có image_url từ S3, giữ nguyên
        if (!empty($product['image_url'])) {
            return; // Đã có URL đầy đủ
        }

        // Nếu chỉ có image path, tạo URL
        if (!empty($product['image'])) {
            $product['image_url'] = $this->apiUrl . '/storage/' . $product['image'];
        } else {
            $product['image_url'] = null;
        }
    }

    /**
     * 4. TỐI ƯU TỐC ĐỘ: Dùng Http::pool() chạy song song
     */
    public function index(Request $request)
    {
        $viewData = [
            'products' => [],
            'brands' => [],
            'categories' => [],
            'pagination' => null,
            'paginationLinks' => null,
            'error' => null
        ];

        try {
            // Chuẩn bị params cho products
            $productParams = [];
            $limit = $request->get('limit', 5);
            $productParams['per_page'] = $limit;
            $productParams['featured'] = true;
            
            Log::info('Home Controller - Sending API request with params:', $productParams);

            // Chạy 3 request CÙNG LÚC với retry
            $responses = Http::pool(fn(Pool $pool) => [
                $pool->as('products')
                    ->baseUrl($this->apiUrl . '/api/client')
                    ->timeout(15)
                    ->retry(2, 100)
                    ->get('/products', $productParams),
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
            ]);

            // Xử lý Products
            $productsResponse = $responses['products'];
            if (!($productsResponse instanceof \Throwable) && $productsResponse->successful()) {
                $data = $productsResponse->json();
                Log::info('Home Controller - Products loaded successfully', [
                    'total' => $data['meta']['total'] ?? 0,
                    'per_page' => $data['meta']['per_page'] ?? 0,
                    'count' => count($data['data'] ?? [])
                ]);
                $viewData['products'] = $data['data'] ?? [];
                $viewData['pagination'] = $data['meta'] ?? null;
                $viewData['paginationLinks'] = $data['links'] ?? null;

                foreach ($viewData['products'] as &$product) {
                    $this->formatProductImageUrl($product);
                }
            } else {
                if ($productsResponse instanceof \Throwable) {
                    Log::error('Home API Error (Products): ' . $productsResponse->getMessage());
                } else {
                    Log::error('Home API Error (Products): HTTP ' . $productsResponse->status());
                }
                $viewData['error'] = 'Không thể tải sản phẩm từ server';
            }

            // Xử lý Brands
            $brandsResponse = $responses['brands'];
            if (!($brandsResponse instanceof \Throwable) && $brandsResponse->successful()) {
                $viewData['brands'] = $brandsResponse->json('data', []);
                Log::info('Home Controller - Brands loaded: ' . count($viewData['brands']));
            } else {
                if ($brandsResponse instanceof \Throwable) {
                    Log::warning('Home API Error (Brands): ' . $brandsResponse->getMessage());
                } else {
                    Log::warning('Home API Error (Brands): HTTP ' . $brandsResponse->status());
                }
            }

            // Xử lý Categories
            $categoriesResponse = $responses['categories'];
            if (!($categoriesResponse instanceof \Throwable) && $categoriesResponse->successful()) {
                $viewData['categories'] = $categoriesResponse->json('data', []);
                Log::info('Home Controller - Categories loaded: ' . count($viewData['categories']));
            } else {
                if ($categoriesResponse instanceof \Throwable) {
                    Log::warning('Home API Error (Categories): ' . $categoriesResponse->getMessage());
                } else {
                    Log::warning('Home API Error (Categories): HTTP ' . $categoriesResponse->status());
                }
            }

            return view('client.home', $viewData);
        } catch (\Exception $e) {
            Log::error('Home Controller Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            $viewData['error'] = 'Đã xảy ra lỗi không mong muốn';
            return view('client.home', $viewData);
        }
    }

    /**
     * Display the contact page. (Không đổi)
     */
    public function contact()
    {
        return view('client.contact');
    }

    /**
     * Display the checkout page. (Không đổi)
     */
    public function checkout()
    {
        $cart = session()->get('cart', []);
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return view('client.checkout', compact('cart', 'total'));
    }

    /**
     * Process checkout với format API mới
     */
    public function processCheckout(OrderRequest $request)
    {
        $validated = $request->validated();

        try {
            $cart = session()->get('cart', []);
            if (empty($cart)) {
                return redirect()->back()->with('error', 'Giỏ hàng của bạn đang trống');
            }

            // Debug log cart structure
            Log::info('Cart structure in processCheckout:', ['cart' => $cart]);

            // Format data theo yêu cầu backend API
            // Chú ý: $cart có cấu trúc [product_id => item_data]
            $items = [];
            foreach ($cart as $productId => $item) {
                $items[] = [
                    'product_id' => $productId,
                    'quantity' => $item['quantity']
                ];
            }

            $orderData = [
                'customer_name' => $validated['name'],
                'customer_phone' => $validated['phone'],
                'customer_email' => $validated['email'],
                'customer_address' => $validated['address'],
                'notes' => $validated['notes'] ?? null,
                'items' => $items
            ];

            // Debug log order data
            Log::info('Order data to send:', ['orderData' => $orderData]);

            // Gửi request đến đúng endpoint: /api/client/orders
            Log::info('Gửi đơn hàng đến: ' . $this->apiUrl . '/api/client/orders');

            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'X-Requested-With' => 'XMLHttpRequest'
                ])
                ->post($this->apiUrl . '/api/client/orders', $orderData);

            // Debug response
            Log::info('Order API Response Status: ' . $response->status());
            Log::info('Order API Response Headers: ' . json_encode($response->headers()));
            if (!$response->successful()) {
                Log::error('Order API Response Body: ' . $response->body());
            }

            if ($response->successful()) {
                session()->forget('cart');

                // Lấy thông tin order vừa tạo
                $orderInfo = $response->json('data');
                $orderNumber = $orderInfo['id'] ?? 'N/A';

                // Redirect đến trang success với thông tin đơn hàng
                return redirect()->route('client.order.success')->with([
                    'success' => "Đặt hàng thành công! Mã đơn hàng: #{$orderNumber}. Chúng tôi sẽ liên hệ với bạn sớm nhất.",
                    'orderInfo' => $orderInfo
                ]);
            } else {
                Log::error('Order API Error: Status ' . $response->status() . ' - ' . $response->body());

                // Xử lý lỗi validation từ backend
                if ($response->status() === 422) {
                    $errors = $response->json('errors', []);
                    return redirect()->back()->withErrors($errors)->withInput();
                }

                // Xử lý lỗi 500 từ backend
                if ($response->status() === 500) {
                    return redirect()->back()->with('error', 'Lỗi máy chủ backend. Vui lòng thử lại sau hoặc liên hệ hỗ trợ.');
                }

                // Xử lý lỗi 404 - endpoint không tồn tại
                if ($response->status() === 404) {
                    return redirect()->back()->with('error', 'Dịch vụ đặt hàng hiện không khả dụng. Vui lòng thử lại sau.');
                }

                return redirect()->back()->with('error', 'Có lỗi xảy ra khi xử lý đơn hàng (Mã lỗi: ' . $response->status() . '). Vui lòng thử lại.');
            }
        } catch (ConnectionException $e) {
            Log::error('Checkout Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Lỗi kết nối máy chủ. Vui lòng thử lại sau.');
        } catch (\Exception $e) {
            Log::error('Checkout Unexpected Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Đã xảy ra lỗi không mong muốn. Vui lòng thử lại sau.');
        }
    }

    /**
     * Hiển thị trang thành công sau khi đặt hàng
     */
    public function orderSuccess()
    {
        // Kiểm tra có thông tin order trong session không
        if (!session()->has('success')) {
            return redirect()->route('client.home')->with('error', 'Không tìm thấy thông tin đơn hàng.');
        }

        $orderInfo = session('orderInfo');

        return view('client.order-success', compact('orderInfo'));
    }
}

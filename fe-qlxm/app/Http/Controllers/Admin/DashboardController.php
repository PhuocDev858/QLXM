<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\ConnectionException; // Giữ lại để tham khảo
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log; // Thêm Log để ghi lỗi
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected $apiUrl;

    /**
     * Dùng Constructor để thiết lập API URL một lần duy nhất
     */
    public function __construct()
    {
        $this->apiUrl = rtrim(config('app.be_api_url'), '/');
    }

    /**
     * HÀM TỐI ƯU: Chỉ dùng để kiểm tra auth
     */
    private function api()
    {
        if (!session('admin_token')) {
            return redirect()->route('admin.auth.login');
        }
        return true;
    }

    /**
     * ĐÃ SỬA LỖI 500 SERVER ERROR
     */
    public function index()
    {
        // 1. Dùng hàm api() để kiểm tra auth
        $authCheck = $this->api();
        if ($authCheck instanceof RedirectResponse) return $authCheck;

        $token = session('admin_token');
        $apiUrl = $this->apiUrl . '/api';

        // 2. TỐI ƯU: Khởi tạo tất cả biến TRƯỚC try...catch
        $data = [
            'productCount' => 0,
            'customerCount' => 0,
            'orderCount' => 0,
            'userCount' => 0,
            'latestOrders' => [],
            'latestProducts' => [],
            'error' => null
        ];

        // Khai báo riêng để dùng trong try
        $orders = $products = [];

        try {
            // 3. Gọi API stats để lấy tổng số từ database
            $statsResponse = Http::withToken($token)->get($apiUrl . '/stats');
            
            if ($statsResponse->successful()) {
                $stats = $statsResponse->json('data', []);
                $data['productCount'] = $stats['productCount'] ?? 0;
                $data['customerCount'] = $stats['customerCount'] ?? 0;
                $data['orderCount'] = $stats['orderCount'] ?? 0;
                $data['userCount'] = $stats['userCount'] ?? 0;
            }

            // 4. Lấy danh sách orders và products mới nhất
            $responses = Http::pool(fn(Pool $pool) => [
                $pool->as('orders')->withToken($token)->get($apiUrl . '/orders'),
                $pool->as('products')->withToken($token)->get($apiUrl . '/products'),
            ]);

            $orders = $responses['orders']->successful() ? $responses['orders']->json('data', []) : [];
            $products = $responses['products']->successful() ? $responses['products']->json('data', []) : [];

            // Ghi nhận lỗi nếu có
            if (!$statsResponse->successful()) {
                $data['error'] = 'Lỗi khi tải thống kê: ' . $statsResponse->status();
            }

        } catch (\Exception $e) {
            Log::error('Dashboard Error: ' . $e->getMessage());
            $data['error'] = 'Không thể kết nối hoặc API bị lỗi: ' . $e->getMessage();
        }

        // 5. Lấy 5 items mới nhất
        $data['latestOrders'] = array_slice($orders, 0, 5);
        $data['latestProducts'] = array_slice($products, 0, 5);

        // Trả về view với $data
        return view('admin.dashboard', $data);
    }
}

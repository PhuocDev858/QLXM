# Cấu hình Chatbot AI với Google Gemini (MIỄN PHÍ)

## ✅ Ưu điểm Gemini
- **Hoàn toàn miễn phí** - 60 requests/phút
- Không cần thẻ tín dụng
- Hỗ trợ tiếng Việt xuất sắc
- Model mạnh (Gemini Pro)
- 1,500 requests/ngày miễn phí

## Bước 1: Lấy API Key

1. Truy cập: **https://aistudio.google.com/app/apikey**
2. Đăng nhập bằng Google Account
3. Click **"Create API Key"**
4. Chọn project hoặc tạo mới
5. Copy API key (dạng: `AIzaSy...`)

## Bước 2: Cấu hình

Mở file `.env` và thêm API key vào dòng:

```env
GEMINI_API_KEY=AIzaSy...your-api-key-here
```

## Bước 3: Clear cache

```bash
php artisan config:clear
php artisan view:clear
```

## Bước 4: Test

1. Refresh trang (Ctrl+F5)
2. Click nút chatbot tím góc phải dưới
3. Hỏi: "Xe máy nào phù hợp cho sinh viên?"

## Giới hạn miễn phí

- 60 requests/phút
- 1,500 requests/ngày
- 1 triệu requests/tháng
- **Đủ cho hầu hết website!**

## Troubleshooting

**Lỗi: "Gemini API key chưa được cấu hình"**
- Kiểm tra đã thêm key vào `.env`
- Chạy `php artisan config:clear`

**Chatbot không hiển thị**
- Ctrl+F5 để hard refresh
- Kiểm tra console (F12)

**Response chậm**
- Bình thường (~2-5 giây)
- Gemini API cần thời gian xử lý

## Tài liệu

- Google AI Studio: https://aistudio.google.com
- Gemini Docs: https://ai.google.dev/docs
- Pricing: https://ai.google.dev/pricing

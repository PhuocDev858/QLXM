<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ChatbotController extends Controller
{
    private $huggingfaceApiKey;
    private $huggingfaceApiUrl = 'https://api-inference.huggingface.co/models/meta-llama/Llama-3.2-3B-Instruct';

    public function __construct()
    {
        $this->huggingfaceApiKey = env('HUGGINGFACE_API_KEY');
    }

    /**
     * Xử lý tin nhắn từ chatbot
     */
    public function handleMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
            'conversation_history' => 'array|max:10'
        ]);

        $userMessage = $request->input('message');
        $conversationHistory = $request->input('conversation_history', []);

        try {
            // Kiểm tra API key
            if (empty($this->huggingfaceApiKey)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Hugging Face API key chưa được cấu hình'
                ], 500);
            }

            // Chuẩn bị system prompt
            $systemPrompt = $this->getSystemPrompt();

            // Tạo prompt với conversation history
            $prompt = $systemPrompt . "\n\n";
            
            // Thêm lịch sử hội thoại
            foreach ($conversationHistory as $msg) {
                if (isset($msg['sender']) && isset($msg['text'])) {
                    if ($msg['sender'] === 'user') {
                        $prompt .= "Khách hàng: " . $msg['text'] . "\n";
                    } else {
                        $prompt .= "Trợ lý: " . $msg['text'] . "\n";
                    }
                }
            }
            
            // Thêm câu hỏi mới
            $prompt .= "Khách hàng: " . $userMessage . "\nTrợ lý:";

            // Gọi Hugging Face Inference API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->huggingfaceApiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($this->huggingfaceApiUrl, [
                'inputs' => $prompt,
                'parameters' => [
                    'max_new_tokens' => 250,
                    'temperature' => 0.7,
                    'top_p' => 0.9,
                    'do_sample' => true,
                    'return_full_text' => false
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Xử lý response từ Hugging Face
                if (isset($data[0]['generated_text'])) {
                    $reply = trim($data[0]['generated_text']);
                    
                    // Loại bỏ phần prompt nếu còn trong response
                    $reply = str_replace($prompt, '', $reply);
                    $reply = trim($reply);
                    
                    // Cắt ở dấu xuống dòng đầu tiên nếu có nhiều đoạn
                    $lines = explode("\n", $reply);
                    $reply = trim($lines[0]);
                    
                    // Giới hạn độ dài
                    if (mb_strlen($reply) > 300) {
                        $reply = mb_substr($reply, 0, 297) . '...';
                    }

                    Log::info('Hugging Face API Success', [
                        'model' => 'Llama-3.2-3B-Instruct',
                        'response_length' => strlen($reply)
                    ]);

                    return response()->json([
                        'success' => true,
                        'reply' => $reply ?: 'Xin lỗi, tôi cần thêm thông tin để trả lời câu hỏi này. Bạn có thể hỏi cụ thể hơn được không?'
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'error' => 'Không nhận được phản hồi từ AI'
                    ], 500);
                }
            } else {
                $statusCode = $response->status();
                $errorBody = $response->body();
                
                Log::error('Hugging Face API Error', [
                    'status' => $statusCode,
                    'body' => $errorBody
                ]);

                // Nếu model đang loading (503), trả về thông báo thân thiện
                if ($statusCode === 503) {
                    return response()->json([
                        'success' => true,
                        'reply' => 'Hệ thống AI đang khởi động, vui lòng thử lại sau vài giây. Hoặc bạn có thể liên hệ hotline để được tư vấn trực tiếp.'
                    ]);
                }

                return response()->json([
                    'success' => false,
                    'error' => 'Không thể kết nối đến AI API'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Chatbot Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Đã xảy ra lỗi khi xử lý yêu cầu'
            ], 500);
        }
    }

    /**
     * Tạo system prompt cho AI về QLXM
     */
    private function getSystemPrompt()
    {
        return "Bạn là trợ lý AI thông minh của QLXM - hệ thống quản lý và bán xe máy trực tuyến tại Việt Nam.

Thông tin về QLXM:
- Hệ thống bán xe máy chính hãng từ các hãng: Honda, Yamaha, Suzuki, Piaggio, SYM
- Các loại xe: xe số, xe tay ga, xe côn tay, xe phân khối lớn
- Giá từ 15-100+ triệu VNĐ
- Bảo hành chính hãng, trả góp 0%

Nhiệm vụ:
1. Tư vấn xe phù hợp với nhu cầu và ngân sách
2. Giải đáp về giá cả, thông số kỹ thuật
3. So sánh các dòng xe, hãng xe
4. Hướng dẫn mua xe, thanh toán

Phong cách:
- Thân thiện, ngắn gọn (2-3 câu)
- Tiếng Việt có dấu
- Gợi ý cụ thể
- Nếu không chắc, đề nghị liên hệ hotline

Chỉ trả lời về xe máy và QLXM. Từ chối lịch sự câu hỏi không liên quan.";
    }
}

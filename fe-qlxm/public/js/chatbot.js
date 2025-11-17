// Chatbot AI Configuration
const CHATBOT_CONFIG = {
    apiKey: '', // Sẽ được set từ backend
    apiUrl: '/api/chatbot/message',
    model: 'gpt-3.5-turbo',
    maxTokens: 150,
    temperature: 0.7
};

class Chatbot {
    constructor() {
        this.isOpen = false;
        this.messages = [];
        this.init();
    }

    init() {
        this.createChatbotUI();
        this.attachEventListeners();
        this.loadChatHistory();
    }

    createChatbotUI() {
        const chatbotHTML = `
            <div id="chatbot-container" class="chatbot-container">
                <!-- Chatbot Toggle Button -->
                <button id="chatbot-toggle" class="chatbot-toggle" title="Chat với AI">
                    <i class="fa fa-comments"></i>
                </button>

                <!-- Chatbot Window -->
                <div id="chatbot-window" class="chatbot-window">
                    <!-- Header -->
                    <div class="chatbot-header">
                        <div class="chatbot-header-info">
                            <i class="fa fa-robot"></i>
                            <div>
                                <h4>Trợ lý AI QLXM</h4>
                                <span class="chatbot-status">Đang hoạt động</span>
                            </div>
                        </div>
                        <button id="chatbot-close" class="chatbot-close-btn">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>

                    <!-- Messages Area -->
                    <div id="chatbot-messages" class="chatbot-messages">
                        <div class="chatbot-message bot-message">
                            <div class="message-avatar">
                                <i class="fa fa-robot"></i>
                            </div>
                            <div class="message-content">
                                <p>Xin chào! Tôi là trợ lý AI của QLXM. Tôi có thể giúp bạn tìm hiểu về các dòng xe máy, so sánh giá cả, hoặc tư vấn chọn xe phù hợp. Bạn cần hỗ trợ gì?</p>
                                <span class="message-time">${this.getCurrentTime()}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Typing Indicator -->
                    <div id="typing-indicator" class="typing-indicator" style="display: none;">
                        <div class="message-avatar">
                            <i class="fa fa-robot"></i>
                        </div>
                        <div class="typing-dots">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>

                    <!-- Input Area -->
                    <div class="chatbot-input-area">
                        <textarea 
                            id="chatbot-input" 
                            class="chatbot-input" 
                            placeholder="Nhập câu hỏi của bạn..."
                            rows="1"
                        ></textarea>
                        <button id="chatbot-send" class="chatbot-send-btn">
                            <i class="fa fa-paper-plane"></i>
                        </button>
                    </div>

                    <!-- Quick Actions -->
                    <div class="chatbot-quick-actions">
                        <button class="quick-action-btn" data-message="Xe máy nào phù hợp cho sinh viên?">
                            <i class="fa fa-graduation-cap"></i> Xe cho sinh viên
                        </button>
                        <button class="quick-action-btn" data-message="So sánh Honda và Yamaha">
                            <i class="fa fa-balance-scale"></i> So sánh hãng
                        </button>
                        <button class="quick-action-btn" data-message="Xe tay ga giá dưới 30 triệu">
                            <i class="fa fa-money-bill"></i> Xe giá rẻ
                        </button>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', chatbotHTML);
    }

    attachEventListeners() {
        // Toggle chatbot
        document.getElementById('chatbot-toggle').addEventListener('click', () => {
            this.toggleChatbot();
        });

        // Close chatbot
        document.getElementById('chatbot-close').addEventListener('click', () => {
            this.toggleChatbot();
        });

        // Send message
        document.getElementById('chatbot-send').addEventListener('click', () => {
            this.sendMessage();
        });

        // Send message on Enter (Shift+Enter for new line)
        document.getElementById('chatbot-input').addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });

        // Auto-resize textarea
        document.getElementById('chatbot-input').addEventListener('input', (e) => {
            e.target.style.height = 'auto';
            e.target.style.height = Math.min(e.target.scrollHeight, 100) + 'px';
        });

        // Quick actions
        document.querySelectorAll('.quick-action-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const message = e.currentTarget.getAttribute('data-message');
                document.getElementById('chatbot-input').value = message;
                this.sendMessage();
            });
        });
    }

    toggleChatbot() {
        this.isOpen = !this.isOpen;
        const chatbotWindow = document.getElementById('chatbot-window');
        const chatbotToggle = document.getElementById('chatbot-toggle');
        
        if (this.isOpen) {
            chatbotWindow.classList.add('open');
            chatbotToggle.classList.add('hidden');
            // Focus on input
            setTimeout(() => {
                document.getElementById('chatbot-input').focus();
            }, 300);
        } else {
            chatbotWindow.classList.remove('open');
            chatbotToggle.classList.remove('hidden');
        }
    }

    async sendMessage() {
        const input = document.getElementById('chatbot-input');
        const message = input.value.trim();
        
        if (!message) return;

        // Add user message to UI
        this.addMessage(message, 'user');
        input.value = '';
        input.style.height = 'auto';

        // Show typing indicator
        this.showTypingIndicator();

        try {
            // Send to backend API
            const response = await fetch(CHATBOT_CONFIG.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    message: message,
                    conversation_history: this.messages.slice(-5) // Send last 5 messages for context
                })
            });

            const data = await response.json();

            // Hide typing indicator
            this.hideTypingIndicator();

            if (data.success && data.reply) {
                this.addMessage(data.reply, 'bot');
            } else {
                this.addMessage('Xin lỗi, tôi gặp sự cố khi xử lý yêu cầu của bạn. Vui lòng thử lại sau.', 'bot');
            }
        } catch (error) {
            this.hideTypingIndicator();
            console.error('Chatbot error:', error);
            this.addMessage('Xin lỗi, không thể kết nối đến server. Vui lòng kiểm tra kết nối internet và thử lại.', 'bot');
        }

        // Save chat history
        this.saveChatHistory();
    }

    addMessage(text, sender) {
        const messagesContainer = document.getElementById('chatbot-messages');
        const messageClass = sender === 'user' ? 'user-message' : 'bot-message';
        const avatar = sender === 'user' 
            ? '<i class="fa fa-user"></i>' 
            : '<i class="fa fa-robot"></i>';

        const messageHTML = `
            <div class="chatbot-message ${messageClass}">
                <div class="message-avatar">
                    ${avatar}
                </div>
                <div class="message-content">
                    <p>${this.escapeHtml(text)}</p>
                    <span class="message-time">${this.getCurrentTime()}</span>
                </div>
            </div>
        `;

        messagesContainer.insertAdjacentHTML('beforeend', messageHTML);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;

        // Store message in history
        this.messages.push({ text, sender, time: new Date().toISOString() });
    }

    showTypingIndicator() {
        document.getElementById('typing-indicator').style.display = 'flex';
        const messagesContainer = document.getElementById('chatbot-messages');
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    hideTypingIndicator() {
        document.getElementById('typing-indicator').style.display = 'none';
    }

    getCurrentTime() {
        const now = new Date();
        return now.getHours().toString().padStart(2, '0') + ':' + 
               now.getMinutes().toString().padStart(2, '0');
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    saveChatHistory() {
        try {
            localStorage.setItem('chatbot_history', JSON.stringify(this.messages.slice(-20)));
        } catch (e) {
            console.warn('Could not save chat history:', e);
        }
    }

    loadChatHistory() {
        try {
            const history = localStorage.getItem('chatbot_history');
            if (history) {
                this.messages = JSON.parse(history);
                // Optionally restore messages to UI
            }
        } catch (e) {
            console.warn('Could not load chat history:', e);
        }
    }
}

// Initialize chatbot when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.chatbot = new Chatbot();
});

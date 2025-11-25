<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BBI Chat</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f5f5f5;
            height: 100vh;
            overflow: hidden;
        }

        #root {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .rag-component {
            display: flex;
            flex-direction: column;
            height: 100%;
            background: rgba(255, 255, 255, 0.30);
        }

        .rag-header {
            padding: 16px;
            border-bottom: 1px solid #e0e0e0;
            background: rgba(51, 51, 51, .60);
            color: white;
        }

        .rag-header h2 {
            font-size: 18px;
            margin-bottom: 4px;
        }

        .rag-header p {
            font-size: 12px;
            opacity: 0.9;
        }

        .rag-form {
            padding: 16px;
            border-bottom: 1px solid #e0e0e0;
            background: rgba(255, 255, 255, 0.30);
        }

        .input-group {
            display: flex;
            gap: 8px;
        }

        .query-input {
            flex: 1;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
        }

        .query-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .submit-btn {
            padding: 10px 16px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: background 0.2s;
        }

        .submit-btn:hover:not(:disabled) {
            background: #5568d3;
        }

        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .answer-section {
            flex: 1;
            overflow-y: auto;
            padding: 16px;
        }

        .answer-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            font-weight: 600;
            color: #333;
        }

        .clear-btn {
            padding: 6px 12px;
            background: #f0f0f0;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            transition: background 0.2s;
        }

        .clear-btn:hover {
            background: #e0e0e0;
        }

        .answer-content {
            padding: 12px;
            background: #f9f9f9;
            border-radius: 6px;
            border-left: 3px solid #667eea;
            line-height: 1.6;
            color: #333;
            font-size: 14px;
        }

        .error-message {
            padding: 12px 16px;
            background: #fee;
            color: #c33;
            border-radius: 6px;
            margin-bottom: 12px;
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .loading-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
            gap: 12px;
            color: #667eea;
        }

        .loading-spinner {
            width: 32px;
            height: 32px;
            border: 3px solid #f0f0f0;
            border-top-color: #667eea;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div id="root"></div>
    <script>
        // Simple RAG Chat Component
        class RAGChat {
            constructor() {
                this.query = '';
                this.answer = '';
                this.loading = false;
                this.error = '';
                this.init();
            }

            init() {
                this.render();
                document.addEventListener('submit', (e) => this.handleSubmit(e));
                document.addEventListener('click', (e) => {
                    if (e.target.classList.contains('clear-btn')) this.clearChat();
                });
                document.addEventListener('input', (e) => {
                    if (e.target.classList.contains('query-input')) {
                        this.query = e.target.value;
                        this.updateButtonState();
                    }
                });
            }

            updateButtonState() {
                const btn = document.querySelector('.submit-btn');
                if (btn) {
                    btn.disabled = this.loading || !this.query.trim();
                }
            }

            async handleSubmit(e) {
                if (!e.target.classList.contains('rag-form')) return;
                e.preventDefault();
                if (!this.query.trim()) return;

                this.loading = true;
                this.error = '';
                this.answer = '';
                this.render();

                try {
                    const apiUrl = '/api/bedrock.php';
                    const response = await fetch(apiUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ message: this.query })
                    });

                    const rawText = await response.text();
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}\n${rawText}`);
                    }

                    let data;
                    try {
                        data = JSON.parse(rawText);
                    } catch (jsonErr) {
                        this.error = `Response was not valid JSON: ${rawText}`;
                        this.answer = '';
                        return;
                    }
                    // Try to extract answer from Bedrock response
                    if (Array.isArray(data.content) && data.content[0]?.text) {
                        this.answer = data.content[0].text;
                    } else if (data?.message) {
                        this.answer = data.message;
                    } else {
                        this.answer = JSON.stringify(data);
                    }
                } catch (err) {
                    this.error = err.message || 'Failed to get answer. Please try again.';
                    console.error('RAG Error:', err);
                } finally {
                    this.loading = false;
                    this.render();
                }
            }

            clearChat() {
                this.query = '';
                this.answer = '';
                this.error = '';
                this.render();
            }

            render() {
                const root = document.getElementById('root');
                // Get name from querystring
                const urlParams = new URLSearchParams(window.location.search);
                const nameFromQuery = urlParams.get('name') || '';
                const placeholderText = nameFromQuery
                    ? `What would you like to know ${nameFromQuery} ?`
                    : 'What would you like to know?';

                root.innerHTML = `
                    <div class="rag-component">
                        <div class="rag-header">
                            <h2><img src="./images/bbiLogoLite.svg" style="height: 24px; vertical-align: middle; margin-right: 8px;" alt="BBI" />AI Assistant</h2>
                            <p>Ask me anything!</p>
                        </div>

                        <form class="rag-form">
                            <div class="input-group">
                                <input
                                    type="text"
                                    class="query-input"
                                    value="${this.query}"
                                    placeholder="${placeholderText}"
                                    ${this.loading ? 'disabled' : ''}
                                />
                                <button 
                                    type="submit"
                                    class="submit-btn"
                                    ${this.loading || !this.query.trim() ? 'disabled' : ''}
                                >
                                    ${this.loading ? '🔄' : '➤'}
                                </button>
                            </div>
                        </form>

                        ${this.error ? `
                            <div class="error-message">
                                <span>❌</span>
                                <span>${this.error}</span>
                            </div>
                        ` : ''}

                        ${this.answer ? `
                            <div class="answer-section">
                                <div class="answer-header">
                                    <span>💡 Answer:</span>
                                    <button class="clear-btn">Clear</button>
                                </div>
                                <div class="answer-content">
                                    ${this.answer}
                                </div>
                            </div>
                        ` : ''}

                        ${this.loading ? `
                            <div class="loading-section">
                                <div class="loading-spinner"></div>
                                <span>Thinking...</span>
                            </div>
                        ` : ''}
                        <div style="width:100%;text-align:center;margin-top:32px;padding:16px;">
                            <a href="https://brownbi.com" style="display:inline-block;text-decoration:none;transition:transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                <img src="./images/bbiLogoLite.svg" alt="BBI Logo - Return Home" style="height:48px;opacity:0.8;transition:opacity 0.2s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.8'" />
                            </a>
                            <p style="font-size:11px;color:#666;margin-top:8px;">Visit brownbi.com</p>
                        </div>
                    </div>
                `;
                
                document.querySelector('.query-input')?.focus();
            }
        }

        new RAGChat();
    </script>
</body>
</html>

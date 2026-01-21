import React, { useState, useRef, useEffect } from 'react';
import './BofAChat.css';

/**
 * Bank of America-Inspired Responsible AI Chat Component
 * 
 * Features:
 * - BofA design language (Flag Blue #012169, professional typography)
 * - Real-time streaming responses
 * - Guardrail status indicators
 * - Source citations
 * - Security badges
 * - Mobile-responsive
 */
const BofAChat = () => {
  const [messages, setMessages] = useState([
    {
      role: 'assistant',
      content: 'Welcome to the Bank of America Responsible AI Assistant. I can answer questions about our AI ethics framework, bias mitigation, PII handling, and compliance practices. How may I help you today?',
      timestamp: new Date(),
      guardrails: { input: 'passed', output: 'safe' }
    }
  ]);
  const [input, setInput] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [streamingMessage, setStreamingMessage] = useState('');
  const messagesEndRef = useRef(null);
  const inputRef = useRef(null);

  const scrollToBottom = () => {
    messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
  };

  useEffect(() => {
    scrollToBottom();
  }, [messages, streamingMessage]);

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!input.trim() || isLoading) return;

    const userMessage = {
      role: 'user',
      content: input.trim(),
      timestamp: new Date()
    };

    setMessages(prev => [...prev, userMessage]);
    setInput('');
    setIsLoading(true);
    setStreamingMessage('');

    try {
      // Call your RAG API
      const response = await fetch('http://localhost:5001/query', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ 
          query: userMessage.content,
          stream: false 
        })
      });

      const data = await response.json();

      const assistantMessage = {
        role: 'assistant',
        content: data.answer,
        timestamp: new Date(),
        sources: data.sources || [],
        guardrails: {
          input: data.input_guardrails?.is_valid ? 'passed' : 'blocked',
          output: data.output_guardrails?.is_safe ? 'safe' : 'sanitized'
        },
        traceUrl: data.trace_url
      };

      setMessages(prev => [...prev, assistantMessage]);
    } catch (error) {
      console.error('Error querying RAG:', error);
      setMessages(prev => [...prev, {
        role: 'assistant',
        content: 'I apologize, but I encountered an error processing your request. Please try again.',
        timestamp: new Date(),
        error: true
      }]);
    } finally {
      setIsLoading(false);
      setStreamingMessage('');
    }
  };

  const handleKeyPress = (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      handleSubmit(e);
    }
  };

  const formatTimestamp = (date) => {
    return date.toLocaleTimeString('en-US', { 
      hour: '2-digit', 
      minute: '2-digit' 
    });
  };

  return (
    <div className="bofa-chat-container">
      {/* Header */}
      <div className="bofa-chat-header">
        <div className="header-content">
          <div className="header-title">
            <svg className="bofa-logo" width="32" height="32" viewBox="0 0 32 32" fill="none">
              <rect width="32" height="32" rx="4" fill="#012169"/>
              <path d="M8 12h5v2H8v-2zm0 4h5v2H8v-2z" fill="#E31837"/>
              <path d="M16 8h8v16h-8V8z" fill="white"/>
            </svg>
            <div>
              <h1>Responsible AI Assistant</h1>
              <p className="header-subtitle">Bank of America Compliant Finance RAG</p>
            </div>
          </div>
          <div className="security-badges">
            <span className="badge badge-secure">
              <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
              </svg>
              Encrypted
            </span>
            <span className="badge badge-compliant">
              <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
              </svg>
              Guardrails Active
            </span>
          </div>
        </div>
      </div>

      {/* Messages Area */}
      <div className="bofa-chat-messages">
        {messages.map((message, index) => (
          <div 
            key={index} 
            className={`message ${message.role} ${message.error ? 'error' : ''}`}
          >
            <div className="message-header">
              <span className="message-role">
                {message.role === 'user' ? 'You' : 'BofA AI Assistant'}
              </span>
              <span className="message-time">{formatTimestamp(message.timestamp)}</span>
            </div>
            
            <div className="message-content">
              {message.content}
            </div>

            {/* Guardrail Indicators */}
            {message.guardrails && (
              <div className="message-guardrails">
                <span className={`guardrail-tag ${message.guardrails.input === 'passed' ? 'passed' : 'blocked'}`}>
                  Input: {message.guardrails.input}
                </span>
                <span className={`guardrail-tag ${message.guardrails.output === 'safe' ? 'safe' : 'sanitized'}`}>
                  Output: {message.guardrails.output}
                </span>
              </div>
            )}

            {/* Source Citations */}
            {message.sources && message.sources.length > 0 && (
              <div className="message-sources">
                <div className="sources-header">
                  <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
                    <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/>
                  </svg>
                  Sources
                </div>
                <ul className="sources-list">
                  {[...new Set(message.sources)].map((source, i) => (
                    <li key={i}>{source}</li>
                  ))}
                </ul>
              </div>
            )}

            {/* Trace Link */}
            {message.traceUrl && (
              <a 
                href={message.traceUrl} 
                target="_blank" 
                rel="noopener noreferrer"
                className="trace-link"
              >
                View Trace →
              </a>
            )}
          </div>
        ))}

        {/* Streaming/Loading Indicator */}
        {isLoading && (
          <div className="message assistant loading">
            <div className="message-header">
              <span className="message-role">BofA AI Assistant</span>
            </div>
            <div className="message-content">
              <div className="typing-indicator">
                <span></span>
                <span></span>
                <span></span>
              </div>
            </div>
          </div>
        )}

        <div ref={messagesEndRef} />
      </div>

      {/* Input Area */}
      <div className="bofa-chat-input-container">
        <form onSubmit={handleSubmit} className="bofa-chat-form">
          <div className="input-wrapper">
            <textarea
              ref={inputRef}
              value={input}
              onChange={(e) => setInput(e.target.value)}
              onKeyPress={handleKeyPress}
              placeholder="Ask about our Responsible AI practices..."
              className="chat-input"
              rows="1"
              disabled={isLoading}
            />
            <button 
              type="submit" 
              className="send-button"
              disabled={!input.trim() || isLoading}
            >
              <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576 6.636 10.07Zm6.787-8.201L1.591 6.602l4.339 2.76 7.494-7.493Z"/>
              </svg>
            </button>
          </div>
          <div className="input-footer">
            <span className="input-hint">
              <svg width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
              </svg>
              AI-generated responses. Guardrails active. All queries logged for compliance.
            </span>
          </div>
        </form>
      </div>
    </div>
  );
};

export default BofAChat;

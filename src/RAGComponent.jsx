import React, { useState } from 'react';
import './RAGComponent.css';

export default function RAGComponent() {
  const [query, setQuery] = useState('');
  const [answer, setAnswer] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!query.trim()) return;

    setLoading(true);
    setError('');
    setAnswer('');

    try {
      // Replace with your actual API Gateway URL
      const apiUrl = 'https://your-api-gateway-url.amazonaws.com/prod/ask';
      const response = await fetch(`${apiUrl}?q=${encodeURIComponent(query)}`);
      
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      
      const data = await response.json();
      setAnswer(data.answer);
    } catch (err) {
      setError('Failed to get answer. Please try again.');
      console.error('RAG Error:', err);
    } finally {
      setLoading(false);
    }
  };

  const clearChat = () => {
    setQuery('');
    setAnswer('');
    setError('');
  };

  return (
    <div className="rag-component">
      <div className="rag-header">
        <h2>🤖 AI Assistant</h2>
        <p>Ask me anything about our documents</p>
      </div>

      <form onSubmit={handleSubmit} className="rag-form">
        <div className="input-group">
          <input
            type="text"
            value={query}
            onChange={(e) => setQuery(e.target.value)}
            placeholder="What would you like to know?"
            className="query-input"
            disabled={loading}
          />
          <button 
            type="submit" 
            className="submit-btn"
            disabled={loading || !query.trim()}
          >
            {loading ? '🔄' : '➤'}
          </button>
        </div>
      </form>

      {error && (
        <div className="error-message">
          <span>❌</span>
          <span>{error}</span>
        </div>
      )}

      {answer && (
        <div className="answer-section">
          <div className="answer-header">
            <span>💡 Answer:</span>
            <button onClick={clearChat} className="clear-btn">Clear</button>
          </div>
          <div className="answer-content">
            {answer}
          </div>
        </div>
      )}

      {loading && (
        <div className="loading-section">
          <div className="loading-spinner"></div>
          <span>Thinking...</span>
        </div>
      )}
    </div>
  );
}

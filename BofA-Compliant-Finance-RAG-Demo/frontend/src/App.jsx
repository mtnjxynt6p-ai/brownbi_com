import React, { useState } from 'react';
import BofAChat from './components/BofAChat';
import './App.css';

/**
 * Main App Component for BofA Responsible AI RAG Demo
 * 
 * Features:
 * - Full-screen chat interface
 * - Info panel with demo details
 * - Example queries sidebar
 * - Responsive layout
 */
function App() {
  const [showInfo, setShowInfo] = useState(false);

  const exampleQueries = [
    {
      category: '🎯 Ethics & Principles',
      queries: [
        "What are the five core principles of Bank of America's Responsible AI framework?",
        "How does BofA ensure accountability in AI decisions?"
      ]
    },
    {
      category: '⚖️ Bias & Fairness',
      queries: [
        "How does Bank of America detect and mitigate bias in AI systems?",
        "What is the disparate impact ratio threshold for AI models?"
      ]
    },
    {
      category: '🔒 Privacy & Security',
      queries: [
        "What are the PII handling requirements for Tier 1 data?",
        "How does BofA prevent prompt injection attacks?"
      ]
    },
    {
      category: '📋 Compliance',
      queries: [
        "What regulations govern AI use in financial services?",
        "What are the incident response procedures for AI failures?"
      ]
    },
    {
      category: '📊 Observability',
      queries: [
        "What metrics does BofA track for LLM applications?",
        "How does BofA evaluate RAG pipeline quality?"
      ]
    }
  ];

  return (
    <div className="app-container">
      {/* Header Bar */}
      <header className="app-header">
        <div className="header-left">
          <h1 className="app-title">
            <span className="bofa-text">Bank of America</span>
            <span className="demo-text">Responsible AI RAG Demo</span>
          </h1>
        </div>
        <div className="header-right">
          <button 
            className="info-button"
            onClick={() => setShowInfo(!showInfo)}
            aria-label="Toggle information panel"
          >
            <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
              <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
              <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
            </svg>
            Demo Info
          </button>
          <a 
            href="https://brownbi.com" 
            target="_blank" 
            rel="noopener noreferrer"
            className="portfolio-link"
          >
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
              <path d="M8.47 1.318a1 1 0 0 0-.94 0l-6 3.2A1 1 0 0 0 1 5.4v.817l5.75 3.45L8 8.917l1.25.75L15 6.217V5.4a1 1 0 0 0-.53-.882l-6-3.2Z"/>
              <path d="M15 7.383l-4.778 2.867L15 13.117V7.383zm-.035 6.88L8 10.082l-6.965 4.18A1 1 0 0 0 2 15h12a1 1 0 0 0 .965-.738ZM1 13.116l4.778-2.867L1 7.383v5.734Z"/>
            </svg>
            brownbi.com
          </a>
        </div>
      </header>

      {/* Main Content */}
      <main className="app-main">
        {/* Info Sidebar */}
        {showInfo && (
          <aside className="info-sidebar">
            <div className="info-content">
              <div className="info-section">
                <h2>About This Demo</h2>
                <p>
                  This application demonstrates enterprise-grade Retrieval Augmented Generation (RAG) 
                  with a focus on responsible AI practices aligned with Bank of America's principles.
                </p>
              </div>

              <div className="info-section">
                <h3>Key Features</h3>
                <ul>
                  <li><strong>Guardrails:</strong> Input/output scanning for PII, prompt injection, and toxicity</li>
                  <li><strong>Observability:</strong> Langfuse tracing with unique trace URLs</li>
                  <li><strong>Source Citations:</strong> All answers reference source documents</li>
                  <li><strong>Compliance:</strong> Structured logging and audit trails</li>
                  <li><strong>Evaluation:</strong> DeepEval metrics with LLM-as-Judge</li>
                </ul>
              </div>

              <div className="info-section">
                <h3>Technical Stack</h3>
                <ul>
                  <li><strong>LLM:</strong> AWS Bedrock - Claude 3.5 Sonnet</li>
                  <li><strong>Embeddings:</strong> sentence-transformers/all-MiniLM-L6-v2</li>
                  <li><strong>Vector DB:</strong> ChromaDB</li>
                  <li><strong>Framework:</strong> LangChain + FastAPI/Flask</li>
                  <li><strong>Frontend:</strong> React + Vite</li>
                </ul>
              </div>

              <div className="info-section">
                <h3>Example Queries</h3>
                {exampleQueries.map((group, idx) => (
                  <div key={idx} className="example-group">
                    <h4>{group.category}</h4>
                    <ul className="example-list">
                      {group.queries.map((query, qIdx) => (
                        <li key={qIdx}>{query}</li>
                      ))}
                    </ul>
                  </div>
                ))}
              </div>

              <div className="info-section">
                <h3>Created By</h3>
                <p>
                  <strong>Russell Brown</strong><br />
                  Senior AI/ML Engineer<br />
                  <a href="https://brownbi.com" target="_blank" rel="noopener noreferrer">brownbi.com</a>
                </p>
              </div>
            </div>
          </aside>
        )}

        {/* Chat Interface */}
        <div className={`chat-wrapper ${showInfo ? 'with-sidebar' : ''}`}>
          <BofAChat />
        </div>
      </main>

      {/* Footer */}
      <footer className="app-footer">
        <p>
          <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style={{ marginRight: '6px' }}>
            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
            <path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
          </svg>
          This is a demonstration. AI-generated responses may contain errors. 
          All queries are logged for compliance and evaluation purposes.
        </p>
      </footer>
    </div>
  );
}

export default App;

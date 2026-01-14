import React, { useState } from 'react'

const ChatBot = () => {
  const [messages, setMessages] = useState([])
  const [input, setInput] = useState('')
  const [loading, setLoading] = useState(false)

  const handleSubmit = async (e) => {
    e.preventDefault()
    if (!input.trim()) return

    setMessages([...messages, { type: 'user', text: input }])
    setInput('')
    setLoading(true)

    try {
      const response = await fetch('/api/bedrock.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message: input })
      })
      const data = await response.json()
      const answer = data.content?.[0]?.text || data.message || 'No response'
      setMessages(prev => [...prev, { type: 'bot', text: answer }])
    } catch (err) {
      setMessages(prev => [...prev, { type: 'bot', text: 'Error: ' + err.message }])
    } finally {
      setLoading(false)
    }
  }

  return (
    <div style={{ maxWidth: '500px', margin: '0 auto', padding: '20px' }}>
      <div style={{ border: '1px solid #ddd', borderRadius: '8px', padding: '16px', minHeight: '300px' }}>
        <div style={{ marginBottom: '16px', maxHeight: '250px', overflowY: 'auto' }}>
          {messages.map((msg, i) => (
            <div key={i} style={{ 
              marginBottom: '12px', 
              textAlign: msg.type === 'user' ? 'right' : 'left',
              padding: '8px 12px',
              backgroundColor: msg.type === 'user' ? '#e3f2fd' : '#f5f5f5',
              borderRadius: '8px',
              marginLeft: msg.type === 'user' ? '40px' : '0',
              marginRight: msg.type === 'user' ? '0' : '40px'
            }}>
              {msg.text}
            </div>
          ))}
        </div>
        <form onSubmit={handleSubmit} style={{ display: 'flex', gap: '8px' }}>
          <input
            type="text"
            value={input}
            onChange={(e) => setInput(e.target.value)}
            placeholder="Ask me something..."
            disabled={loading}
            style={{
              flex: 1,
              padding: '8px',
              border: '1px solid #ddd',
              borderRadius: '4px',
              fontSize: '14px'
            }}
          />
          <button 
            type="submit" 
            disabled={loading}
            style={{
              padding: '8px 16px',
              backgroundColor: '#667eea',
              color: 'white',
              border: 'none',
              borderRadius: '4px',
              cursor: loading ? 'not-allowed' : 'pointer',
              opacity: loading ? 0.6 : 1
            }}
          >
            {loading ? '...' : 'Send'}
          </button>
        </form>
      </div>
    </div>
  )
}

export default ChatBot

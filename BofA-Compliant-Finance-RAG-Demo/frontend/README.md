# Bank of America Responsible AI RAG - Frontend

Professional React chat interface showcasing BofA's design language and responsible AI principles.

## Quick Start

```bash
# Install dependencies
npm install

# Start development server (connects to Flask backend on :8000)
npm run dev

# Build for production
npm run build

# Preview production build
npm run preview
```

## Development

The frontend runs on `http://localhost:3000` and proxies API requests to `http://localhost:8000`.

Make sure the Flask backend is running:
```bash
cd ..
python3 flask_app.py
```

## Features

- **BofA Design Language:** Flag Blue (#012169), professional typography
- **Real-time Chat:** Streaming responses with typing indicators
- **Security Indicators:** Visible guardrails, encryption badges
- **Source Citations:** All answers cite source documents
- **Observability:** Trace URLs for debugging
- **Responsive:** Mobile-friendly design
- **Accessible:** WCAG compliant

## Production Build

```bash
npm run build
```

Output goes to `dist/` directory. Deploy these static files to your web server.

## Environment Variables

Create `.env` for custom configuration:

```
VITE_API_URL=https://brownbi.com/api
```

## Tech Stack

- **React 18:** Modern hooks-based components
- **Vite:** Fast build tool and dev server
- **CSS Modules:** Scoped styling
- **ESLint:** Code quality

## Project Structure

```
frontend/
├── src/
│   ├── components/
│   │   ├── BofAChat.jsx      # Main chat component
│   │   └── BofAChat.css      # Chat styles
│   ├── App.jsx                # Root component
│   ├── App.css                # App layout styles
│   ├── main.jsx               # Entry point
│   └── index.css              # Global styles
├── index.html                 # HTML template
├── vite.config.js             # Vite configuration
└── package.json               # Dependencies
```

## Customization

### Change API Endpoint

Edit `vite.config.js`:

```javascript
server: {
  proxy: {
    '/api': {
      target: 'https://your-api-domain.com',
      changeOrigin: true,
    }
  }
}
```

### Modify Chat Appearance

Edit `src/components/BofAChat.css` - all BofA brand colors are in CSS variables.

### Add Features

The `BofAChat` component is self-contained. Easy to:
- Add message reactions
- Implement conversation history
- Add export functionality
- Integrate voice input

## Browser Support

- Chrome/Edge (latest)
- Firefox (latest)
- Safari (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## License

MIT - Russell Brown (brownbi.com)

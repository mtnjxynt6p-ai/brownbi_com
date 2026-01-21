# Bank of America RAG Demo - GoDaddy Deployment Guide

## Overview
This guide explains how to deploy the BofA Responsible AI RAG demo to brownbi.com hosted on GoDaddy.

## Architecture

```
brownbi.com
├── /chat                    # React chat interface (built static files)
├── /api/rag                 # Flask API endpoint (WSGI)
└── Backend: Python 3.11 + Passenger WSGI
```

## Deployment Steps

### 1. Prepare Production Build

```bash
# Build React frontend
cd frontend
npm install
npm run build

# This creates frontend/dist/ with static files
```

### 2. Upload Files to GoDaddy

Upload to your GoDaddy hosting directory (e.g., `public_html/bofa-rag/`):

```
public_html/bofa-rag/
├── passenger_wsgi.py        # WSGI entry point
├── flask_app.py             # Flask API
├── app.py                   # RAG pipeline
├── .htaccess                # URL rewriting
├── requirements.txt         # Python dependencies
├── chroma_db/              # Vector database (or regenerate)
├── static/                  # Built React app from frontend/dist/
│   ├── index.html
│   ├── assets/
│   └── ...
└── logs/
```

### 3. Configure Passenger WSGI

Create `passenger_wsgi.py` in your deployment directory:

```python
import sys
import os

# Add your app directory to Python path
INTERP = "/home/username/.local/bin/python3"
if sys.executable != INTERP:
    os.execl(INTERP, INTERP, *sys.argv)

sys.path.insert(0, os.path.dirname(__file__))

from flask_app import application
```

### 4. Install Dependencies

SSH into your GoDaddy server:

```bash
# Install dependencies in user space
pip3 install --user -r requirements.txt

# Note: Some packages may be too large for shared hosting
# You may need to pre-download sentence-transformers models
```

### 5. Configure .htaccess

Create `.htaccess` for URL routing:

```apache
# Passenger configuration
PassengerEnabled on
PassengerAppRoot /home/username/public_html/bofa-rag
PassengerPython /home/username/.local/bin/python3

# Route API requests to Flask
RewriteEngine On
RewriteBase /

# API routes go to Flask app
RewriteCond %{REQUEST_URI} ^/api/
RewriteRule ^(.*)$ passenger_wsgi.py/$1 [QSA,L]

# Everything else serves static React app
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ /static/index.html [L]

# CORS headers
Header set Access-Control-Allow-Origin "https://brownbi.com"
Header set Access-Control-Allow-Methods "GET, POST, OPTIONS"
Header set Access-Control-Allow-Headers "Content-Type"
```

### 6. Environment Variables

Create `.env` file (not in public_html for security):

```bash
# AWS Bedrock credentials
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_REGION=us-east-1

# Langfuse observability (optional)
LANGFUSE_PUBLIC_KEY=pk-xxx
LANGFUSE_SECRET_KEY=sk-xxx
LANGFUSE_HOST=https://cloud.langfuse.com

# Flask configuration
FLASK_ENV=production
SECRET_KEY=your_secret_key_here
```

### 7. Test Deployment

```bash
# Test API endpoint
curl https://brownbi.com/bofa-rag/api/health

# Expected response:
{
  "status": "healthy",
  "components": {
    "api": "ok",
    "rag_engine": "ok",
    "vectorstore": "ok"
  }
}

# Test chat interface
# Visit: https://brownbi.com/bofa-rag/
```

## GoDaddy Shared Hosting Limitations & Solutions

### Issue: Large Python packages (transformers, torch)
**Solution:** Use lightweight alternatives or pre-compute embeddings
```python
# Instead of loading models on-the-fly:
# embeddings = HuggingFaceEmbeddings(...)

# Pre-compute and cache embeddings, use ChromaDB with HTTP client
```

### Issue: Memory limits (512MB typical)
**Solution:** 
- Disable guardrails in production (or use API-based scanners)
- Use smaller embedding models
- Implement request queuing

### Issue: No background processes
**Solution:** 
- Use external services (AWS Lambda) for heavy processing
- Implement webhook callbacks

### Issue: Read-only filesystem in some directories
**Solution:**
```python
# Use writable temp directory
import tempfile
CHROMA_PERSIST_DIR = os.path.join(tempfile.gettempdir(), 'chroma_db')
```

## Alternative: AWS EC2 + Reverse Proxy

If GoDaddy limitations are too restrictive:

1. **Deploy full stack on AWS EC2:**
   - t3.medium instance ($30/mo)
   - Full control, no package restrictions
   - Run FastAPI with uvicorn

2. **Point subdomain to EC2:**
   ```
   rag.brownbi.com → AWS EC2 Elastic IP
   ```

3. **Use Nginx reverse proxy on GoDaddy:**
   ```nginx
   location /api/rag {
       proxy_pass http://your-ec2-ip:8000;
   }
   ```

## Monitoring & Maintenance

### Logs
```bash
# View application logs
tail -f ~/public_html/bofa-rag/logs/rag_api.log

# View Passenger logs
tail -f ~/logs/passenger.log
```

### Performance Monitoring
- Use Langfuse for LLM observability
- Monitor API response times
- Set up UptimeRobot for availability checks

### Updates
```bash
# Pull latest code
git pull origin main

# Restart Passenger
touch ~/public_html/bofa-rag/tmp/restart.txt
```

## Security Checklist

- [ ] `.env` file is outside public_html
- [ ] API keys are not committed to git
- [ ] Rate limiting is enabled
- [ ] CORS is properly configured
- [ ] HTTPS is enforced
- [ ] Error messages don't leak sensitive info
- [ ] Input validation is in place
- [ ] Logs are rotated and secured

## Troubleshooting

### "500 Internal Server Error"
1. Check `passenger.log` for Python errors
2. Verify Python path in `passenger_wsgi.py`
3. Ensure all dependencies are installed

### "Module not found"
```bash
# Verify package installation
pip3 list --user | grep langchain

# Reinstall if needed
pip3 install --user --upgrade langchain-community
```

### "Vector store not loading"
```bash
# Regenerate vector database
cd ~/public_html/bofa-rag
python3 -c "from app import ingest_docs; ingest_docs()"
```

## Cost Estimate

**GoDaddy Shared Hosting:**
- $5-10/month (existing hosting)
- Limited resources, may struggle with ML packages

**AWS EC2 (Recommended for demo):**
- t3.medium: ~$30/month
- Full control, better performance
- Can handle all dependencies

**Total (with AWS):**
- $30-40/month for demo period
- Scale down to t3.micro ($9/mo) for light usage

## Next Steps

1. Test locally: `python3 flask_app.py`
2. Build frontend: `cd frontend && npm run build`
3. Deploy to staging environment first
4. Test all features thoroughly
5. Deploy to production
6. Monitor for 24 hours
7. Share demo link for interview

## Support

For deployment issues:
- GoDaddy support: https://www.godaddy.com/help
- Passenger docs: https://www.phusionpassenger.com/
- Flask deployment: https://flask.palletsprojects.com/en/2.3.x/deploying/

---

**Pro Tip for BofA Interview:**
Emphasize the production-ready aspects:
- Comprehensive error handling
- Rate limiting for DoS protection
- Structured logging for compliance
- Health check endpoints for monitoring
- CORS security
- Scalable architecture (easy to move to AWS/Azure)

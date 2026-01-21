# GoDaddy Deployment - Complete Upload Checklist

## Step 1: Pre-Build Vector Database (Do This on Your Mac FIRST)

```bash
cd /Users/russellbrown/brown-bi-com-react/public_html/BofA-Compliant-Finance-RAG-Demo

# Build the vector database locally
python3 -c "
from app import ingest_docs
print('Building vector database...')
vectorstore = ingest_docs()
print('✅ Vector database created in ./chroma_db/')
"
```

This creates `chroma_db/` folder (~500MB) with all embeddings pre-computed.

---

## Step 2: Build React Frontend

```bash
cd frontend
npm install
npm run build

# Creates frontend/dist/ with static files
```

---

## Step 3: Create Upload Package

Create this folder structure locally:

```
bofa-rag-deploy/
├── flask_app.py                 (copy from project)
├── passenger_wsgi.py            (we just created this)
├── app.py                        (copy from project)
├── requirements-godaddy.txt      (we just created this)
├── chroma_db/                    (from step 1 - pre-built)
│   ├── 2024-01-15/              (ChromaDB data)
│   └── ...
├── frontend/dist/               (from step 2 - React build)
│   ├── index.html
│   ├── assets/
│   └── ...
├── .htaccess                     (create below)
└── .env                          (create below)
```

---

## Step 4: Create `.htaccess`

Create file: `bofa-rag-deploy/.htaccess`

```apache
# Gzip compression
<IfModule mod_deflate.c>
  AddOutputFilterByType DEFLATE text/plain
  AddOutputFilterByType DEFLATE text/html
  AddOutputFilterByType DEFLATE text/xml
  AddOutputFilterByType DEFLATE application/xml
  AddOutputFilterByType DEFLATE application/json
  AddOutputFilterByType DEFLATE text/css
  AddOutputFilterByType DEFLATE text/javascript
  AddOutputFilterByType DEFLATE application/javascript
</IfModule>

# Browser caching
<IfModule mod_expires.c>
  ExpiresActive On
  ExpiresByType text/html "access plus 1 hour"
  ExpiresByType application/json "access plus 1 hour"
  ExpiresByType text/css "access plus 1 week"
  ExpiresByType text/javascript "access plus 1 week"
  ExpiresByType application/javascript "access plus 1 week"
</IfModule>

# Serve static files from frontend/dist
RewriteEngine On
RewriteBase /bofa-rag/

# API routes go to Flask
RewriteCond %{REQUEST_URI} ^/api/
RewriteRule ^(.*)$ index.py/$1 [QSA,L]

# Static files (JS, CSS, images)
RewriteCond %{REQUEST_FILENAME} -f
RewriteRule ^ - [QSA,L]

# Frontend React app for everything else
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ frontend/dist/index.html [L]
```

---

## Step 5: Create `.env` (Store Secrets Securely)

Create file: `bofa-rag-deploy/.env`

```bash
# AWS Bedrock credentials
AWS_ACCESS_KEY_ID=your_access_key_here
AWS_SECRET_ACCESS_KEY=your_secret_key_here
AWS_REGION=us-east-1

# Langfuse (optional)
LANGFUSE_PUBLIC_KEY=pk_xxx
LANGFUSE_SECRET_KEY=sk_xxx

# Flask
FLASK_ENV=production
```

⚠️ **IMPORTANT:** Don't commit `.env` to git! Only on server.

---

## Step 6: Upload via FTP

Using your GoDaddy FTP credentials:

```bash
# From your Mac terminal
ftp kingruss.siteurl.com

# Or use Cyberduck/FileZilla (easier GUI)
```

**Upload to:** `/public_html/bofa-rag/`

Contents:
- ✅ `flask_app.py`
- ✅ `passenger_wsgi.py`
- ✅ `app.py`
- ✅ `requirements-godaddy.txt`
- ✅ `chroma_db/` (entire folder)
- ✅ `frontend/dist/` (entire folder)
- ✅ `.htaccess`
- ✅ `.env`

---

## Step 7: Install Python Dependencies on GoDaddy

SSH into your account:

```bash
ssh kingruss@brownbi.com
cd public_html/bofa-rag

# Install dependencies
pip3 install --user -r requirements-godaddy.txt
```

---

## Step 8: Test the Deployment

```bash
# Check if Flask can start
python3 flask_app.py

# Or test via web browser
# Visit: https://brownbi.com/bofa-rag/
```

---

## Step 9: Monitor Logs

If something breaks:

```bash
ssh kingruss@brownbi.com
tail -f ~/logs/passenger.log
```

---

## File Sizes Reference

| Component | Size | Notes |
|-----------|------|-------|
| `flask_app.py` | ~8 KB | Flask app |
| `app.py` | ~25 KB | RAG pipeline |
| `chroma_db/` | ~300-500 MB | Pre-computed embeddings (largest!) |
| `frontend/dist/` | ~200-400 KB | React build |
| Python packages | ~100-200 MB | Installed via pip |
| **TOTAL** | ~600-800 MB | Should fit fine (you have ∞ space) |

---

## Troubleshooting

### Error: "No module named 'langchain_community'"
```bash
# Re-install requirements
pip3 install --user -r requirements-godaddy.txt
```

### Error: "Permission denied" on chroma_db
```bash
# Fix permissions
chmod -R 755 chroma_db/
```

### Error: "Flask app not found"
- Check `passenger_wsgi.py` exists
- Check `flask_app.py` exists
- Check paths in `passenger_wsgi.py`

### Slow first load / "504 Gateway Timeout"
- ChromaDB is loading from disk (takes 20-30 sec first time)
- This is normal! Subsequent requests are fast
- Pre-warm with: `curl https://brownbi.com/bofa-rag/api/health`

---

## Success Checklist

- [ ] `chroma_db/` built locally
- [ ] `frontend/dist/` built locally  
- [ ] All files uploaded to `/public_html/bofa-rag/`
- [ ] `requirements-godaddy.txt` installed on server
- [ ] `.env` file created on server with AWS credentials
- [ ] `.htaccess` in place
- [ ] Test: `https://brownbi.com/bofa-rag/api/health` returns 200
- [ ] Test: `https://brownbi.com/bofa-rag/` shows chat interface
- [ ] Test: Submit a query and get response

---

## Need Help?

1. Check `~/logs/passenger.log` for errors
2. SSH and test: `python3 flask_app.py` locally
3. Verify AWS credentials in `.env`
4. Check file permissions with `ls -la`

Good luck! 🚀

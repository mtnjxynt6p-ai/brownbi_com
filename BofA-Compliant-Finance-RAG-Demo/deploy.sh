#!/bin/bash
# GoDaddy Deployment Helper Script
# Run this locally to prepare everything for upload

set -e

echo "🚀 BofA RAG - GoDaddy Deployment Prep"
echo "======================================"
echo ""

# Step 1: Build Vector Database
echo "📚 Step 1: Building Vector Database..."
if [ -d "chroma_db" ]; then
    echo "⚠️  chroma_db already exists. Skipping rebuild."
    echo "   (Delete chroma_db/ if you want to rebuild)"
else
    python3 << 'EOF'
from app import ingest_docs
print("  Building embeddings...")
vectorstore = ingest_docs()
print("  ✅ Vector database created!")
EOF
fi
echo ""

# Step 2: Build React Frontend
echo "📦 Step 2: Building React Frontend..."
cd frontend
if [ -d "dist" ]; then
    echo "⚠️  dist/ already exists. Skipping rebuild."
    echo "   (Delete dist/ if you want to rebuild)"
else
    echo "  Installing dependencies..."
    npm install
    echo "  Building..."
    npm run build
    echo "  ✅ Frontend built!"
fi
cd ..
echo ""

# Step 3: Create deployment folder
echo "📂 Step 3: Creating Deployment Package..."
mkdir -p bofa-rag-deploy

# Copy files
cp flask_app.py bofa-rag-deploy/
cp passenger_wsgi.py bofa-rag-deploy/
cp app.py bofa-rag-deploy/
cp requirements-godaddy.txt bofa-rag-deploy/
cp .htaccess bofa-rag-deploy/ 2>/dev/null || echo "  (Create .htaccess manually)"

# Copy folders
cp -r chroma_db bofa-rag-deploy/ 2>/dev/null || echo "  (chroma_db not found)"
cp -r frontend/dist bofa-rag-deploy/frontend/ 2>/dev/null || echo "  (frontend/dist not found)"

echo "  ✅ Deployment package ready!"
echo ""

# Step 4: Summary
echo "📋 Deployment Package Contents:"
du -sh bofa-rag-deploy/
ls -lh bofa-rag-deploy/ | tail -n +2

echo ""
echo "✅ Ready for upload!"
echo ""
echo "Next steps:"
echo "1. Create .env file in bofa-rag-deploy/ with AWS credentials"
echo "2. Upload bofa-rag-deploy/ to /public_html/bofa-rag/ on GoDaddy"
echo "3. SSH and run: pip3 install --user -r requirements-godaddy.txt"
echo "4. Test: curl https://brownbi.com/bofa-rag/api/health"
echo ""
echo "For details, see GODADDY_DEPLOYMENT.md"

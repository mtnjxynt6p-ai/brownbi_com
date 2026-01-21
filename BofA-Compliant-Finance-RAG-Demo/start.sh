#!/bin/bash

# Quick start script for BofA-Compliant Finance RAG Demo
# Usage: ./start.sh

echo "=========================================="
echo "🏦 BofA-Compliant Finance RAG Demo"
echo "=========================================="
echo ""

# Check if virtual environment exists
if [ ! -d "bofa-rag" ]; then
    echo "📦 Creating virtual environment..."
    python3 -m venv bofa-rag
    echo "✅ Virtual environment created"
else
    echo "✅ Virtual environment found"
fi

# Activate virtual environment
echo "🔧 Activating virtual environment..."
source bofa-rag/bin/activate

# Check if dependencies are installed
if [ ! -f "bofa-rag/lib/python*/site-packages/langchain/__init__.py" ]; then
    echo "📦 Installing dependencies (this may take a few minutes)..."
    pip install -q --upgrade pip
    pip install -q -r requirements.txt
    echo "✅ Dependencies installed"
else
    echo "✅ Dependencies already installed"
fi

# Check for .env file
if [ ! -f ".env" ]; then
    echo ""
    echo "⚠️  WARNING: .env file not found!"
    echo "📝 Creating .env from template..."
    cp .env.example .env
    echo ""
    echo "⚠️  IMPORTANT: Edit .env file with your credentials before running:"
    echo "   - AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY (or use IAM role)"
    echo "   - LANGFUSE_PUBLIC_KEY and LANGFUSE_SECRET_KEY"
    echo "   - OPENAI_API_KEY (for DeepEval evaluation)"
    echo ""
    echo "Run: nano .env (or vim .env)"
    echo ""
    read -p "Press Enter after configuring .env, or Ctrl+C to exit..."
fi

echo ""
echo "=========================================="
echo "🚀 Starting RAG Demo..."
echo "=========================================="
echo ""
echo "Choose mode:"
echo "  1) Interactive CLI (default)"
echo "  2) Web API Server (FastAPI on port 5001)"
echo ""
read -p "Enter choice [1/2]: " choice

case $choice in
    2)
        echo ""
        echo "🌐 Starting FastAPI server..."
        echo "   Access Swagger UI at: http://localhost:5001/docs"
        echo "   Health check: http://localhost:5001/health"
        echo ""
        python -m uvicorn api_server:app --host 127.0.0.1 --port 5001 --reload
        ;;
    *)
        echo ""
        echo "💬 Starting interactive CLI..."
        echo ""
        python app.py
        ;;
esac

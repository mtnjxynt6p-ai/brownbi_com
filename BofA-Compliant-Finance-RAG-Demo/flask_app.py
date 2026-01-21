"""
Production Flask API Server for BofA RAG Demo
Optimized for GoDaddy shared hosting deployment

Features:
- WSGI-compatible for Passenger
- Lightweight dependencies
- CORS support
- Error handling
- Rate limiting
- Logging
"""

from flask import Flask, request, jsonify
from flask_cors import CORS
import os
import sys
import logging
from datetime import datetime
from functools import wraps
import time

# Add parent directory to path for imports
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))

# Import RAG components (lazy load to avoid startup issues)
RAG_AVAILABLE = False
query_pipeline = None
get_vectorstore = None
ingest_docs = None
vectorstore = None

def init_rag():
    """Lazy load RAG components on first use"""
    global RAG_AVAILABLE, query_pipeline, get_vectorstore, ingest_docs, vectorstore
    if RAG_AVAILABLE:
        return
    
    try:
        from app import query_pipeline as qp, get_vectorstore as gv, ingest_docs as id_func
        query_pipeline = qp
        get_vectorstore = gv
        ingest_docs = id_func
        RAG_AVAILABLE = True
        logger.info("RAG components loaded successfully")
    except ImportError as e:
        logger.error(f"Could not import RAG components: {e}")
        RAG_AVAILABLE = False

app = Flask(__name__)
CORS(app, origins=['https://brownbi.com', 'https://www.brownbi.com', 'http://localhost:3000'])

# Configure logging
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s',
    handlers=[
        logging.FileHandler('rag_api.log'),
        logging.StreamHandler()
    ]
)
logger = logging.getLogger(__name__)

# Simple rate limiting (in-memory, replace with Redis for production)
request_counts = {}
RATE_LIMIT = 10  # requests per minute
RATE_WINDOW = 60  # seconds

def rate_limit(f):
    @wraps(f)
    def decorated_function(*args, **kwargs):
        client_ip = request.remote_addr
        now = time.time()
        
        # Clean old entries
        request_counts[client_ip] = [
            timestamp for timestamp in request_counts.get(client_ip, [])
            if now - timestamp < RATE_WINDOW
        ]
        
        # Check rate limit
        if len(request_counts.get(client_ip, [])) >= RATE_LIMIT:
            return jsonify({
                'error': 'Rate limit exceeded. Please try again later.',
                'retry_after': 60
            }), 429
        
        # Add current request
        request_counts.setdefault(client_ip, []).append(now)
        
        return f(*args, **kwargs)
    return decorated_function

# Initialize vector store on first use
vectorstore = None

def get_vs():
    """Get vectorstore, loading on first access"""
    global vectorstore
    if vectorstore is None:
        try:
            init_rag()
            if RAG_AVAILABLE and get_vectorstore:
                if not os.path.exists('./chroma_db'):
                    logger.info("Vector database not found. Ingesting documents...")
                    vectorstore = ingest_docs()
                else:
                    logger.info("Loading existing vector database...")
                    vectorstore = get_vectorstore()
                logger.info("Vector store loaded successfully")
        except Exception as e:
            logger.error(f"Failed to load vector store: {e}")
    return vectorstore

@app.route('/')
def index():
    """API health check and info endpoint"""
    return jsonify({
        'service': 'Bank of America Responsible AI RAG API',
        'status': 'online',
        'version': '1.0.0',
        'rag_available': RAG_AVAILABLE and vectorstore is not None,
        'timestamp': datetime.now().isoformat(),
        'endpoints': {
            '/api/query': 'POST - Query the RAG system',
            '/api/health': 'GET - Health check',
            '/api/info': 'GET - System information'
        }
    })

@app.route('/api/health')
def health():
    """Health check endpoint"""
    health_status = {
        'status': 'healthy',
        'timestamp': datetime.now().isoformat(),
        'components': {
            'api': 'ok',
            'rag_engine': 'ok' if (RAG_AVAILABLE and vectorstore is not None) else 'unavailable',
            'vectorstore': 'ok' if vectorstore is not None else 'unavailable'
        }
    }
    
    status_code = 200 if all(v == 'ok' for v in health_status['components'].values()) else 503
    return jsonify(health_status), status_code

@app.route('/api/info')
def info():
    """System information endpoint"""
    try:
        vector_count = vectorstore._collection.count() if vectorstore else 0
    except:
        vector_count = 0
    
    return jsonify({
        'service': 'BofA Responsible AI RAG',
        'description': 'Production RAG system demonstrating responsible AI practices',
        'features': [
            'Guardrails (PII detection, prompt injection, toxicity)',
            'Observability (Langfuse tracing)',
            'Source citations',
            'Compliance logging'
        ],
        'vector_database': {
            'status': 'loaded' if vectorstore else 'not_loaded',
            'document_count': vector_count
        }
    })

@app.route('/api/query', methods=['POST'])
@rate_limit
def query():
    """
    Main RAG query endpoint
    
    Request:
        {
            "query": "What are BofA's AI ethics principles?",
            "use_guardrails": true,
            "trace": false
        }
    
    Response:
        {
            "query": "...",
            "answer": "...",
            "sources": [...],
            "input_guardrails": {...},
            "output_guardrails": {...},
            "trace_url": "..."
        }
    """
    try:
        # Validate request
        if not request.json:
            return jsonify({'error': 'Request must be JSON'}), 400
        
        query_text = request.json.get('query', '').strip()
        if not query_text:
            return jsonify({'error': 'Query text is required'}), 400
        
        if len(query_text) > 500:
            return jsonify({'error': 'Query exceeds maximum length of 500 characters'}), 400
        
        # Check if RAG is available
        vs = get_vs()
        if not RAG_AVAILABLE or vs is None:
            return jsonify({
                'error': 'RAG system is currently unavailable',
                'message': 'The AI service is temporarily offline. Please try again later.'
            }), 503
        
        # Extract options
        use_guardrails = request.json.get('use_guardrails', True)
        trace = request.json.get('trace', False)
        
        # Log request
        logger.info(f"Query received: {query_text[:100]}... from {request.remote_addr}")
        
        # Execute RAG pipeline
        init_rag()
        result = query_pipeline(
            query_text,
            vectorstore=vs,
            use_guardrails=use_guardrails,
            trace=trace
        )
        
        # Add metadata
        result['timestamp'] = datetime.now().isoformat()
        result['request_id'] = f"{int(time.time())}-{request.remote_addr.replace('.', '')}"
        
        logger.info(f"Query processed successfully. Sources: {len(result.get('sources', []))}")
        
        return jsonify(result), 200
    
    except Exception as e:
        logger.error(f"Error processing query: {str(e)}", exc_info=True)
        return jsonify({
            'error': 'Internal server error',
            'message': 'An error occurred processing your request. Please try again.',
            'timestamp': datetime.now().isoformat()
        }), 500

@app.route('/api/examples')
def examples():
    """Example queries to help users get started"""
    return jsonify({
        'examples': [
            {
                'category': 'Ethics & Principles',
                'queries': [
                    "What are the five core principles of Bank of America's Responsible AI framework?",
                    "How does BofA ensure accountability in AI decisions?"
                ]
            },
            {
                'category': 'Bias & Fairness',
                'queries': [
                    "How does Bank of America detect and mitigate bias in AI systems?",
                    "What fairness metrics does BofA use for AI models?"
                ]
            },
            {
                'category': 'Privacy & Security',
                'queries': [
                    "What are the PII handling requirements for Tier 1 data?",
                    "How does BofA protect customer data in AI applications?"
                ]
            },
            {
                'category': 'Compliance',
                'queries': [
                    "What regulations govern AI use in financial services?",
                    "How does BofA comply with the Fair Credit Reporting Act?"
                ]
            },
            {
                'category': 'Observability',
                'queries': [
                    "What metrics does BofA track for LLM applications?",
                    "How does BofA monitor AI system performance?"
                ]
            }
        ]
    })

@app.errorhandler(404)
def not_found(e):
    return jsonify({
        'error': 'Endpoint not found',
        'message': 'The requested endpoint does not exist. See / for available endpoints.'
    }), 404

@app.errorhandler(500)
def internal_error(e):
    logger.error(f"Internal server error: {str(e)}", exc_info=True)
    return jsonify({
        'error': 'Internal server error',
        'message': 'An unexpected error occurred. Please try again later.'
    }), 500

# WSGI application for Passenger/GoDaddy
application = app

if __name__ == '__main__':
    # Development server
    port = int(os.environ.get('PORT', 8000))
    debug = os.environ.get('FLASK_ENV') == 'development'
    
    logger.info(f"Starting Flask server on port {port} (debug={debug})")
    app.run(host='0.0.0.0', port=port, debug=debug)

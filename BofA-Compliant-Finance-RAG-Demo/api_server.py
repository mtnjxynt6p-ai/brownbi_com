"""
FastAPI wrapper for BofA-Compliant Finance RAG Demo.
Exposes REST API endpoints for web integration on brownbi.com.
"""

from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from typing import Optional
import os

# Import core RAG functions
from app import query_pipeline, get_vectorstore, ingest_docs

# Initialize FastAPI app
app = FastAPI(
    title="BofA-Compliant Finance RAG API",
    description="Responsible AI RAG system with guardrails and observability",
    version="1.0.0"
)

# Enable CORS for brownbi.com
app.add_middleware(
    CORSMiddleware,
    allow_origins=[
        "https://brownbi.com",
        "http://brownbi.com",
        "http://localhost",
        "http://localhost:8000"
    ],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Global vectorstore instance
vectorstore = None


@app.on_event("startup")
async def startup_event():
    """Initialize vector store on server startup."""
    global vectorstore
    print("\n" + "=" * 80)
    print("🚀 STARTING BOFA RAG API SERVER")
    print("=" * 80)
    
    if not os.path.exists("./chroma_db"):
        print("\n📦 Vector database not found. Ingesting documents...")
        vectorstore = ingest_docs()
    else:
        print(f"\n✅ Loading existing vector database from ./chroma_db")
        vectorstore = get_vectorstore()
    
    print("\n✅ API server ready. Endpoints available at /docs")
    print("=" * 80 + "\n")


# Request/Response models
class QueryRequest(BaseModel):
    query: str
    use_guardrails: bool = True

    class Config:
        schema_extra = {
            "example": {
                "query": "What are the five core principles of BofA's Responsible AI framework?",
                "use_guardrails": True
            }
        }


class QueryResponse(BaseModel):
    query: str
    answer: str
    sources: list[str]
    trace_url: Optional[str] = None
    guardrails_applied: bool


@app.get("/")
async def root():
    """Root endpoint with API information."""
    return {
        "message": "BofA-Compliant Finance RAG API",
        "version": "1.0.0",
        "endpoints": {
            "health": "/health",
            "query": "/query (POST)",
            "docs": "/docs",
            "openapi": "/openapi.json"
        }
    }


@app.get("/health")
async def health_check():
    """Health check endpoint for monitoring."""
    return {
        "status": "healthy",
        "vectorstore_loaded": vectorstore is not None,
        "vectorstore_size": vectorstore._collection.count() if vectorstore else 0
    }


@app.post("/query", response_model=QueryResponse)
async def query_rag(request: QueryRequest):
    """
    Process RAG query with guardrails and observability.
    
    Args:
        request: QueryRequest with user query and guardrails flag
    
    Returns:
        QueryResponse with answer, sources, and trace URL
    
    Raises:
        HTTPException: If query processing fails
    """
    if not vectorstore:
        raise HTTPException(
            status_code=503,
            detail="Vector store not initialized. Please try again in a few seconds."
        )
    
    # Validate query length
    if len(request.query) > 500:
        raise HTTPException(
            status_code=400,
            detail="Query too long. Maximum 500 characters."
        )
    
    if not request.query.strip():
        raise HTTPException(
            status_code=400,
            detail="Query cannot be empty."
        )
    
    try:
        # Execute RAG pipeline
        result = query_pipeline(
            query=request.query,
            vectorstore=vectorstore,
            use_guardrails=request.use_guardrails,
            trace=True
        )
        
        return QueryResponse(
            query=result["query"],
            answer=result["answer"],
            sources=result.get("sources", []),
            trace_url=result.get("trace_url"),
            guardrails_applied=request.use_guardrails
        )
        
    except Exception as e:
        print(f"❌ Error processing query: {e}")
        raise HTTPException(
            status_code=500,
            detail=f"Internal server error: {str(e)}"
        )


@app.get("/stats")
async def get_stats():
    """Get system statistics."""
    return {
        "vectorstore": {
            "loaded": vectorstore is not None,
            "total_chunks": vectorstore._collection.count() if vectorstore else 0,
            "persist_directory": "./chroma_db"
        },
        "model": {
            "llm": "anthropic.claude-3-5-sonnet-20241022-v2:0",
            "embeddings": "sentence-transformers/all-MiniLM-L6-v2"
        },
        "features": {
            "guardrails": "LLM Guard (PII, prompt injection, toxicity)",
            "observability": "Langfuse distributed tracing",
            "evaluation": "DeepEval with LLM-as-Judge"
        }
    }


# Run with: uvicorn api_server:app --host 127.0.0.1 --port 5000 --reload
if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="127.0.0.1", port 5000)

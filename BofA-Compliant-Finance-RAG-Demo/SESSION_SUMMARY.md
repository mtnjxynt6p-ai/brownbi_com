# Langfuse Trace URL Integration - Session Summary

## Date
January 14, 2026

## Objective
Fix Langfuse tracing to return direct trace URLs in API responses for full observability of the RAG pipeline.

## Problem
- Langfuse credentials were loading correctly
- Traces were being sent to Langfuse dashboard
- BUT: No way to get the trace URL back in the API response (`trace_url` was always `null`)

## Solution Journey

### Attempt 1: CallbackHandler Methods
- Tried: `hasattr(langfuse_handler, 'trace')`, `hasattr(langfuse_handler, 'trace_id')`
- Result: ❌ Attributes didn't exist

### Attempt 2: Langfuse Client Trace Creation
- Tried: `langfuse_client.trace()` to create trace explicitly
- Problem: `Langfuse` object has no attribute `trace`
- Result: ❌ Wrong approach

### Attempt 3: Wrong Import Path
- Tried: `from langfuse.callback import CallbackHandler`
- Result: ❌ Module doesn't exist (correct: `from langfuse.langchain import CallbackHandler`)

### Attempt 4: Invalid CallbackHandler Arguments
- Tried: `CallbackHandler(trace_id=trace_id)`, `CallbackHandler(session_id=..., user_id=...)`
- Result: ❌ CallbackHandler doesn't accept these parameters

### Attempt 5: Discovery & Success
- **Inspected handler attributes** after invocation: printed `dir(langfuse_handler)`
- **Found it**: `last_trace_id` attribute!
- **Solution**: Use `langfuse_handler.last_trace_id` to build trace URL

## Final Implementation

```python
# Create callback handler
if trace:
    langfuse_handler = CallbackHandler()  # No parameters needed
    callbacks = [langfuse_handler]

# After chain invocation
if trace and langfuse_handler:
    if hasattr(langfuse_handler, 'last_trace_id') and langfuse_handler.last_trace_id:
        langfuse_host = os.getenv('LANGFUSE_HOST', 'https://cloud.langfuse.com')
        trace_url = f"{langfuse_host}/trace/{langfuse_handler.last_trace_id}"
```

## Result
✅ **API Response Now Includes Direct Trace URLs**

Example:
```json
{
  "query": "What are BofA's responsible AI principles?",
  "answer": "According to [BofA_Responsible_AI_Framework_2024.md]...",
  "sources": ["BofA_Responsible_AI_Framework_2024.md"],
  "trace_url": "https://us.cloud.langfuse.com/trace/550e8400-e29b-41d4-a716-446655440000",
  "guardrails_applied": true
}
```

## Key Learnings

1. **CallbackHandler API**: Reads credentials from environment variables automatically. No initialization parameters needed.
2. **Trace ID Access**: Available via `handler.last_trace_id` after chain invocation (not before)
3. **Debugging Strategy**: When API docs don't help, inspect the actual object with `dir()` and `hasattr()`

## System Status

### ✅ Working Components
- RAG pipeline (AWS Bedrock Claude 3 Sonnet)
- Vector store (Chroma with BofA documents)
- Input guardrails (PromptInjection, Toxicity, BanTopics)
- Output guardrails (Sensitive, NoRefusal, Relevance)
- Langfuse tracing with direct trace URLs
- OpenAI API integration (for evaluation)
- FastAPI /query endpoint
- Auto-reload development server

### 📊 Observable via Langfuse Dashboard
- Token usage per query
- Chain execution steps
- Latency metrics
- Input/output values
- Error tracking

## Next Steps (Optional)
1. Test adversarial queries to showcase guardrails blocking malicious input
2. Run evaluation suite with DeepEval
3. Deploy to production
4. Create demo script for interview

## Git Commit
```
feat: Add Langfuse trace URL to API response using last_trace_id

- Import uuid for trace generation
- Use CallbackHandler() to capture Langfuse traces
- Extract trace URL using handler.last_trace_id
- Build direct link to Langfuse dashboard for each query
- Provides full observability of RAG pipeline execution
```

Commit: `a1a7ce7`

## Files Modified
- `app.py`: Added trace URL extraction logic
- `.env`: (No changes - credentials already properly formatted)

---

**Status**: ✅ Langfuse integration complete and working with direct trace URLs in API responses

#!/usr/bin/env python3
"""
Quick LangChain AWS Bedrock Chat App
===================================
Simple CLI chat interface using your existing AWS Bedrock connection.
"""

import os
from dotenv import load_dotenv
from langchain_aws import ChatBedrock
from langchain_core.prompts import ChatPromptTemplate
from langchain_core.output_parsers import StrOutputParser

# Load environment variables (same .env as your main app)
load_dotenv()

# Configuration - same as your main app
AWS_REGION = os.getenv("AWS_REGION", "us-east-1")
BEDROCK_MODEL_ID = "anthropic.claude-3-sonnet-20240229-v1:0"

def create_chat_chain():
    """Create a simple LangChain chat chain with Claude 3.5 Sonnet."""
    
    # Initialize Bedrock LLM
    llm = ChatBedrock(
        model_id=BEDROCK_MODEL_ID,
        region_name=AWS_REGION,
        model_kwargs={
            "temperature": 0.7,
            "max_tokens": 1500,
            "top_p": 0.9
        }
    )
    
    # Create prompt template
    prompt_template = ChatPromptTemplate.from_messages([
        ("system", "You are a helpful AI assistant. Be concise but thorough in your responses."),
        ("human", "{user_input}")
    ])
    
    # Create chain: prompt -> llm -> parser
    chain = prompt_template | llm | StrOutputParser()
    
    return chain

def main():
    """Run interactive chat session."""
    
    print("🤖 LangChain + AWS Bedrock Chat")
    print("=" * 40)
    print(f"Model: {BEDROCK_MODEL_ID}")
    print(f"Region: {AWS_REGION}")
    print("Type 'quit' or 'exit' to end the session")
    print("=" * 40)
    
    try:
        # Initialize chat chain
        chat_chain = create_chat_chain()
        
        while True:
            # Get user input
            user_input = input("\n🧑 You: ").strip()
            
            if user_input.lower() in ['quit', 'exit', 'q']:
                print("👋 Goodbye!")
                break
            
            if not user_input:
                continue
            
            try:
                # Get response from Claude
                print("🤖 Claude: ", end="", flush=True)
                response = chat_chain.invoke({"user_input": user_input})
                print(response)
                
            except Exception as e:
                print(f"❌ Error: {e}")
                print("Make sure your AWS credentials are configured and Bedrock access is enabled.")
    
    except KeyboardInterrupt:
        print("\n👋 Chat ended by user.")
    except Exception as e:
        print(f"❌ Failed to initialize: {e}")
        print("\nTroubleshooting:")
        print("1. Check your .env file has AWS credentials")
        print("2. Verify AWS Bedrock access in us-east-1")
        print("3. Ensure Claude 3.5 Sonnet model access is enabled")

if __name__ == "__main__":
    main()
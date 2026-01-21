"""
Passenger WSGI entry point for GoDaddy hosting
This file is required by Passenger to run the Flask app
"""

import sys
import os

# Set Python path
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))

# Import the Flask app
from flask_app import app as application

# Ensure app runs in production mode
os.environ['FLASK_ENV'] = 'production'
os.environ['PYTHONUNBUFFERED'] = '1'

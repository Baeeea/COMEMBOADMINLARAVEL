#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import sys
import os
import json

# Add the app/Services directory to the path
current_dir = os.path.dirname(os.path.abspath(__file__))
services_dir = os.path.join(current_dir, 'app', 'Services')
sys.path.append(services_dir)

# Import from our fixed service
from SentimentAnalysisService_fixed import SentimentAnalysisService

def test_example_complaint():
    """Test the sentiment analysis with a specific example complaint"""

    # Initialize service
    service = SentimentAnalysisService()

    # Test text from your example
    test_text = "It is late at night pero ung mga kapitbahay ang lakas ng videoke hindi kami makatulog Nakakainis na at paulit-ulit"

    print("TESTING SENTIMENT ANALYSIS")
    print("=" * 50)
    print(f"Original text: {test_text}")
    print()

    # Step 1: Tokenization
    tokens = service.tokenize_text(test_text)
    print("Step 1 - Tokenization:")
    print(", ".join([f'"{token}"' for token in tokens]))
    print()

    # Step 2: Elimination
    filtered_tokens = service.eliminate_stop_words(tokens)
    print("Step 2 - Elimination (after removing stop words):")
    print(", ".join([f'"{token}"' for token in filtered_tokens]))
    print()

    # Step 3: Context Understanding
    sentiment, scores = service.context_understanding(filtered_tokens)
    print("Step 3 - Context Understanding:")
    print(f"Negative: {scores['negative']}%")
    print(f"Neutral: {scores['neutral']}%")
    print(f"Positive: {scores['positive']}%")
    print()
    print(f"Final Sentiment: {sentiment.upper()}")
    print("=" * 50)

    # Test with other examples
    examples = [
        "The neighbors are playing loud music at night, I can't sleep.",
        "Thank you for addressing my concerns about the noise issue.",
        "The garbage collection in our area is irregular.",
        "There are stray dogs roaming in our neighborhood."
    ]

    print("\nTESTING ADDITIONAL EXAMPLES:")
    for i, text in enumerate(examples, 1):
        result = service.analyze_sentiment_detailed(text)
        print(f"\nExample {i}: {text}")
        print(f"Sentiment: {result['sentiment'].upper()}")
        print(f"Scores: Negative={result['scores']['negative']}%, Positive={result['scores']['positive']}%, Neutral={result['scores']['neutral']}%")
        print(f"Key tokens: {', '.join(result['tokens'])}")
        print("-" * 50)

if __name__ == "__main__":
    test_example_complaint()

#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import sys
import os
sys.path.append(os.path.join(os.path.dirname(__file__), 'app', 'Services'))

from SentimentAnalysisService import SentimentAnalysisService

def test_sentiment_analysis():
    """Test the new 3-step sentiment analysis"""

    # Initialize service
    service = SentimentAnalysisService()

    # Test text from your example
    test_text = "It is late at night pero ung mga kapitbahay ang lakas ng videoke hindi kami makatulog Nakakainis na at paulit-ulit"

    print("Testing Sentiment Analysis")
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
    print()

    # Test with detailed analysis
    detailed = service.analyze_sentiment_detailed(test_text)
    print("Detailed Analysis Result:")
    print(f"Sentiment: {detailed['sentiment']}")
    print(f"Scores: {detailed['scores']}")

if __name__ == "__main__":
    test_sentiment_analysis()

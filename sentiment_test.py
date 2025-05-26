#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""
Simple test script for the sentiment analysis service
"""

import sys
import os
import json

# Define the sample text for testing
sample_text = "It is late at night pero ung mga kapitbahay ang lakas ng videoke hindi kami makatulog Nakakainis na at paulit-ulit."

def tokenize_text(text):
    """Step 1: Tokenization - Split text into tokens"""
    if not text:
        return []

    # Convert to lowercase and split into words
    text = text.lower()
    # Keep Filipino and English characters, including common Filipino punctuation
    import re
    text = re.sub(r'[^\w\s]', ' ', text)
    tokens = text.split()

    return tokens

def eliminate_stop_words(tokens):
    """Step 2: Elimination - Remove stop words and irrelevant words"""
    # Common English stop words
    english_stop_words = {
        'the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for',
        'of', 'with', 'by', 'is', 'are', 'was', 'were', 'be', 'been', 'have',
        'has', 'had', 'do', 'does', 'did', 'will', 'would', 'could', 'should',
        'it', 'its', 'this', 'that', 'these', 'those', 'i', 'you', 'he', 'she',
        'we', 'they', 'me', 'him', 'her', 'us', 'them', 'my', 'your', 'his',
        'her', 'our', 'their'
    }

    # Common Filipino stop words
    filipino_stop_words = {
        'ang', 'ng', 'sa', 'na', 'at', 'ay', 'mga', 'ung', 'yung', 'kung',
        'para', 'ako', 'ka', 'siya', 'kami', 'kayo', 'sila', 'ko', 'mo', 'niya',
        'namin', 'ninyo', 'nila', 'akin', 'iyo', 'kaniya', 'amin', 'inyo', 'kanila'
    }

    all_stop_words = english_stop_words.union(filipino_stop_words)

    # Filter out stop words and very short words
    filtered_tokens = [token for token in tokens if token not in all_stop_words and len(token) > 2]

    return filtered_tokens

def get_sentiment_lexicon():
    """Get sentiment lexicon for Filipino and English words"""
    return {
        # Negative words
        'negative': {
            # English negative words
            'bad', 'terrible', 'awful', 'horrible', 'worst', 'hate', 'angry',
            'frustrated', 'disappointed', 'complaint', 'problem', 'issue',
            'annoying', 'disturbing', 'loud', 'noisy', 'late', 'night',
            # Filipino negative words
            'pangit', 'galit', 'hindi', 'masama', 'problema', 'reklamo',
            'ingay', 'maingay', 'basura', 'dumi', 'amoy', 'nakakainis',
            'paulit', 'ulit', 'kapitbahay', 'lakas', 'videoke', 'makatulog',
            'nakakabadtrip', 'badtrip', 'stress', 'nakakastress'
        },
        # Positive words
        'positive': {
            # English positive words
            'good', 'great', 'excellent', 'amazing', 'wonderful', 'fantastic',
            'satisfied', 'happy', 'pleased', 'thank', 'appreciate', 'nice',
            'beautiful', 'clean', 'quiet', 'peaceful',
            # Filipino positive words
            'maganda', 'salamat', 'masaya', 'satisfied', 'ok', 'ayos',
            'tahimik', 'malinis', 'ganda', 'okay'
        },
        # Neutral words
        'neutral': {
            # Time/descriptive words that can be neutral
            'time', 'day', 'night', 'morning', 'afternoon', 'evening',
            'house', 'home', 'place', 'area', 'street', 'neighbor'
        }
    }

def context_understanding(tokens):
    """Step 3: Context Understanding - Analyze meaningful words and calculate sentiment"""
    lexicon = get_sentiment_lexicon()

    negative_score = 0
    positive_score = 0
    neutral_score = 0

    token_analysis = {}

    # Analyze each token
    for token in tokens:
        if token in lexicon['negative']:
            negative_score += 1
            token_analysis[token] = 'negative'
        elif token in lexicon['positive']:
            positive_score += 1
            token_analysis[token] = 'positive'
        elif token in lexicon['neutral']:
            neutral_score += 1
            token_analysis[token] = 'neutral'
        else:
            token_analysis[token] = 'unknown'

    total_sentiment_words = negative_score + positive_score + neutral_score

    if total_sentiment_words == 0:
        return 'neutral', {'negative': 0, 'positive': 0, 'neutral': 100}, token_analysis

    # Calculate percentages
    negative_percentage = (negative_score / total_sentiment_words) * 100
    positive_percentage = (positive_score / total_sentiment_words) * 100
    neutral_percentage = (neutral_score / total_sentiment_words) * 100

    scores = {
        'negative': round(negative_percentage, 2),
        'positive': round(positive_percentage, 2),
        'neutral': round(neutral_percentage, 2)
    }

    # Determine overall sentiment
    if negative_percentage > positive_percentage and negative_percentage > neutral_percentage:
        sentiment = 'negative'
    elif positive_percentage > negative_percentage and positive_percentage > neutral_percentage:
        sentiment = 'positive'
    else:
        sentiment = 'neutral'

    return sentiment, scores, token_analysis

def analyze_text(text):
    print("Analyzing text: ", text)
    print("\nStep 1: Tokenization")
    tokens = tokenize_text(text)
    print("Tokens:", tokens)

    print("\nStep 2: Elimination (remove stop words)")
    filtered_tokens = eliminate_stop_words(tokens)
    print("Filtered tokens:", filtered_tokens)

    print("\nStep 3: Context Understanding")
    sentiment, scores, token_analysis = context_understanding(filtered_tokens)

    print("\nToken Analysis:")
    for token, category in token_analysis.items():
        print(f"  {token}: {category}")

    print("\nSentiment Scores:")
    print(f"  Negative: {scores['negative']}%")
    print(f"  Positive: {scores['positive']}%")
    print(f"  Neutral: {scores['neutral']}%")

    print("\nFinal Sentiment:", sentiment.upper())

    return {
        'text': text,
        'sentiment': sentiment,
        'scores': scores,
        'tokens': filtered_tokens,
        'token_analysis': token_analysis
    }

if __name__ == "__main__":
    # Get text from command line or use default sample
    text = ' '.join(sys.argv[1:]) if len(sys.argv) > 1 else sample_text

    # Analyze the text
    result = analyze_text(text)

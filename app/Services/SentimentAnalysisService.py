#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import sys
import json
import re
import mysql.connector
from textblob import TextBlob
import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.naive_bayes import MultinomialNB
from sklearn.pipeline import Pipeline
import pickle
import os

class SentimentAnalysisService:
    def __init__(self, db_config=None):
        self.db_config = db_config or {
            "host": "localhost",
            "user": "root",
            "password": "",
            "database": "comembo"
        }
        self.model_path = os.path.join(os.path.dirname(__file__), 'sentiment_model.pkl')
        self.pipeline = None
        self.load_or_train_model()

    def tokenize_text(self, text):
        """Step 1: Tokenization - Split text into tokens"""
        if not text:
            return []
        text = text.lower()
        text = re.sub(r'[^\w\s]', ' ', text)
        tokens = text.split()
        return tokens

    def eliminate_stop_words(self, tokens):
        """Step 2: Elimination - Remove stop words and irrelevant words"""
        english_stop_words = {
            'the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for',
            'of', 'with', 'by', 'is', 'are', 'was', 'were', 'be', 'been', 'have',
            'has', 'had', 'do', 'does', 'did', 'will', 'would', 'could', 'should',
            'it', 'its', 'this', 'that', 'these', 'those', 'i', 'you', 'he', 'she',
            'we', 'they', 'me', 'him', 'her', 'us', 'them', 'my', 'your', 'his',
            'her', 'our', 'their'
        }
        filipino_stop_words = {
            'ang', 'ng', 'sa', 'na', 'at', 'ay', 'mga', 'ung', 'yung', 'kung',
            'para', 'ako', 'ka', 'siya', 'kami', 'kayo', 'sila', 'ko', 'mo', 'niya',
            'namin', 'ninyo', 'nila', 'akin', 'iyo', 'kaniya', 'amin', 'inyo', 'kanila'
        }
        all_stop_words = english_stop_words.union(filipino_stop_words)
        filtered_tokens = [token for token in tokens if token not in all_stop_words and len(token) > 2]
        return filtered_tokens

    def get_sentiment_lexicon(self):
        return {
            'negative': {
                'bad', 'terrible', 'awful', 'horrible', 'worst', 'hate', 'angry',
                'frustrated', 'disappointed', 'complaint', 'problem', 'issue',
                'annoying', 'disturbing', 'loud', 'noisy', 'late', 'night',
                'pangit', 'galit', 'hindi', 'masama', 'problema', 'reklamo',
                'ingay', 'maingay', 'basura', 'dumi', 'amoy', 'nakakainis',
                'paulit', 'ulit', 'kapitbahay', 'lakas', 'videoke', 'makatulog',
                'nakakabadtrip', 'badtrip', 'stress', 'nakakastress',
                # Infrastructure and environmental issues
                'sewage', 'leaking', 'leak', 'broken', 'damaged', 'dirty', 'filthy',
                'smelly', 'stinking', 'polluted', 'contaminated', 'clogged', 'blocked',
                'overflow', 'flooding', 'flooded', 'waste', 'garbage', 'trash',
                'pothole', 'cracked', 'unsafe', 'dangerous', 'hazardous', 'broken',
                'malfunctioning', 'defective', 'deteriorating', 'rotting', 'rusty',
                'corroded', 'collapsed', 'faulty', 'poor', 'inadequate', 'insufficient',
                'lacking', 'missing', 'absent', 'unavailable', 'inaccessible',
                # Water and sanitation issues
                'sewerage', 'drainage', 'plumbing', 'pipe', 'pipes', 'sewer',
                'septic', 'overflow', 'backup', 'clog', 'blockage', 'burst',
                'rupture', 'crack', 'hole', 'opening', 'breach', 'damage',
                # Road and infrastructure
                'pothole', 'crater', 'bump', 'uneven', 'rough', 'damaged',
                'deteriorated', 'worn', 'eroded', 'crumbling', 'breaking',
                # Safety and health concerns
                'unsanitary', 'unhygienic', 'health', 'risk', 'hazard', 'danger',
                'threat', 'unsafe', 'contamination', 'infection', 'disease',
                'illness', 'sick', 'poison', 'toxic', 'harmful', 'unhealthy'
            },
            'positive': {
                'good', 'great', 'excellent', 'amazing', 'wonderful', 'fantastic',
                'satisfied', 'happy', 'pleased', 'thank', 'appreciate', 'nice',
                'beautiful', 'clean', 'quiet', 'peaceful',
                'maganda', 'salamat', 'masaya', 'satisfied', 'ok', 'ayos',
                'tahimik', 'malinis', 'ganda', 'okay'
            },
            'neutral': {
                'time', 'day', 'night', 'morning', 'afternoon', 'evening',
                'house', 'home', 'place', 'area', 'street', 'neighbor',
                'water', 'road', 'sidewalk', 'pavement', 'construction',
                'repair', 'maintenance', 'work', 'project', 'installation',
                'location', 'address', 'building', 'structure', 'facility',
                'infrastructure', 'system', 'service', 'utility', 'public',
                'government', 'municipal', 'city', 'barangay', 'community'
            }
        }

    def context_understanding(self, tokens):
        lexicon = self.get_sentiment_lexicon()
        negative_score = 0
        positive_score = 0
        neutral_score = 0
        
        # Track which tokens matched which sentiment
        matched_tokens = {
            'negative': [],
            'positive': [],
            'neutral': []
        }
        
        for token in tokens:
            if token in lexicon['negative']:
                negative_score += 1
                matched_tokens['negative'].append(token)
            elif token in lexicon['positive']:
                positive_score += 1
                matched_tokens['positive'].append(token)
            elif token in lexicon['neutral']:
                neutral_score += 1
                matched_tokens['neutral'].append(token)
        
        total_sentiment_words = negative_score + positive_score + neutral_score
        
        # If no sentiment words found, return neutral
        if total_sentiment_words == 0:
            return 'neutral', {'negative': 0, 'positive': 0, 'neutral': 100}, matched_tokens
            
        # Calculate percentages
        negative_percentage = (negative_score / total_sentiment_words) * 100
        positive_percentage = (positive_score / total_sentiment_words) * 100
        neutral_percentage = (neutral_score / total_sentiment_words) * 100
        
        scores = {
            'negative': round(negative_percentage, 2),
            'positive': round(positive_percentage, 2),
            'neutral': round(neutral_percentage, 2)
        }
        
        # Improved decision logic: 
        # If there are ANY negative words, and they make up at least 25% of sentiment words, classify as negative
        # This handles cases like "sewage water leaking" where "sewage" and "leaking" are negative
        # but "water" is neutral
        if negative_score > 0 and (negative_percentage >= 25 or negative_score >= positive_score):
            return 'negative', scores, matched_tokens
        elif positive_score > 0 and positive_percentage > negative_percentage and positive_percentage >= 25:
            return 'positive', scores, matched_tokens
        else:
            return 'neutral', scores, matched_tokens

    def clean_text(self, text):
        if not text:
            return ""
        text = text.lower()
        text = re.sub(r'[^a-zA-Z\s]', '', text)
        text = ' '.join(text.split())
        return text

    def load_training_data(self):
        try:
            training_files = [
                os.path.join(os.path.dirname(__file__), '../../sentimentAnalysis-main/Lexicon/twitter_training.csv'),
                os.path.join(os.path.dirname(__file__), '../../sentimentAnalysis-main/Lexicon/twitter_validation.csv')
            ]
            dfs = []
            for file in training_files:
                if os.path.exists(file):
                    df = pd.read_csv(file, encoding='utf-8', header=None)
                    if df.shape[1] >= 3:
                        df = df[[1, 2]]
                        df.columns = ['sentiment', 'text']
                        dfs.append(df)
            if dfs:
                combined_df = pd.concat(dfs, ignore_index=True)
                sentiment_mapping = {
                    'Positive': 'positive',
                    'Negative': 'negative',
                    'Neutral': 'neutral',
                    'positive': 'positive',
                    'negative': 'negative',
                    'neutral': 'neutral'
                }
                combined_df['sentiment'] = combined_df['sentiment'].map(sentiment_mapping)
                combined_df = combined_df.dropna()
                return combined_df
            else:
                return pd.DataFrame({
                    'text': ['This is great!', 'This is terrible', 'This is okay'],
                    'sentiment': ['positive', 'negative', 'neutral']
                })
        except Exception as e:
            print(f"Error loading training data: {e}")
            return pd.DataFrame({
                'text': ['This is great!', 'This is terrible', 'This is okay'],
                'sentiment': ['positive', 'negative', 'neutral']
            })

    def train_model(self):
        try:
            df = self.load_training_data()
            df['text'] = df['text'].apply(self.clean_text)
            self.pipeline = Pipeline([
                ('tfidf', TfidfVectorizer(max_features=5000, stop_words='english')),
                ('classifier', MultinomialNB())
            ])
            self.pipeline.fit(df['text'], df['sentiment'])
            with open(self.model_path, 'wb') as f:
                pickle.dump(self.pipeline, f)
            return True
        except Exception as e:
            print(f"Error training model: {e}")
            return False

    def load_model(self):
        try:
            with open(self.model_path, 'rb') as f:
                self.pipeline = pickle.load(f)
            return True
        except:
            return False

    def load_or_train_model(self):
        if not self.load_model():
            print("Training new sentiment analysis model...")
            self.train_model()

    def analyze_sentiment_textblob(self, text):
        try:
            blob = TextBlob(text)
            polarity = blob.sentiment.polarity
            if polarity > 0.1:
                return 'positive'
            elif polarity < -0.1:
                return 'negative'
            else:
                return 'neutral'
        except:
            return 'neutral'

    def analyze_sentiment(self, text):
        if not text:
            return 'neutral'
        try:
            tokens = self.tokenize_text(text)
            filtered_tokens = self.eliminate_stop_words(tokens)
            sentiment, scores, matched_tokens = self.context_understanding(filtered_tokens)
            return sentiment
        except Exception as e:
            print(f"Error analyzing sentiment: {e}")
            return self.analyze_sentiment_textblob(text)

    def analyze_sentiment_detailed(self, text):
        if not text:
            return {
                'sentiment': 'neutral', 
                'scores': {'negative': 0, 'positive': 0, 'neutral': 100}, 
                'tokens': [],
                'matched_tokens': {'negative': [], 'positive': [], 'neutral': []}
            }
        try:
            tokens = self.tokenize_text(text)
            filtered_tokens = self.eliminate_stop_words(tokens)
            sentiment, scores, matched_tokens = self.context_understanding(filtered_tokens)
            return {
                'sentiment': sentiment,
                'scores': scores,
                'tokens': filtered_tokens,
                'original_tokens': tokens,
                'matched_tokens': matched_tokens
            }
        except Exception as e:
            print(f"Error analyzing sentiment: {e}")
            return {
                'sentiment': 'neutral',
                'scores': {'negative': 0, 'positive': 0, 'neutral': 100},
                'tokens': [],
                'matched_tokens': {'negative': [], 'positive': [], 'neutral': []},
                'error': str(e)
            }

    def analyze_complaint_sentiment(self, complaint_text):
        sentiment = self.analyze_sentiment(complaint_text)
        if sentiment == 'positive':
            return 'neutral'
        return sentiment

    def batch_analyze_complaints(self):
        try:
            conn = mysql.connector.connect(**self.db_config)
            cursor = conn.cursor()
            cursor.execute("""
                SELECT user_id, specific_description
                FROM complaintrequests
                WHERE specific_description IS NOT NULL
                AND specific_description != ''
            """)
            complaints = cursor.fetchall()
            results = []
            for user_id, description in complaints:
                sentiment = self.analyze_complaint_sentiment(description)
                results.append({
                    'user_id': user_id,
                    'sentiment': sentiment,
                    'description': description[:100] + '...' if len(description) > 100 else description
                })
            cursor.close()
            conn.close()
            return results
        except Exception as e:
            print(f"Error in batch analysis: {e}")
            return []

def main():
    if len(sys.argv) < 2:
        print("Usage: python SentimentAnalysisService.py <text_to_analyze>")
        sys.exit(1)
    service = SentimentAnalysisService()
    text = ' '.join(sys.argv[1:])
    detailed_result = service.analyze_sentiment_detailed(text)
    result = {
        'text': text,
        'sentiment': detailed_result['sentiment'],
        'scores': detailed_result['scores'],
        'tokens_analyzed': detailed_result['tokens'],
        'original_tokens': detailed_result['original_tokens'],
        'matched_tokens': detailed_result.get('matched_tokens', {'negative': [], 'positive': [], 'neutral': []}),
        'success': True
    }
    print(json.dumps(result))

if __name__ == "__main__":
    main()

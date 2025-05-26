#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import sys
import json
import re
import traceback

class SentimentAnalysisService:
    def __init__(self):
        self.debug = True
        print("[DEBUG] SentimentAnalysisService initialized", file=sys.stderr)
        self.log_debug("Init complete")

    def log_debug(self, message, data=None):
        """Print debug info to stderr so it doesn't interfere with JSON output"""
        try:
            if self.debug:
                if data:
                    print(f"[DEBUG] {message}: {json.dumps(data, ensure_ascii=False)}", file=sys.stderr)
                else:
                    print(f"[DEBUG] {message}", file=sys.stderr)
        except Exception as e:
            print(f"[DEBUG] Error logging: {str(e)}", file=sys.stderr)

    def tokenize_text(self, text):
        """Step 1: Tokenization - Split text into tokens"""
        if not text:
            return []

        # Convert to lowercase and split into words
        text = text.lower()
        # Keep Filipino and English characters, including common Filipino punctuation
        text = re.sub(r'[^\w\s]', ' ', text)
        tokens = text.split()

        self.log_debug("Tokenization result", {'tokens': tokens})
        return tokens

    def get_sentiment_lexicon(self):
        """Get sentiment lexicon for Filipino and English words"""
        return {
            'negative': {
                # English negative words - comprehensive barangay complaint terms
                'bad', 'terrible', 'awful', 'horrible', 'worst', 'hate', 'angry',
                'frustrated', 'disappointed', 'complaint', 'problem', 'issue',
                'annoying', 'disturbing', 'loud', 'noisy', 'late', 'night',
                'violation', 'dispute', 'disturbance', 'nuisance', 'harassment', 'conflict',
                'garbage', 'trash', 'waste', 'smell', 'odor', 'stench', 'dirty', 'filthy',
                'stray', 'dogs', 'rats', 'pests', 'flooding', 'leak', 'drainage', 'clogged',
                'drunk', 'fighting', 'shouting', 'screaming', 'damage', 'broken', 'damaged',
                'blocked', 'obstruction', 'illegal', 'danger', 'unsafe', 'threat', 'hazard',
                'unbearable', 'intolerable', 'excessive', 'unresolved', 'ignored', 'neglected',
                'smoking', 'urinating', 'defecating', 'vandalism', 'graffiti', 'leaking',
                'stealing', 'theft', 'robbery', 'trespassing', 'breaking', 'destroying',
                'disruptive', 'messy', 'inconsiderate', 'rude', 'ignore', 'negligent',
                'uncooperative', 'irresponsible', 'unreasonable', 'grievance', 'inconvenience',

                # Filipino negative words - comprehensive barangay complaint terms
                'pangit', 'galit', 'hindi', 'masama', 'problema', 'reklamo', 'away',
                'ingay', 'maingay', 'basura', 'dumi', 'amoy', 'nakakainis', 'nakakairita',
                'mabaho', 'baho', 'kalat', 'makalat', 'marumi', 'dumumi',
                'sigaw', 'sisigaw', 'away', 'nag-aaway', 'basag', 'sira',
                'nakakagalit', 'nakakabwisit', 'nakakayamot', 'nakakairita',
                'lasing', 'nagkakalat', 'iniwanan', 'tinapon', 'tinambak',
                'nakaharang', 'harang', 'sagabal', 'nakasagabal', 'walang',
                'tigil', 'hinto', 'respeto', 'modo', 'tumagas', 'tumutulo', 'baha',
                'bumabaha', 'sagabal', 'ginugulo', 'gulo', 'kaguluhan', 'nakakasira',
                'sinisira', 'nagtatapon', 'nagiinuman', 'nag-iingay', 'nagkalat',
                'nakakasagabal', 'pasaway', 'matigas', 'ulo', 'matigas ang ulo',
                'nakakatakot', 'delikado', 'mapanganib', 'insekto', 'lamok',
                'ipis', 'daga', 'sunog', 'apoy', 'usok', 'alipunga',
                'balahura', 'walanghiya', 'walangya', 'punyeta', 'asar',
                'nakakaasar', 'masakit', 'masaklap', 'inaabuso', 'inaapi',
                'dinudumog', 'naiistorbo', 'istorbo', 'kaingayan', 'kalakasan',
                'nagdidiskarte', 'bumabasag', 'sumisigaw', 'nagwawala', 'nagdadabog',
                'nanggugulo', 'nananakit', 'nanggugulpi', 'nananakot', 'tinatakot',
                'nambubully', 'nambabastos', 'nanghihimasok', 'naninira', 'naninirang puri',
                'nakakabother', 'kinakabahan', 'natataranta', 'nababahala', 'naiinis',
                'guguluhin', 'nakakadistorbo', 'masamang', 'sirang', 'wasak', 'sira-sira',
                'magulo', 'magaslaw', 'maasim', 'malakas', 'nakakasulasok', 'nakakasuka',
                'pilahan', 'barado', 'bulok', 'nabubulok', 'sumisipsip', 'lumalabag'
            },
            'positive': {
                # English positive words
                'good', 'great', 'excellent', 'amazing', 'wonderful', 'fantastic',
                'satisfied', 'happy', 'pleased', 'thank', 'appreciate', 'nice',
                'beautiful', 'clean', 'quiet', 'peaceful', 'resolved', 'fixed',
                'solution', 'helped', 'resolved', 'improved', 'understanding',
                # Filipino positive words
                'maganda', 'salamat', 'masaya', 'satisfied', 'ok', 'ayos', 'napagayos',
                'tahimik', 'malinis', 'ganda', 'okay', 'maayos', 'nagkaayos', 'naayos',
                'tulong', 'makatulong', 'nagtulong', 'maintindihan', 'naintindihan',
                'maganda', 'magaling', 'mabilis', 'tama', 'mahusay'
            },
            'neutral': {
                # Time words
                'time', 'day', 'night', 'morning', 'afternoon', 'evening', 'today',
                'yesterday', 'tomorrow', 'week', 'month', 'always', 'sometimes',
                # Location words
                'house', 'home', 'place', 'area', 'street', 'neighbor', 'barangay',
                'community', 'residence', 'building', 'subdivision', 'village',
                # Action words (neutral)
                'report', 'inform', 'ask', 'request', 'need', 'want', 'going',
                'happened', 'happening', 'occur', 'continue', 'still', 'already'
            }
        }

    def get_word_weights(self):
        """Get weights for specific words that should count more heavily in sentiment analysis"""
        return {
            # High severity words (weight: 2.5)
            'emergency': 2.5, 'fire': 2.5, 'sunog': 2.5, 'death': 2.5, 'dying': 2.5,
            'knife': 2.5, 'gun': 2.5, 'weapon': 2.5, 'blood': 2.5, 'bleeding': 2.5,
            'drugs': 2.5, 'shabu': 2.5, 'rape': 2.5, 'assault': 2.5, 'murder': 2.5,
            'killed': 2.5, 'attacked': 2.5, 'collapsed': 2.5, 'toxic': 2.5,
            'patay': 2.5, 'gulo': 2.5, 'away': 2.5, 'barilan': 2.5, 'holdap': 2.5,

            # Distress words (weight: 2.0)
            'unbearable': 2.0, 'intolerable': 2.0, 'suffering': 2.0, 'trauma': 2.0,
            'terrified': 2.0, 'threatened': 2.0, 'scared': 2.0, 'fearful': 2.0,
            'cannot sleep': 2.0, 'hindi makatulog': 2.0, 'dangerous': 2.0,
            'delikado': 2.0, 'nakakatakot': 2.0, 'natatakot': 2.0,
            
            # Persistent issues (weight: 1.8)
            'always': 1.8, 'everyday': 1.8, 'constant': 1.8, 'continuous': 1.8,
            'repeatedly': 1.8, 'regular': 1.8, 'persistent': 1.8, 'daily': 1.8,
            'lagi': 1.8, 'palagi': 1.8, 'araw-araw': 1.8, 'paulit-ulit': 1.8,

            # Noise complaints (weight: 1.5)
            'loud': 1.5, 'noisy': 1.5, 'noise': 1.5, 'disturbing': 1.5,
            'ingay': 1.5, 'maingay': 1.5, 'videoke': 1.5, 'karaoke': 1.5,
            'kalakasan': 1.5, 'malakas': 1.5, 'sigaw': 1.5, 'sisigaw': 1.5,

            # Time-sensitive (weight: 1.2)
            'night': 1.2, 'midnight': 1.2, '3am': 1.2, 'madaling-araw': 1.2, 
            'gabi': 1.2, 'hatinggabi': 1.2, 'every night': 1.2, 'late': 1.2,
        }

    def get_word_categories(self):
        """Get specialized word categories for enhanced sentiment analysis"""
        return {
            'noise_words': {
                'noise', 'loud', 'noisy', 'ingay', 'maingay', 'videoke', 'karaoke',
                'sigaw', 'sisigaw', 'kalakasan', 'lakas', 'kaingayan',
                'nag-iingay', 'bumabasag', 'sumisigaw', 'nagwawala'
            },
            'time_words': {
                'night', 'midnight', 'madaling-araw', 'gabi', 'hatinggabi',
                'morning', 'umaga', '3am', '2am', '4am', 'late',
                'early', 'maaga', 'whole day', 'buong araw', 'always',
                'lagi', 'palagi', 'araw-araw', 'every day', 'every night'
            },
            'persistent_words': {
                'always', 'lagi', 'palagi', 'araw-araw', 'every day',
                'every night', 'paulit-ulit', 'over and over', 'keeps',
                'continuing', 'continuous', 'repeatedly', 'regular',
                'daily', 'weekly', 'monthly', 'constant', 'persistent',
                'habang', 'tuloy-tuloy', 'walang tigil'
            },
            'distress_words': {
                'cannot sleep', 'hindi makatulog', 'disturbed', 'stressed',
                'suffering', 'affected', 'unbearable', 'intolerable',
                'threatening', 'dangerous', 'delikado', 'nakakatakot',
                'nakakabahala', 'worried', 'concerned', 'alarming',
                'harassment', 'abuse', 'violent', 'terror', 'scared',
                'fearful', 'crying', 'scared', 'traumatized', 'hurt',
                'injured', 'wounded', 'bleeding', 'attacked'
            },
            'high_priority_words': {
                'fighting', 'away', 'gulo', 'riot', 'violence', 'assault',
                'weapon', 'knife', 'gun', 'illegal', 'drugs', 'shabu',
                'rape', 'murder', 'killing', 'death', 'threat', 'blood',
                'emergency', 'fire', 'sunog', 'flood', 'baha', 'collapse',
                'accident', 'injury', 'wounded', 'crisis', 'danger',
                'hazard', 'toxic', 'poison', 'explosive', 'bomb'
            }
        }

    def identify_common_phrases(self, text):
        """
        Identify common negative and positive phrases in text
        Returns tuple of (negative_phrases_count, positive_phrases_count)
        """
        text = text.lower()
        
        negative_phrases = [
            # English negative phrases
            'every night', 'all night', 'whole night',
            'every day', 'all day', 'whole day',
            'very loud', 'too loud', 'so loud',
            'cannot sleep', "can't sleep", 'unable to sleep',
            'always noisy', 'always drunk', 'always fighting',
            'out of control', 'no control',
            'very dangerous', 'too dangerous',
            'badly affected', 'severely affected',
            'health hazard', 'safety hazard', 'dangerous situation',
            'urgent action', 'immediate action',
            
            # Filipino negative phrases
            'hindi makatulog', 'di makatulog', 'walang tulog',
            'gabi gabi', 'araw araw', 'buong gabi',
            'sobrang ingay', 'grabe ang ingay', 'napaka ingay',
            'walang tigil', 'tuloy tuloy', 'paulit ulit',
            'laging lasing', 'laging nag-aaway',
            'sobrang baho', 'grabe ang baho', 'napaka baho',
            'delikadong lugar', 'mapanganib na lugar',
            'sobrang dumi', 'napaka dumi', 'grabe ang dumi',
            'walang disiplina', 'walang modo', 'walang respeto',
            'kailangan ng aksyon', 'kailangan ng tulong'
        ]
        
        positive_phrases = [
            # English positive phrases
            'thank you', 'thanks for', 'appreciate',
            'well done', 'good job', 'great job',
            'very helpful', 'so helpful', 'really helpful',
            'problem solved', 'issue resolved',
            'quick response', 'fast action',
            'much better', 'improved a lot',
            
            # Filipino positive phrases
            'maraming salamat', 'salamat po', 'nagpapasalamat',
            'maganda ang', 'magaling ang', 'mahusay ang',
            'naayos na', 'maayos na', 'ok na',
            'malinis na', 'tahimik na', 'payapa na',
            'mabilis na aksyon', 'mabilis na tugon',
            'mas maayos na', 'mas maganda na'
        ]

        neg_count = sum(1 for phrase in negative_phrases if phrase in text)
        pos_count = sum(1 for phrase in positive_phrases if phrase in text)
        
        return (neg_count, pos_count)

    def context_understanding(self, tokens, original_text):
        """Step 3: Context Understanding - Analyze meaningful words and calculate sentiment"""
        lexicon = self.get_sentiment_lexicon()
        word_weights = self.get_word_weights()
        categories = self.get_word_categories()

        negative_score = 0
        positive_score = 0
        neutral_score = 0

        matched_tokens = {
            'negative': [],
            'positive': [],
            'neutral': []
        }

        weighted_tokens = []
        has_noise = False
        has_time = False
        has_persistence = False
        has_distress = False
        has_high_priority = False
        
        # Process individual tokens with weights and categories
        for token in tokens:
            weight = word_weights.get(token, 1.0)  # Default weight is 1.0

            # Check specialized categories
            if token in categories['noise_words']:
                has_noise = True
                weight *= 1.5
            if token in categories['time_words']:
                has_time = True
                weight *= 1.2
            if token in categories['persistent_words']:
                has_persistence = True
                weight *= 1.8
            if token in categories['distress_words']:
                has_distress = True
                weight *= 2.0
            if token in categories['high_priority_words']:
                has_high_priority = True
                weight *= 2.5

            # Apply sentiment scoring with weights
            if token in lexicon['negative']:
                negative_score += weight
                matched_token = f"{token}(x{weight})" if weight > 1.0 else token
                matched_tokens['negative'].append(matched_token)
            elif token in lexicon['positive']:
                positive_score += weight
                matched_tokens['positive'].append(token)
            elif token in lexicon['neutral']:
                neutral_score += weight
                matched_tokens['neutral'].append(token)

        # Detect and score common negative phrases with higher impact
        neg_phrases, pos_phrases = self.identify_common_phrases(original_text)
        if neg_phrases > 0:
            negative_score = negative_score * (1 + (neg_phrases * 0.5))  # Exponential increase for multiple negative phrases
            matched_tokens['negative'].append(f"[{neg_phrases} negative phrase(s)]")
        if pos_phrases > 0:
            positive_score = positive_score * (1 + (pos_phrases * 0.3))  # Smaller boost for positive phrases
            matched_tokens['positive'].append(f"[{pos_phrases} positive phrase(s)]")

        # Apply stronger contextual multipliers for complaint severity
        if has_noise and has_time:
            negative_score *= 2.0  # Increased weight for noise at sensitive times
        if has_persistence:
            negative_score *= 2.0  # Persistent issues are more severe
        if has_distress:
            negative_score *= 2.5  # Signs of distress indicate serious issues
        if has_high_priority:
            negative_score *= 3.0  # Critical issues need immediate attention
            
        # Additional contextual boosting
        if has_noise or has_time:
            negative_score *= 1.5  # Even single factors should boost negative score
        if len(matched_tokens['negative']) > 2:
            negative_score *= 1.3  # Multiple negative aspects compound the issue

        total_score = negative_score + positive_score + neutral_score
        if total_score == 0:
            return 'neutral', {'negative': 0, 'positive': 0, 'neutral': 100}, matched_tokens

        # Calculate normalized scores
        scores = {
            'negative': round((negative_score / total_score) * 100, 2),
            'positive': round((positive_score / total_score) * 100, 2),
            'neutral': round((neutral_score / total_score) * 100, 2)
        }

        # Enhanced sentiment determination with adjusted thresholds
        # Any significant negative sentiment in complaints should be flagged
        if has_high_priority:
            matched_tokens['severity'] = "critical"
            return 'negative', scores, matched_tokens
            
        if has_distress or has_persistence:
            matched_tokens['severity'] = "high"
            return 'negative', scores, matched_tokens
            
        if scores['negative'] >= 30:  # Lowered threshold for negative sentiment
            severity = "medium" if scores['negative'] >= 50 else "low"
            matched_tokens['severity'] = severity
            return 'negative', scores, matched_tokens
            
        if scores['positive'] >= 50:  # Higher threshold for positive sentiment
            matched_tokens['severity'] = "low"
            return 'positive', scores, matched_tokens
            
        # Even with lower negative scores, if it's higher than positive, mark as negative
        if scores['negative'] > scores['positive']:
            matched_tokens['severity'] = "low"
            return 'negative', scores, matched_tokens
        elif scores['positive'] > scores['negative']:
            matched_tokens['severity'] = "low"
            return 'positive', scores, matched_tokens
            
        # Only if truly neutral (equal scores or all very low)
        return 'neutral', scores, matched_tokens

    def analyze_sentiment(self, text):
        """Analyze sentiment and return result as JSON string"""
        try:
            if not text or not isinstance(text, str):
                return json.dumps({
                    'sentiment': 'neutral',
                    'success': True,
                    'scores': {'negative': 0, 'positive': 0, 'neutral': 100},
                    'tokens': [],
                    'matched_tokens': {'negative': [], 'positive': [], 'neutral': []}
                }, ensure_ascii=False)

            self.log_debug("Analyzing text", {'text': text})

            # Step 1: Tokenization
            tokens = self.tokenize_text(text)

            # Step 2: Context Understanding (includes elimination)
            sentiment, scores, matched_tokens = self.context_understanding(tokens)

            self.log_debug("Analysis complete", {
                'sentiment': sentiment,
                'scores': scores,
                'matched_tokens': matched_tokens
            })

            # Ensure scores are properly formatted
            normalized_scores = {
                'negative': round(float(scores.get('negative', 0)), 2),
                'positive': round(float(scores.get('positive', 0)), 2),
                'neutral': round(float(scores.get('neutral', 0)), 2)
            }

            # Ensure matched_tokens is properly formatted
            normalized_matched = {
                'negative': list(matched_tokens.get('negative', [])),
                'positive': list(matched_tokens.get('positive', [])),
                'neutral': list(matched_tokens.get('neutral', []))
            }

            return json.dumps({
                'sentiment': str(sentiment).lower(),
                'success': True,
                'scores': normalized_scores,
                'tokens': list(tokens),
                'matched_tokens': normalized_matched
            }, ensure_ascii=False)

        except Exception as e:
            error_msg = f"Error: {str(e)}"
            error_trace = traceback.format_exc()
            self.log_debug("Analysis error", {
                'error': error_msg,
                'trace': error_trace,
                'text': text
            }, True)

            return json.dumps({
                'sentiment': 'neutral',
                'success': False,
                'error': error_msg,
                'trace': error_trace,
                'scores': {'negative': 0, 'positive': 0, 'neutral': 100},
                'tokens': [],
                'matched_tokens': {'negative': [], 'positive': [], 'neutral': []}
            }, ensure_ascii=False)

def main():
    try:
        if len(sys.argv) < 2:
            result = {
                'sentiment': 'neutral',
                'success': True,
                'scores': {'negative': 0, 'positive': 0, 'neutral': 100},
                'tokens': [],
                'matched_tokens': {'negative': [], 'positive': [], 'neutral': []}
            }
            print(json.dumps(result, ensure_ascii=False))
            return

        service = SentimentAnalysisService()
        text = ' '.join(sys.argv[1:])
        result = service.analyze_sentiment(text)
        print(result)

    except Exception as e:
        print(json.dumps({
            'sentiment': 'neutral',
            'success': False,
            'error': str(e)
        }))

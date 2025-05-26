#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import re
import json
import os

# Simple standalone test without importing the module
# This is a simplified version of the SentimentAnalysisService class

def tokenize_text(text):
    """Step 1: Tokenization - Split text into tokens"""
    if not text:
        return []

    # Convert to lowercase and split into words
    text = text.lower()
    # Keep Filipino and English characters, including common Filipino punctuation
    text = re.sub(r'[^\w\s]', ' ', text)
    tokens = text.split()

    return tokens

def eliminate_stop_words(tokens):
    """Step 2: Elimination - Remove stop words and irrelevant words, but keep all sentiment words"""
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
    # Always keep sentiment words, even if they are short or in stop words
    sentiment_words = set().union(
        get_sentiment_lexicon()['negative'],
        get_sentiment_lexicon()['positive'],
        get_sentiment_lexicon()['neutral']
    )
    filtered_tokens = [
        token for token in tokens
        if (token not in all_stop_words or token in sentiment_words)
    ]
    return filtered_tokens

def get_sentiment_lexicon():
    """Get sentiment lexicon for Filipino and English words"""
    return {
        # Negative words
        'negative': {
            # English negative words - original + new
            'bad', 'terrible', 'awful', 'horrible', 'worst', 'hate', 'angry',
            'frustrated', 'disappointed', 'complaint', 'problem', 'issue',
            'annoying', 'disturbing', 'loud', 'noisy', 'late', 'night',
            # Added English negative words for barangay complaints
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

            # Filipino negative words - original + new
            'pangit', 'galit', 'hindi', 'masama', 'problema', 'reklamo',
            'ingay', 'maingay', 'basura', 'dumi', 'amoy', 'nakakainis',
            'paulit', 'ulit', 'kapitbahay', 'lakas', 'videoke', 'makatulog',
            'nakakabadtrip', 'badtrip', 'stress', 'nakakastress',
            # Added Filipino negative words for barangay complaints
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

        # Positive words
        'positive': {
            # English positive words - original + new
            'good', 'great', 'excellent', 'amazing', 'wonderful', 'fantastic',
            'satisfied', 'happy', 'pleased', 'thank', 'appreciate', 'nice',
            'beautiful', 'clean', 'quiet', 'peaceful',
            # Added English positive words for barangay complaints
            'resolved', 'solution', 'improved', 'fixed', 'repaired', 'cleaned',
            'addressed', 'harmonious', 'cooperative', 'helpful', 'supportive',
            'responsive', 'action', 'proper', 'maintained', 'orderly',
            'respect', 'respectful', 'understanding', 'considerate', 'prompt',
            'effective', 'efficient', 'organized', 'fair', 'just', 'immediate',
            'coordinated', 'productive', 'improvement', 'success', 'working',
            'functioning', 'commendable', 'praiseworthy', 'accommodating',
            'attentive', 'accessible', 'reliable', 'dependable', 'swift',
            'timely', 'grateful', 'thankful', 'progress', 'innovative',

            # Filipino positive words - original + new
            'maganda', 'salamat', 'masaya', 'satisfied', 'ok', 'ayos',
            'tahimik', 'malinis', 'ganda', 'okay',
            # Added Filipino positive words for barangay complaints
            'naayos', 'inayos', 'nilinis', 'maayos', 'tulungan', 'tulong',
            'nagtulong', 'nalutas', 'sinagot', 'inaksyunan', 'kinausap',
            'pinakinggan', 'nakikinig', 'mahinahon', 'mapayapa', 'naiintindihan',
            'kaayusan', 'pagkakaisa', 'bayanihan', 'mabilis', 'agad', 'disiplina',
            'maasikaso', 'inasikaso', 'maasahan', 'nasugpo', 'naiwasan',
            'magalang', 'marunong', 'maunawain', 'matino', 'makatwiran',
            'masipag', 'masigasig', 'matulungin', 'magiliw', 'magaling',
            'mahusay', 'mabait', 'malumanay', 'makatuwiran', 'makatarungan',
            'makabuluhan', 'nagmamalasakit', 'nagpapasalamat', 'naaaksyunan',
            'natutugunan', 'naaayos', 'nasosolusyunan', 'napagtutuunan',
            'napapakinggan', 'napapansin', 'naaasikaso', 'nasisiyahan',
            'katugon', 'kaagapay', 'kasangga', 'katulong', 'matapat',
            'matagumpay', 'epektibo', 'positibo', 'napapanahon',
            'sumasagot', 'tinutulungan', 'pinapakinggan', 'nag-aalaga'
        },

        # Neutral words
        'neutral': {
            # Time/descriptive words that can be neutral - original + new
            'time', 'day', 'night', 'morning', 'afternoon', 'evening',
            'house', 'home', 'place', 'area', 'street', 'neighbor',
            # Added neutral words for barangay complaints
            'barangay', 'captain', 'chairman', 'kagawad', 'tanod', 'official',
            'resident', 'neighbor', 'community', 'sitio', 'purok', 'street',
            'road', 'alley', 'canal', 'drainage', 'water', 'electricity',
            'power', 'report', 'file', 'complaint', 'mediation', 'hearing',
            'resolution', 'ordinance', 'rule', 'regulation', 'law', 'policy',
            'permit', 'certificate', 'document', 'agreement', 'settlement',
            'boundary', 'property', 'fence', 'wall', 'gate', 'animal', 'pet',
            'meeting', 'session', 'discussion', 'schedule', 'agenda', 'minutes',
            'notification', 'notice', 'announcement', 'date', 'time', 'period',
            'duration', 'record', 'documentation', 'procedure', 'protocol',

            # Added Filipino neutral words for barangay complaints
            'kapitbahay', 'tao', 'pamilya', 'bahay', 'kalye', 'eskinita',
            'daanan', 'kanal', 'tubig', 'ilaw', 'kuryente', 'kanto',
            'tindahan', 'tanggapan', 'opisina', 'pulong', 'usapan',
            'kasunduan', 'lupain', 'lupa', 'gamit', 'oras', 'araw',
            'gabi', 'umaga', 'hapon', 'lupon', 'dokumento', 'sulat',
            'pirma', 'listahan', 'talaan', 'pangalan', 'reklamo',
            'barangay', 'kapitan', 'chairman', 'kagawad', 'tanod',
            'konsehal', 'alkalde', 'mayor', 'munisipyo', 'lungsod',
            'hearing', 'pagdinig', 'kasulatan', 'patakaran', 'ordinansa',
            'bakod', 'pader', 'gate', 'pintuan', 'bintana', 'bubong',
            'bakuran', 'hardin', 'halaman', 'puno', 'hayop', 'aso',
            'pusa', 'manok', 'alaga', 'alagang hayop', 'daan', 'daanan',
            'kalye', 'kalsada', 'bangketa', 'paradahan', 'parking',
            'tawag', 'tumawag', 'kamag-anak', 'kapitbisig', 'kapitbahayan',
            'pulong-pulong', 'pulong', 'dumalo', 'pagdaluhan', 'abiso'
        }
    }

def get_word_weights():
    """Get weights for high-impact words in complaints"""
    return {
        # Words that strongly indicate serious complaints (English)
        'fire': 2.0,
        'flood': 1.5,
        'dangerous': 1.5,
        'illegal': 1.5,
        'violence': 2.0,
        'assault': 2.0,
        'threat': 1.5,
        'harassment': 1.5,
        'drugs': 2.0,
        'stealing': 1.8,
        'theft': 1.8,
        'fighting': 1.7,
        'unsafe': 1.5,
        'unbearable': 1.3,
        'intolerable': 1.3,
        'knife': 1.8,
        'weapon': 1.9,
        'gun': 2.0,
        'accident': 1.7,
        'emergency': 1.8,
        'dying': 2.0,
        'death': 2.0,
        'collapsed': 1.8,
        'explosion': 1.9,
        'hazard': 1.6,
        'smoke': 1.4,
        'biohazard': 1.9,
        'toxic': 1.7,
        'poison': 1.8,

        # Words that strongly indicate serious complaints (Filipino)
        'sunog': 2.0,      # fire
        'baha': 1.5,       # flood
        'delikado': 1.5,   # dangerous
        'droga': 2.0,      # drugs
        'nakaw': 1.8,      # steal
        'away': 1.7,       # fight
        'gulo': 1.7,       # trouble/chaos
        'sigaw': 1.3,      # shout
        'basag': 1.3,      # break
        'sira': 1.2,       # damage
        'takot': 1.5,      # fear
        'patay': 2.0,      # dead/kill
        'sugat': 1.5,      # injury
        'sakit': 1.3,      # pain/illness
        'baril': 2.0,      # gun
        'patalim': 1.8,    # blade/knife
        'kutsilyo': 1.8,   # knife
        'aksidente': 1.7,  # accident
        'emerhensiya': 1.8,# emergency
        'namatay': 2.0,    # died
        'pagkamatay': 2.0, # death
        'gumuho': 1.8,     # collapsed
        'pagsabog': 1.9,   # explosion
        'usok': 1.4,       # smoke
        'lason': 1.8,      # poison
    }

def identify_common_phrases(text):
    """Identify common phrases in barangay complaints"""
    negative_phrases = [
        "hindi makatulog", "walang tigil", "walang respeto",
        "walang aksyon", "walang ginagawa", "laging may",
        "every night", "all night", "very loud", "too noisy",
        "maraming basura", "napakadumi", "napaka ingay",
        "hindi ko na kaya", "sobrang lakas", "hindi na matiis",
        "paulit ulit", "palaging", "araw araw", "gabi gabi",
        "walang konsiderasyon", "walang pakialam", "walang magawa",
        "hindi na makatulog", "hindi pinakikinggan", "hindi inaksyunan",
        "out of control", "hindi pinapansin", "nagkakalat ng basura",
        "ginagabi", "hanggang umaga", "buong gabi", "puyat na puyat",
        "pagod na pagod", "labag sa batas", "bawal na", "hindi legal",
        "sobrang ingay", "hindi pa rin", "walang ginagawa",
        "parati nalang", "napaka baho", "mula pa noong", "ayaw makinig",
        "ilang beses na", "maraming beses na", "napakaraming", "laging",
        "nakakabingi", "sobrang lapit", "walang matulugan", "walang pahinga",
        "walang tulong", "hindi maintindihan", "walang katapusan", "sobrang baho",
        "di makatulog", "walang disiplina", "nag-away na naman", "patuloy pa rin",
        "muli nangyari", "puro kalat", "bagsakan ng basura", "tambakan ng dumi"
    ]

    positive_phrases = [
        "agad inaksyunan", "mabilis na tugon", "maayos na solusyon",
        "immediately resolved", "quickly addressed", "thank you for",
        "salamat sa tulong", "maganda ang pagkaka", "maayos na naresolba",
        "mabilis na naayos", "hindi na naulit", "hindi na umulit",
        "properly handled", "naging maayos na", "nagbago na",
        "cooperative naman", "nakikinig naman", "nalutas agad",
        "well maintained", "nagkaroon ng pagbabago", "thank you for addressing",
        "appreciate your prompt", "salamat po sa", "malaking tulong",
        "naayos po agad", "well done", "good job", "maraming salamat po",
        "salamat sa mabilis", "naging maayos na", "thank you so much",
        "nalutas ang problema", "napakaganda ng", "naging responsive",
        "mabilis ang aksyon", "natugunan kaagad", "may pagbabago na",
        "mas maayos na ngayon", "may improvement na", "better now",
        "tuloy tuloy sana", "sana ipagpatuloy", "keep up the good",
        "nakaka appreciate", "napaka galing", "commendable action"
    ]

    # Count phrases found
    neg_count = sum(1 for phrase in negative_phrases if phrase.lower() in text.lower())
    pos_count = sum(1 for phrase in positive_phrases if phrase.lower() in text.lower())

    return neg_count, pos_count

def enhanced_context_understanding(tokens, original_text):
    """Enhanced context understanding with phrase detection and word weights"""
    lexicon = get_sentiment_lexicon()
    word_weights = get_word_weights()

    negative_score = 0
    positive_score = 0
    neutral_score = 0

    matched_tokens = {
        'negative': [],
        'positive': [],
        'neutral': []
    }

    weighted_tokens = []

    # Process individual tokens with weights
    for token in tokens:
        weight = word_weights.get(token, 1.0)  # Default weight is 1.0

        if token in lexicon['negative']:
            negative_score += weight
            matched_tokens['negative'].append(token)
            if weight > 1.0:
                weighted_tokens.append(f"{token}(x{weight})")
        elif token in lexicon['positive']:
            positive_score += weight
            matched_tokens['positive'].append(token)
        elif token in lexicon['neutral']:
            neutral_score += weight
            matched_tokens['neutral'].append(token)

    # Add phrase detection
    neg_phrases, pos_phrases = identify_common_phrases(original_text)
    negative_score += neg_phrases * 1.5  # Weight phrases more heavily
    positive_score += pos_phrases * 1.5

    # Adjust the verbose output to reflect phrase detection
    if neg_phrases > 0:
        matched_tokens['negative'].append(f"[{neg_phrases} negative phrase(s)]")
    if pos_phrases > 0:
        matched_tokens['positive'].append(f"[{pos_phrases} positive phrase(s)]")

    # If there are weighted tokens, add them to the output
    if weighted_tokens:
        matched_tokens['negative'].append("Weighted: " + ", ".join(weighted_tokens))

    total_sentiment_score = negative_score + positive_score + neutral_score

    if total_sentiment_score == 0:
        return 'neutral', {'negative': 0, 'positive': 0, 'neutral': 100}, matched_tokens

    # Calculate percentages
    negative_percentage = (negative_score / total_sentiment_score) * 100
    positive_percentage = (positive_score / total_sentiment_score) * 100
    neutral_percentage = (neutral_score / total_sentiment_score) * 100

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

    return sentiment, scores, matched_tokens

def analyze_sentiment_detailed(text):
    """Analyze sentiment and return detailed breakdown"""
    if not text:
        return {'sentiment': 'neutral', 'scores': {'negative': 0, 'positive': 0, 'neutral': 100}, 'tokens': []}

    # Step 1: Tokenization
    tokens = tokenize_text(text)

    # Step 2: Elimination
    filtered_tokens = eliminate_stop_words(tokens)

    # Step 3: Enhanced Context Understanding
    sentiment, scores, matched_tokens = enhanced_context_understanding(filtered_tokens, text)

    return {
        'sentiment': sentiment,
        'scores': scores,
        'tokens': filtered_tokens,
        'original_tokens': tokens,
        'matched_tokens': matched_tokens
    }

def test_example_complaint():
    """Test the sentiment analysis with a specific example complaint"""

    # Test text from your example
    test_text = "It is late at night pero ung mga kapitbahay ang lakas ng videoke hindi kami makatulog Nakakainis na at paulit-ulit"

    print("TESTING ENHANCED SENTIMENT ANALYSIS FOR BARANGAY COMPLAINTS")
    print("=" * 60)
    print(f"Original text: {test_text}")
    print()

    # Step 1: Tokenization
    tokens = tokenize_text(test_text)
    print("Step 1 - Tokenization:")
    print(", ".join([f'"{token}"' for token in tokens]))
    print()

    # Step 2: Elimination
    filtered_tokens = eliminate_stop_words(tokens)
    print("Step 2 - Elimination (after removing stop words):")
    print(", ".join([f'"{token}"' for token in filtered_tokens]))
    print()

    # Step 3: Enhanced Context Understanding
    sentiment, scores, matched_tokens = enhanced_context_understanding(filtered_tokens, test_text)
    print("Step 3 - Enhanced Context Understanding:")
    print(f"Negative words: {', '.join(matched_tokens['negative'])}")
    print(f"Positive words: {', '.join(matched_tokens['positive'])}")
    print(f"Neutral words: {', '.join(matched_tokens['neutral'])}")
    print()
    print(f"Negative: {scores['negative']}%")
    print(f"Neutral: {scores['neutral']}%")
    print(f"Positive: {scores['positive']}%")
    print()
    print(f"Final Sentiment: {sentiment.upper()}")
    print("=" * 60)

    # Test with other examples
    examples = [
        "The neighbors are playing loud music at night, I can't sleep.",
        "Thank you for addressing my concerns about the noise issue.",
        "The garbage collection in our area is irregular.",
        "There are stray dogs roaming in our neighborhood.",
        # Additional examples for barangay complaints
        "May sunog sa kabilang kanto kagabi, delikado na dito sa amin.",
        "Maraming beses na akong nagrereklamo tungkol sa videoke nila pero walang aksyon.",
        "Ang baho ng kanal, hindi na malinis ng maayos.",
        "Mabilis po ang aksyon ng barangay. Salamat sa tulong ninyo.",
        "Lasing na naman ang mga tambay sa kanto, nag-aaway at nagsisigawan.",
        "Hindi na dumadaan ang garbage collector dito sa amin for two weeks na.",
        "Every night hindi kami makatulog dahil sa videoke ng kapitbahay",
        "Thank you sa mabilis na aksyon ng barangay sa aking reklamo",
        "May nakita kaming mga lalaki na may baril sa kabilang kalye",
        "Sobrang baho ng kanal, maraming lamok at ipis na lumalabas"
    ]

    print("\nTESTING ADDITIONAL EXAMPLES:")
    for i, text in enumerate(examples, 1):
        result = analyze_sentiment_detailed(text)
        print(f"\nExample {i}: {text}")
        print(f"Sentiment: {result['sentiment'].upper()}")
        print(f"Scores: Negative={result['scores']['negative']}%, Positive={result['scores']['positive']}%, Neutral={result['scores']['neutral']}%")

        if 'matched_tokens' in result:
            print(f"Negative words: {', '.join(result['matched_tokens']['negative'])}")
            print(f"Positive words: {', '.join(result['matched_tokens']['positive'])}")
            print(f"Neutral words: {', '.join(result['matched_tokens']['neutral'])}")

        print(f"Key tokens: {', '.join(result['tokens'])}")
        print("-" * 60)

if __name__ == "__main__":
    test_example_complaint()

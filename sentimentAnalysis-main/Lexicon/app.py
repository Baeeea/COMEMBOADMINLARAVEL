import sys
import pandas as pd
import mysql.connector
from flask import Flask, request, jsonify
from flask_cors import CORS

# Ensure UTF-8 encoding
sys.stdout.reconfigure(encoding='utf-8')

app = Flask(__name__)
CORS(app)  # Allow cross-origin requests

# Database Configuration
DB_CONFIG = {
    "host": "localhost",
    "user": "root",
    "password": "",
    "database": "lexicon"
}

# Load datasets
dataset_files = ["twitter_training.csv", "twitter_validation.csv"]
dfs = []

for file in dataset_files:
    try:
        df = pd.read_csv(file, encoding="utf-8", header=None)
        print(f"[INFO] Loaded dataset: {file}")

        if df.shape[1] < 3:
            print(f"[ERROR] Incorrect format in {file}. Check CSV structure.")
            sys.exit(1)

        sentiment_column = df.columns[1]  # Sentiment in 2nd column
        tweet_column = df.columns[2]  # Tweet text in 3rd column

        df = df[[sentiment_column, tweet_column]]
        df.columns = ["sentiment", "comment_text"]

        dfs.append(df)

    except FileNotFoundError:
        print(f"[ERROR] File not found: {file}")
        sys.exit(1)
    except Exception as e:
        print(f"[ERROR] Failed to load {file}: {e}")
        sys.exit(1)

df = pd.concat(dfs, ignore_index=True)

# Insert dataset comments into MySQL
try:
    conn = mysql.connector.connect(**DB_CONFIG)
    cursor = conn.cursor()
    print("[INFO] Connected to MySQL.")

    # Insert into `datasets` table
    cursor.executemany("INSERT INTO datasets (comment_text, sentiment) VALUES (%s, %s)", 
                       df.to_records(index=False).tolist())

    conn.commit()
    print("[INFO] Data inserted into MySQL.")
except mysql.connector.Error as e:
    print(f"[ERROR] MySQL Error: {e}")
    sys.exit(1)
finally:
    cursor.close()
    conn.close()
    print("[INFO] Database connection closed.")


### **Flask API Routes**
@app.route("/submit_comment", methods=["POST"])
def submit_comment():
    """Store user comment in MySQL"""
    data = request.json
    comment = data.get("comment", "")

    if not comment:
        return jsonify({"error": "Comment is required"}), 400

    sentiment = "Positive" if "good" in comment.lower() else "Negative" if "bad" in comment.lower() else "Neutral"

    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor()

        cursor.execute("INSERT INTO user_comments (comment_text, sentiment) VALUES (%s, %s)", (comment, sentiment))
        conn.commit()
        return jsonify({"message": "Comment added successfully", "sentiment": sentiment}), 201
    except mysql.connector.Error as e:
        return jsonify({"error": str(e)}), 500
    finally:
        cursor.close()
        conn.close()


@app.route("/get_comments", methods=["GET"])
def get_comments():
    """Retrieve all user-submitted comments"""
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor(dictionary=True)

        cursor.execute("SELECT comment_text, sentiment, created_at FROM user_comments ORDER BY created_at DESC")
        comments = cursor.fetchall()
        return jsonify(comments), 200
    except mysql.connector.Error as e:
        return jsonify({"error": str(e)}), 500
    finally:
        cursor.close()
        conn.close()


if __name__ == "__main__":
    app.run(debug=True, port=5000)

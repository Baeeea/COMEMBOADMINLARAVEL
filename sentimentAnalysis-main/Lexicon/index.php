<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lexicon";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve past comments
$sql = "SELECT comment_text FROM user_comments ORDER BY created_at DESC"; 
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lexicon Sentiment Analysis</title>
</head>
<body>
    <h2>Submit a Comment</h2>
    <form id="commentForm">
        <textarea id="comment" name="comment" required></textarea>
        <button type="submit">Submit</button>
    </form>

    <h2>Previous Comments</h2>
    <div id="comments">
        <?php 
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<p>" . htmlspecialchars($row["comment_text"]) . "</p>";
            }
        } else {
            echo "<p>No comments yet.</p>";
        }
        ?>
    </div>

    <script>
        document.getElementById("commentForm").addEventListener("submit", function(event) {
            event.preventDefault();
            const comment = document.getElementById("comment").value;

            fetch("http://localhost:5000/submit_comment", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ comment: comment })
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                location.reload();
            })
            .catch(error => alert("Error submitting comment."));
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>

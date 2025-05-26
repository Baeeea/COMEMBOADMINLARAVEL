<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lexicon";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sort by negative sentiment first
$sql = "SELECT comment_text, sentiment, created_at FROM user_comments 
        ORDER BY 
            CASE sentiment 
                WHEN 'Negative' THEN 1 
                WHEN 'Neutral' THEN 2 
                WHEN 'Positive' THEN 3 
            END, created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comment Analysis</title>
</head>
<body>
    <h2>Sentiment Analysis Results</h2>
    <table border="1">
        <tr>
            <th>Comment</th>
            <th>Date</th>
            <th>Sentiment Analysis</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($row["comment_text"]) ?></td>
                <td><?= $row["created_at"] ?></td>
                <td><strong><?= $row["sentiment"] ?></strong></td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>

<?php $conn->close(); ?>

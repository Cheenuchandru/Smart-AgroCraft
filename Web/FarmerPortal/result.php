<?php
if (isset($_GET["error"])) {
    $error = urldecode($_GET["error"]);
} else {
    echo "No result found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prediction Result</title>
    <script>
        alert("<?php echo $error; ?>");
        window.location.href = "upload.php"; // Redirect back to upload page
    </script>
</head>
<body>
</body>
</html>

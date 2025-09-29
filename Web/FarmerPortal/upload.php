<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    $uploadDir = "uploads/";

    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $sentence = [
        "Bad Quality Brokoli", "Bad Quality Cabbage", "Bad Quality Capsicum",
        "Bad Quality Carrot", "Bad Quality Cauliflower", "Bad Quality Potato",
        "Bad Quality Tomato", "Bad Quality GreenChilli"
    ];

    $word = "Good";
    $found = true;

    $fileName = basename($_FILES["file"]["name"]);
    $targetFilePath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
        $flaskURL = "http://127.0.0.1:5000/predict";

        $postFields = ["file" => new CURLFile($targetFilePath)];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $flaskURL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);

        if (isset($data["prediction"])) {
            $prediction = $data["prediction"];

            foreach ($sentence as $item) {
                if (strpos($item, $prediction) !== false) {
                    $found = false;
                    break;
                }
            }

            if ($found) {
                header("Location: insertproduct.php?file=" . urlencode($targetFilePath));
            } else {
                header("Location: result.php?error=" . urlencode("The vegetable is of bad quality and cannot be sold."));
                exit();
            }
        } else {
            header("Location: result.php?error=" . urlencode("The vegetable is of bad quality and cannot be sold."));
            exit();
        }
    } else {
        echo "Failed to upload file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Image</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f0fff0, #e0f7fa);
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            max-width: 500px;
            padding: 20px;
        }

        h2 {
            color: #2c3e50;
            margin-bottom: 30px;
            font-size: 26px;
            text-align: center;
        }

        form {
            background: #ffffff;
            padding: 35px 45px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            width: 100%;
            box-sizing: border-box;
        }

        label {
            font-size: 17px;
            color: #333333;
            display: block;
            margin-bottom: 12px;
            font-weight: 500;
        }

        input[type="file"] {
            padding: 12px;
            border: 2px solid #ccc;
            border-radius: 6px;
            width: 100%;
            box-sizing: border-box;
            background-color: #f9f9f9;
            margin-bottom: 20px;
            font-size: 15px;
        }

        button {
            background-color: #28a745;
            color: white;
            padding: 12px 30px;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        button:hover {
            background-color: #218838;
            transform: scale(1.05);
        }

        @media (max-width: 480px) {
            form {
                padding: 25px 30px;
            }

            h2 {
                font-size: 22px;
            }

            label {
                font-size: 16px;
            }

            button {
                padding: 10px 20px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Upload an Image for Vegetable Quality Prediction</h2>
        <form action="upload.php" method="post" enctype="multipart/form-data">
            <label>Select an image:</label>
            <input type="file" name="file" required>
            <button type="submit">Upload & Predict</button>
        </form>
    </div>
</body>
</html>

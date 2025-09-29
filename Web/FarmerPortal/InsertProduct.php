<?php
include("../Includes/db.php");
session_start();
$sessphonenumber = $_SESSION['phonenumber'];

$imagePath = isset($_GET['file']) ? urldecode($_GET['file']) : '';
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert Product</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            max-width: 600px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        input, select, textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        input[type="radio"] {
            width: auto;
            margin-right: 5px;
        }
        button {
            width: 100%;
            background-color: #28a745;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 15px;
            font-size: 16px;
        }
        button:hover {
            background-color: #218838;
        }
        .image-preview {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }
        .image-preview img {
            border-radius: 5px;
            border: 1px solid #ddd;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Insert Your Product</h2>
        <form action="insertproduct.php" method="post" enctype="multipart/form-data">
            <label>Product Title:</label>
            <input type="text" name="product_title" required>

            <label>Product Stock (in kg):</label>
            <input type="text" name="product_stock" required>

            <label>Product Category:</label>
            <select name="product_cat" required>
                <option>Select a Category</option>
                <?php
                $get_cats = "SELECT * FROM categories";
                $run_cats = mysqli_query($con, $get_cats);
                while ($row_cats = mysqli_fetch_array($run_cats)) {
                    echo "<option value='{$row_cats['cat_id']}'>{$row_cats['cat_title']}</option>";
                }
                ?>
            </select>

            <label>Product Type:</label>
            <input type="text" name="product_type" required>

            <label>Product Expiry:</label>
            <input type="date" name="product_expiry" required>

            <?php if ($imagePath): ?>
                <div class="image-preview">
                    <p>Using uploaded image:</p>
                    <img src="<?php echo $imagePath; ?>" width="100">
                </div>
                <input type="hidden" name="uploaded_image" value="<?php echo $imagePath; ?>">
            <?php endif; ?>

            <label>Product Price (Per kg):</label>
            <input type="text" name="product_price" required>

            <label>Product Description:</label>
            <textarea name="product_desc" rows="3" required></textarea>

            <label>Product Keywords:</label>
            <input type="text" name="product_keywords" required>

            <label>Delivery:</label>
            <input type="radio" name="product_delivery" value="yes" required> Yes
            <input type="radio" name="product_delivery" value="no" required> No

            <button type="submit" name="insert_pro">INSERT</button>
        </form>
    </div>
</body>

</html>

<?php
if (isset($_POST['insert_pro'])) {
    $product_title = $_POST['product_title'];
    $product_cat = $_POST['product_cat'];
    $product_type = $_POST['product_type'];
    $product_stock = $_POST['product_stock'];
    $product_price = $_POST['product_price'];
    $product_expiry = $_POST['product_expiry'];
    $product_desc = $_POST['product_desc'];
    $product_keywords = $_POST['product_keywords'];
    $product_delivery = $_POST['product_delivery'];

    $product_image = ""; // Initialize empty

    if (!empty($_POST['uploaded_image'])) {
        // Image path received from upload.php (uploads/image.jpg)
        $old_image_path = $_POST['uploaded_image']; 
        
        // Extract just the filename (e.g., image.jpg)
        $image_filename = basename($old_image_path);
        
        // Define the new destination
        $new_image_path = "../Admin/product_images/" . $image_filename;

        // Move the image to the correct directory
        if (rename($old_image_path, $new_image_path)) {
            $product_image = $image_filename; // Save relative path
        } else {
            echo "<script>alert('Error moving image!');</script>";
            exit();
        }
    } else {
        echo "<script>alert('No image provided!');</script>";
        exit();
    }

    // Ensure session is active
    if (isset($_SESSION['phonenumber'])) {
        $getting_id = "SELECT * FROM farmerregistration WHERE farmer_phone = '$sessphonenumber'";
        $run = mysqli_query($con, $getting_id);
        $row = mysqli_fetch_array($run);
        $id = $row['farmer_id'];

        // Insert product into database with the updated image path
        $insert_product = "INSERT INTO products (farmer_fk, product_title, product_cat, product_type, product_expiry, product_image, product_stock, product_price, product_desc, product_keywords, product_delivery) 
        VALUES ('$id', '$product_title', '$product_cat', '$product_type', '$product_expiry', '$product_image', '$product_stock', '$product_price', '$product_desc', '$product_keywords', '$product_delivery')";

        if (mysqli_query($con, $insert_product)) {
            echo "<script>alert('Product has been added successfully!');</script>";
            echo "<script>window.location.href = 'farmerHomepage.php';</script>";
        } else {
            echo "<script>alert('Error inserting product!');</script>";
        }
    }
}

?>

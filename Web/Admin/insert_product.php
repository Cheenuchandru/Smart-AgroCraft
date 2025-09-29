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
</head>

<body>
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

        <!-- <label>Product Image:</label>
        <input type="file" name="product_image"> -->
        <?php if ($imagePath): ?>
            <p>Using uploaded image: <img src="<?php echo $imagePath; ?>" width="100"></p>
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
    // $product_image = $_POST['product_image'];
    $product_image = $_POST['uploaded_image'];
    $product_keywords = $_POST['product_keywords'];
    $product_delivery = $_POST['product_delivery'];

    // Check if using uploaded image or new one
    if (!empty($_POST['uploaded_image'])) {
        $product_image = $_POST['uploaded_image'];
        move_uploaded_file($product_image_tmp, "../Admin/product_images/$product_image");
    } else {
        $product_image = $_FILES['product_image']['name'];
        $product_image_tmp = $_FILES['product_image']['tmp_name'];
        move_uploaded_file($product_image_tmp, "../Admin/product_images/$product_image");
    }

    if (isset($_SESSION['phonenumber'])) {
        move_uploaded_file($product_image_tmp, "../Admin/product_images/$product_image");
        $getting_id = "SELECT * FROM farmerregistration WHERE farmer_phone = '$sessphonenumber'";
        $run = mysqli_query($con, $getting_id);
        $row = mysqli_fetch_array($run);
        $id = $row['farmer_id'];

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

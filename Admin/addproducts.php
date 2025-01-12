<?php
// Include your database connection file
include '../include/db_con.php';

// Fetch seller IDs from the database
$sellerQuery = "SELECT seller_id, seller_name FROM sellers";
$sellerResult = $con->query($sellerQuery);

// Fetch categories from the database
$categoryQuery = "SELECT cat_id, cat_name FROM categories";
$categoryResult = $con->query($categoryQuery);

// Process form submission for adding a product
if (isset($_POST['addProduct'])) {
    $pr_id = $_POST['prId'];
    $pr_name = $_POST['prName'];
    $pr_quantity = $_POST['quantity'];
    $pr_category = $_POST['prCategory'];
    $pr_price = $_POST['prPrice'];
    $buying_price = $_POST['buyingPrice'];
    $seller_id = $_POST['seId'];
    $pr_desc = $_POST['desc'];

    // Handle file upload
    $pr_img = '';
    if (isset($_FILES['prImg']) && $_FILES['prImg']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["prImg"]["name"]);
        if (move_uploaded_file($_FILES["prImg"]["tmp_name"], $target_file)) {
            $pr_img = $target_file;
        }
    }

    // Insert product into database
    $query = "INSERT INTO products (pr_id, pr_name, pr_quantity, pr_category, pr_price, buying_price, seller_id, pr_img, pr_desc) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ssisdisss", $pr_id, $pr_name, $pr_quantity, $pr_category, $pr_price, $buying_price, $seller_id, $pr_img, $pr_desc);

    if ($stmt->execute()) {
        $success_message = "Product added successfully!";
    } else {
        $error_message = "Error adding product: " . $con->error;
    }

    $stmt->close();
}

// Process form submission for updating a product
if (isset($_POST['updateProduct'])) {
    $pr_id = $_POST['prId'];
    $pr_name = $_POST['prName'];
    $pr_quantity = $_POST['quantity'];
    $pr_category = $_POST['prCategory'];
    $pr_price = $_POST['prPrice'];
    $buying_price = $_POST['buyingPrice'];
    $seller_id = $_POST['seId'];
    $pr_desc = $_POST['desc'];

    // Handle file upload for update
    $pr_img = '';
    if (isset($_FILES['prImg']) && $_FILES['prImg']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["prImg"]["name"]);
        if (move_uploaded_file($_FILES["prImg"]["tmp_name"], $target_file)) {
            $pr_img = $target_file;
        }
    }

    // Prepare the update query
    $query = "UPDATE products SET pr_name = ?, pr_quantity = ?, pr_category = ?, pr_price = ?, buying_price = ?, seller_id = ?, pr_desc = ?";
    $params = array($pr_name, $pr_quantity, $pr_category, $pr_price, $buying_price, $seller_id, $pr_desc);
    $types = "sidsdss";

    // If a new image was uploaded, include it in the update
    if (!empty($pr_img)) {
        $query .= ", pr_img = ?";
        $params[] = $pr_img;
        $types .= "s";
    }

    $query .= " WHERE pr_id = ?";
    $params[] = $pr_id;
    $types .= "s";

    // Prepare and execute the statement
    $stmt = $con->prepare($query);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        $success_message = "Product updated successfully!";
    } else {
        $error_message = "Error updating product: " . $con->error;
    }

    $stmt->close();
}

// Fetch product details
if (isset($_GET['prId']) || isset($_GET['prName'])) {
    $prId = isset($_GET['prId']) ? $_GET['prId'] : null;
    $prName = isset($_GET['prName']) ? $_GET['prName'] : null;

    $query = "SELECT pr_id, pr_name, pr_quantity, pr_category, pr_price, buying_price, seller_id, pr_desc 
              FROM products 
              WHERE pr_id = ? OR pr_name = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ss", $prId, $prName);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(null);
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="eng">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Products</title>
    <link rel="icon" href="img/mainlogo.png" type="image/png" />
    <link rel="stylesheet" href="css/style1.css" />
    <link rel="stylesheet" href="css/bootstrap1.min.css" />
    <link rel="stylesheet" href="css/metisMenu.css" />
</head>
<body class="crm_body_bg">

<?php include 'sidebar.php'; ?>
<section class="main_content dashboard_part large_header_bg">
    <div class="main_content_iner">
        <div class="container-fluid p-0 sm_padding_15px">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="white_card card_height_100 mb_30">
                        <div class="white_card_header">
                            <div class="box_header m-0">
                                <div class="main-title">
                                    <h3 class="m-0">Add Product</h3>
                                </div>
                            </div>
                        </div>
                        <div class="white_card_body">
                            <div class="card-body">
                                <?php
                                if (isset($success_message)) {
                                    echo "<div class='alert alert-success'>" . $success_message . "</div>";
                                }
                                if (isset($error_message)) {
                                    echo "<div class='alert alert-danger'>" . $error_message . "</div>";
                                }
                                ?>
                                <form action="" method="POST" enctype="multipart/form-data">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label" for="prId">Product Id</label>
                                            <input type="text" class="form-control" id="prId" name="prId" placeholder="Product ID" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="prName">Product Name</label>
                                            <input type="text" class="form-control" id="prName" name="prName" placeholder="Product Name" required>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label" for="seId">Seller</label>
                                            <select id="seId" name="seId" class="form-control" required>
                                                <option value="">Choose...</option>
                                                <?php
                                                if ($sellerResult->num_rows > 0) {
                                                    while ($row = $sellerResult->fetch_assoc()) {
                                                        echo "<option value='" . $row['seller_id'] . "'>" . $row['seller_name'] . "</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="quantity">Enter Quantity In KG</label>
                                            <input type="number" class="form-control" id="quantity" name="quantity" placeholder="Quantity" required>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label" for="buyingPrice">Product Buying Price</label>
                                            <input type="number" step="0.01" class="form-control" id="buyingPrice" name="buyingPrice" placeholder="Buying Price" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="prPrice">Product Selling Price</label>
                                            <input type="number" step="0.01" class="form-control" id="prPrice" name="prPrice" placeholder="Selling Price" required>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label" for="prCategory">Product Category</label>
                                            <select id="prCategory" name="prCategory" class="form-control" required>
                                                <option value="">Choose...</option>
                                                <?php
                                                if ($categoryResult->num_rows > 0) {
                                                    while ($row = $categoryResult->fetch_assoc()) {
                                                        echo "<option value='" . $row['cat_id'] . "'>" . $row['cat_name'] . "</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="prImg">Product Image</label>
                                            <input type="file" class="form-control" id="prImg" name="prImg" accept="image/*" required>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-8">
                                            <label class="form-label" for="desc">Product Description</label>
                                            <textarea class="form-control" id="desc" name="desc" required></textarea>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <button type="submit" name="addProduct" class="btn btn-success">Add Product</button>
                                            <button type="submit" name="updateProduct" class="btn btn-primary">Update Product</button>
                                            <button type="button" id="clearButton" class="btn btn-dark">Clear</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer_part">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="footer_iner text-center text-align-center">
                        <p class=fs-5>2024 © All Rights are Reserved by Green Market Place</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div id="back-top" style="display: none">
    <a title="Go to Top" href="#">
        <i class="ti-angle-up"></i>
    </a>
</div>
<script src="js/jquery1-3.4.1.min.js"></script>
<script src="js/popper1.min.js"></script>
<script src="js/bootstrap.min.html"></script>
<script src="js/metisMenu.js"></script>
<script src="js/custom.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const prIdField = document.getElementById('prId');
    const prNameField = document.getElementById('prName');

    prIdField.addEventListener('change', function () {
        fetchProductData({ prId: prIdField.value });
    });

    prNameField.addEventListener('change', function () {
        fetchProductData({ prName: prNameField.value });
    });

    function fetchProductData(params) {
        const queryString = new URLSearchParams(params).toString();

        fetch('fetch_product.php?' + queryString)
            .then(response => response.json())
            .then(product => {
                if (product) {
                    prIdField.value = product.pr_id;
                    prNameField.value = product.pr_name;
                    document.getElementById('quantity').value = product.pr_quantity;
                    document.getElementById('prCategory').value = product.pr_category;
                    document.getElementById('buyingPrice').value = product.buying_price;
                    document.getElementById('prPrice').value = product.pr_price;
                    document.getElementById('seId').value = product.seller_id;
                    document.getElementById('desc').value = product.pr_desc;
                } else {
                    alert('Product not found.');
                }
            })
            .catch(error => {
                console.error('Error fetching product data:', error);
            });
    }
});
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#clearButton').on('click', function() {
        $('#prId').val('');
        $('#prName').val('');
        $('#quantity').val('');
        $('#prCategory').val('');
        $('#prPrice').val('');
        $('#buyingPrice').val('');
        $('#seId').val('');
        $('#desc').val('');
        $('input[type="file"]').val('');
    });

    $('#prId, #prName').on('blur', function() {
        var prId = $('#prId').val();
        var prName = $('#prName').val();
        
        if (prId || prName) {
            $.ajax({
                url: 'fetch_product_details.php',
                type: 'POST',
                data: { prId: prId, prName: prName },
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data) {
                        $('#prId').val(data.pr_id);
                        $('#prName').val(data.pr_name);
                        $('#quantity').val(data.pr_quantity);
                        $('#prCategory').val(data.pr_category);
                        $('#prPrice').val(data.pr_price);
                        $('#buyingPrice').val(data.buying_price);
                        $('#seId').val(data.seller_id);
                        $('#desc').val(data.pr_desc);
                    }
                }
            });
        }
    });
});
</script>
  </body>
</html>
</body>
</html> 
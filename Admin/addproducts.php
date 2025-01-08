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
    $query = "INSERT INTO products (pr_id, pr_name, pr_quantity, pr_category, pr_price, seller_id, pr_img, pr_desc) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ssisdiss", $pr_id, $pr_name, $pr_quantity, $pr_category, $pr_price, $seller_id, $pr_img, $pr_desc);

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
    $query = "UPDATE products SET pr_name = ?, pr_quantity = ?, pr_category = ?, pr_price = ?, seller_id = ?, pr_desc = ?";
    $params = array($pr_name, $pr_quantity, $pr_category, $pr_price, $seller_id, $pr_desc);
    $types = "sidsds";

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

if (isset($_GET['prId'])) {
    $prId = $_GET['prId'];
    $query = "SELECT pr_name, pr_quantity, pr_category, pr_price, buying_price, seller_id, pr_desc FROM products WHERE pr_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $prId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        echo json_encode($product); // Return product data as JSON
    } else {
        echo json_encode(null); // No product found
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="eng">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1, shrink-to-fit=no"
    />
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
                          <input type="text" class="form-control" id="prId" name="prId" placeholder="Product ID" required onchange="loadProductData()">
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
                              while($row = $sellerResult->fetch_assoc()) {
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
                          <label class="form-label" for="prCategory">Product Category</label>
                          <select id="prCategory" name="prCategory" class="form-control" required>
                            <option value="">Choose...</option>
                            <?php
                            if ($categoryResult->num_rows > 0) {
                                while($row = $categoryResult->fetch_assoc()) {
                                    echo "<option value='" . $row['cat_id'] . "'>" . $row['cat_name'] . "</option>";
                                }
                            }
                            ?>
                          </select>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label" for="prPrice">Product Price</label>
                          <input type="number" step="0.01" class="form-control" id="prPrice" name="prPrice" placeholder="Product Price" required>
                        </div>
                      </div>

                      <div class="row mb-3">
                        <div class="col-md-8">
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
                            <p class=fs-5>2024 © All Rights are Reserved by Green Market Place 
                                   </p>
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
    function loadProductData() {
        var prId = document.getElementById('prId').value;

        if (prId) {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'fetch_product.php?prId=' + prId, true);
            xhr.onload = function() {
                if (this.status === 200) {
                    var product = JSON.parse(this.responseText);
                    if (product) {
                        document.getElementById('prName').value = product.pr_name;
                        document.getElementById('quantity').value = product.pr_quantity;
                        document.getElementById('prCategory').value = product.pr_category;
                        document.getElementById('prPrice').value = product.pr_price;
                        document.getElementById('buyingPrice').value = product.buying_price;
                        document.getElementById('seId').value = product.seller_id; // Ensure this field exists in your form
                        document.getElementById('desc').value = product.pr_desc; // Ensure this field exists in your form
                    } else {
                        alert('Product not found.');
                    }
                }
            };
            xhr.send();
        }
    }
    </script>
  </body>
</html>

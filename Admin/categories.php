<?php
session_start();
include('../include/db_con.php');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

$alert_message = '';
$alert_type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submit'])) {
        if (isset($_POST['catId']) && isset($_POST['catName'])) {
            $cat_id = $_POST['catId'];
            $cat_name = $_POST['catName'];

            // Check if category with the same ID or Name already exists
            $check_sql = "SELECT * FROM categories WHERE cat_id = ? OR cat_name = ?";
            $check_stmt = $con->prepare($check_sql);
            $check_stmt->bind_param("ss", $cat_id, $cat_name);
            $check_stmt->execute();
            $result = $check_stmt->get_result();
            
            // Initialize flags for existing categories
            $existing_id = false;
            $existing_name = false;

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    if ($row['cat_id'] === $cat_id) {
                        $existing_id = true; // Found an existing ID
                    }
                    if ($row['cat_name'] === $cat_name) {
                        $existing_name = true; // Found an existing Name
                    }
                }

                // Create alert messages based on what exists
                if ($existing_id) {
                    $alert_message .= "A category with ID '$cat_id' already exists. ";
                }
                if ($existing_name) {
                    $alert_message .= "A category with Name '$cat_name' already exists. ";
                }
                $alert_type = "error";
            } else {
                // Category doesn't exist, proceed with insertion
                $sql = "INSERT INTO categories (cat_id, cat_name) VALUES (?, ?)";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("ss", $cat_id, $cat_name);
                
                if ($stmt->execute()) {
                    $alert_message = "New category added successfully";
                    $alert_type = "success";
                } else {
                    $alert_message = "Error: " . $stmt->error;
                    $alert_type = "error";
                }

                $stmt->close();
            }

            $check_stmt->close();
        } else {
            $alert_message = "Please fill in all required fields.";
            $alert_type = "error";
        }
    } elseif (isset($_POST['remove'])) {
        if (isset($_POST['catId']) && !empty($_POST['catId'])) {
            $cat_id = $_POST['catId'];

            // Check if category exists
            $check_sql = "SELECT * FROM categories WHERE cat_id = ?";
            $check_stmt = $con->prepare($check_sql);
            $check_stmt->bind_param("s", $cat_id);
            $check_stmt->execute();
            $result = $check_stmt->get_result();

            if ($result->num_rows > 0) {
                // Category exists, proceed with removal
                $sql = "DELETE FROM categories WHERE cat_id = ?";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("s", $cat_id);
                
                if ($stmt->execute()) {
                    $alert_message = "Category removed successfully";
                    $alert_type = "success";
                } else {
                    $alert_message = "Error: " . $stmt->error;
                    $alert_type = "error";
                }

                $stmt->close();
            } else {
                $alert_message = "Error: Category with ID '$cat_id' does not exist.";
                $alert_type = "error";
            }

            $check_stmt->close();
        } else {
            $alert_message = "Please enter a Category ID to remove.";
            $alert_type = "error";
        }
    }
}

// Fetch categories from the database
$categories_sql = "SELECT * FROM categories";
$categories_result = $con->query($categories_sql);
?>

<!DOCTYPE html>
<html lang="eng">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Categories</title>
    <link rel="icon" href="img/mainlogo.png" type="image/png">
    <link rel="stylesheet" href="css/style1.css" />
    <link rel="stylesheet" href="css/bootstrap1.min.css" />
    <link rel="stylesheet" href="css/metisMenu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        .input-group-text {
            padding: 0.7rem;
        }
        table {
            border: 5px solid black;
        }
    </style>

    <script>
        function showAlert(message, type) {
            if (type === "success") {
                alert("Success: " + message);
            } else if (type === "error") {
                alert("Error: " + message);
            }
        }
    </script>
</head>

<body class="crm_body_bg">

<?php include('sidebar.php'); ?>


<section class="main_content dashboard_part large_header_bg">
        <div class="main_content_iner">
            <div class="container-fluid p-0">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="white_card card_height_100 mb_30">
                            <div class="white_card_header">
                                <div class="box_header m-0">
                                    <div class="main-title">
                                        <h3 class="m-0">Add Category</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="white_card_body">
                                <h4>Existing Categories</h4>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Category ID</th>
                                            <th>Category Name</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($category = $categories_result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($category['cat_id']); ?></td>
                                                <td><?php echo htmlspecialchars($category['cat_name']); ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                                <div class="text-end mb-3">
                                    <a href="download_categories.php" class="btn btn-primary">
                                        <i class="fas fa-download"></i> Download Categories PDF
                                    </a>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label" for="catId">Category Id</label>
                                                <input type="text" class="form-control" id="catId" name="catId" placeholder="Category Id" required />
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label" for="catName">Category Name</label>
                                                <input type="text" class="form-control" id="catName" name="catName" placeholder="Category Name" required />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <button type="submit" class="btn btn-success" name="submit">Add Category</button>
                                            </div>
                                            <div class="col-md-6 text-end">
                                                <button type="submit" class="btn btn-danger" name="remove">Remove Category</button>
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
                        <div class="footer_iner text-center">
                            <p class="fs-5">2024 © All Rights are Reserved by Green Market Place</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="js/jquery1-3.4.1.min.js"></script>
    <script src="js/popper1.min.js"></script>
    <script src="js/bootstrap.min.html"></script>
    <script src="js/metisMenu.js"></script>
    <script src="js/custom.js"></script>

    <?php
    // Display alert if there's any message
    if (!empty($alert_message)) {
        echo "<script>showAlert('$alert_message', '$alert_type');</script>";
    }
    ?>
</body>
</html>

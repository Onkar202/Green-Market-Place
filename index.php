<?php
session_start(); // Start the session
include('include/db_con.php');

// Check if the connection was successful
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Function to check if user is logged in
function isUserLoggedIn() {
    return isset($_SESSION['user_id']); // Assuming 'user_id' is set when the user logs in
}

// Check for order success message
if (isset($_SESSION['order_success'])) {
    echo '<div class="alert alert-success">' . $_SESSION['order_success'] . '</div>';
    unset($_SESSION['order_success']); // Clear the message after displaying it
}

// Get the selected category from the URL parameter
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : null;

// Get the search query from the URL parameter
$searchQuery = isset($_GET['query']) ? $_GET['query'] : '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Green Market Place</title>
    <!-- Bootstrap Css Link -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
    <!-- font awesome link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<style>
    .main-bg {
        background-color: rgb(68, 183, 68);
    }

    .logo {
        width: 5%;
        height: 5%;
        mix-blend-mode: multiply;
    }

    .card-img-top {
        width: 100%;
        height: 200px;
        object-fit: cover; /* Changed to cover for better responsiveness */
    }

    .card {
        transition: transform 0.2s; /* Add a transition effect */
    }

    #modalProductDesc {
        max-height: 300px;
        overflow-y: auto;
        padding-right: 10px;
    }

    #modalProductDesc::-webkit-scrollbar {
        width: 8px;
    }

    #modalProductDesc::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    #modalProductDesc::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }

    #modalProductDesc::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    body {
        overflow-x: hidden; /* Prevent horizontal overflow */
    }

    .container-fluid {
        padding-left: 0; /* Remove left padding */
        padding-right: 0; /* Remove right padding */
    }

    .row {
        margin-left: 0; /* Remove left margin */
        margin-right: 0; /* Remove right margin */
    }
</style>

<body>
    <div class="container-fluid p-0">
        <!-- navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">
                    <img src="images/logo/logo.png" alt="logo" class="logo" style="width: 50px; height: auto;" />
                    <span class="ms-2 fw-bold text-success">Green Market Place</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="about.php">About Us</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contact.php">Contact</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="Admin/index.php">Admin</a>
                        </li>
                    </ul>

                    <!-- Search Bar in the Center -->
                    <form class="d-flex mx-auto" style="width: 40%;" action="index.php" method="GET">
                        <input class="form-control me-2" type="search" placeholder="Search for products..." aria-label="Search" name="query" value="<?php echo htmlspecialchars($searchQuery); ?>">
                        <button class="btn btn-outline-success" type="submit">Search</button>
                    </form>

                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="cart.php">
                                <i class="fa-solid fa-cart-shopping"></i>
                                <span class="badge bg-success rounded-pill">
                                    <?php echo isset($_SESSION['total_items']) ? $_SESSION['total_items'] : 0; ?>
                                </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                Total: ₹<?php echo isset($_SESSION['total_price']) ? number_format($_SESSION['total_price'], 2) : '0.00'; ?>
                            </a>
                        </li>
                        <?php if (isUserLoggedIn()): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Welcome, <?php echo $_SESSION['user_name']; ?>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item" href="profile.php">My Profile</a></li>
                                    <li><a class="dropdown-item" href="my_orders.php">My Orders</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="login.php">Login/Register</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="bg-transparent">
            <h3 class="text-center">Green Market Place</h3>
        </div>

        <div class="row">
            <?php
                // Fetch categories
                $sql = "SELECT cat_id, cat_name FROM categories ORDER BY id";
                $result = $con->query($sql);

                if ($result === false) {
                    echo "Error: " . $con->error;
                } elseif ($result->num_rows > 0) {
                    echo "
                    <div class='col-md-2 bg-secondary p-0'>
                        <ul class='navbar-nav me-auto text-center'>
                            <li class='nav-item'>
                                <a href='index.php' class='nav-link text-light bg-success'><h4>Categories</h4></a>
                            </li>";
                
                    while($row = $result->fetch_assoc()){
                        $activeClass = ($selectedCategory == $row['cat_id']) ? 'active' : '';
                        echo "                      <li class='nav-item'>
                            <a href='index.php?category={$row['cat_id']}' class='nav-link text-light $activeClass'>{$row['cat_name']}</a>
                        </li>";
                    }
                    echo "</ul></div>";
                } else {
                    echo "<li class='nav-item'>
                        <a href='#' class='nav-link text-light'>No Category</a>
                    </li>";
                }
            ?>

            <!-- Products -->
            <div class="col-md-10">
                <div class="row">
                <?php
                    // Base SQL query
                    $product_sql = "SELECT pr_id, pr_name, pr_img, pr_desc, pr_price FROM products WHERE active = 1";

                    if ($selectedCategory) {
                        $product_sql .= " AND pr_category = ?";
                    }
                    
                    if ($searchQuery) {
                        $product_sql .= " AND pr_name LIKE ?";
                    }
                    
                    // Add ORDER BY RAND() at the end of the query
                    $product_sql .= " ORDER BY RAND()";
                    
                    $stmt = $con->prepare($product_sql);
                    if (!$stmt) {
                        die("Error preparing statement: " . $con->error);
                    }
                    
                    if ($selectedCategory && $searchQuery) {
                        $searchTerm = "%$searchQuery%";  // Create a new variable for search query
                        $stmt->bind_param("is", $selectedCategory, $searchTerm);  // Bind the parameters correctly
                    } elseif ($selectedCategory) {
                        $stmt->bind_param("i", $selectedCategory);
                    } elseif ($searchQuery) {
                        $searchTerm = "%$searchQuery%";  // Create a new variable for search query
                        $stmt->bind_param("s", $searchTerm);  // Bind the search parameter
                    }
                    
                    if (!$stmt->execute()) {
                        die("Error executing statement: " . $stmt->error);
                    }
                    
                    $product_result = $stmt->get_result();
                    

                    if ($product_result === false) {
                        echo "Error: " . $con->error;
                    } elseif ($product_result->num_rows > 0) {
                        while ($product = $product_result->fetch_assoc()) {
                            echo "
                            <div class='col-md-4 mb-2' style='text-align: center;'>
                                <div class='card p-2'>
                                    <img src='Admin/{$product['pr_img']}' class='card-img-top' alt='{$product['pr_name']}'>
                                    <div class='card-body'>
                                        <h5 class='card-title'>{$product['pr_name']}</h5>
                                        <p class='card-text'><strong>Price: ₹ {$product['pr_price']} per kg</strong></p>
                                        <div class='mb-2'>
                                            <label for='quantity_{$product['pr_id']}'>Quantity (kg):</label>
                                            <input type='number' id='quantity_{$product['pr_id']}' name='quantity' min='1' max='100' value='1' class='form-control' style='width: 80px; display: inline-block;' />
                                        </div>
                                        <button type='button' class='btn btn-success' onclick='addToCart({$product['pr_id']}, \"{$selectedCategory}\")'>Add to cart</button>
                                        <button class='btn btn-secondary' data-bs-toggle='modal' data-bs-target='#productModal' 
                                                data-id='{$product['pr_id']}' 
                                                data-name='{$product['pr_name']}' 
                                                data-img='Admin/{$product['pr_img']}' 
                                                data-desc='" . htmlspecialchars($product['pr_desc'], ENT_QUOTES) . "'>
                                            View Product
                                        </button>
                                    </div>
                                </div>
                            </div>";
                        }
                    } else {
                        echo "<p>No products available.</p>";
                    }
                    $stmt->close();
                ?>
                </div>
            </div>
        </div>

        <!-- Product Modal -->
        <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="productModalLabel"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <img id="modalProductImg" src="" class="img-fluid" alt="Product Image">
                            </div>
                            <div class="col-md-6">
                                <div id="modalProductDesc" style="max-height: 300px; overflow-y: auto;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- footer -->
        <div class="bg-transparent p-3 text-center">
            <p>All Rights Reserved © By Green Market Place</p>
        </div>
    </div>

    <!-- JavaScript function -->
    <script>
        function addToCart(productId, category) {
            const quantity = document.getElementById('quantity_' + productId).value;
            const url = isUserLoggedIn ? `add_to_cart.php?product_id=${productId}&quantity=${quantity}&category=${category}` : 'login.php';

            if (isUserLoggedIn) {
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            alert(data.error);
                        } else {
                            document.querySelector('.fa-cart-shopping + .badge').textContent = data.total_items;
                            document.querySelector('.nav-link[href="#"]').textContent = `Total: ₹${data.total_price.toFixed(2)}`;
                        }
                    })
                    .catch(error => console.error('Error:', error));
            } else {
                window.location.href = url;
            }
        }
    </script>

    <!-- bootstrap js link -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <script>
        // JavaScript to handle modal data population
        document.addEventListener('DOMContentLoaded', function () {
            var productModal = document.getElementById('productModal');
            productModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var productName = button.getAttribute('data-name');
                var productImg = button.getAttribute('data-img');
                var productDesc = button.getAttribute('data-desc');

                var modalTitle = productModal.querySelector('.modal-title');
                var modalImg = productModal.querySelector('#modalProductImg');
                var modalDesc = productModal.querySelector('#modalProductDesc');

                modalTitle.textContent = productName;
                modalImg.src = productImg;
                modalDesc.innerHTML = productDesc;
            });
        });
    </script>
    
    <script>
        // Pass PHP login status to JavaScript
        const isUserLoggedIn = <?php echo json_encode(isUserLoggedIn()); ?>;
    </script>
</body>

</html>

<?php
session_start(); // Start the session
include('include/db_con.php');

// Function to check if user is logged in
function isUserLoggedIn() {
    return isset($_SESSION['user_id']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>About Us - Green Market Place</title>
    <!-- Bootstrap Css Link -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
    <!-- font awesome link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .logo {
            width: 50px;
            height: auto;
        }
        .about-section {
            background-color: #f8f9fa;
            padding: 50px 0;
        }
        .about-image {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
        }
    </style>
</head>

<body>
    <div class="container-fluid p-0">
        <!-- navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">
                    <img src="images/logo/logo.png" alt="logo" class="logo"/>
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
                            <a class="nav-link" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="about.php">About Us</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Contact</a>
                        </li>
                    </ul>
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

        <!-- About Us Content -->
        <div class="about-section">
            <div class="container">
                <h1 class="text-center mb-5">About Green Market Place</h1>
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <img src="images/about-us.png" alt="About Green Market Place" class="about-image mb-3">
                    </div>
                    <div class="col-md-6">
                        <h2>Our Mission</h2>
                        <p>At Green Market Place, we're committed to bringing fresh, organic produce directly from local farmers to your table. Our mission is to promote sustainable agriculture, support local communities, and provide our customers with the highest quality fruits and vegetables.</p>
                        <h2>Our Story</h2>
                        <p>Founded in 2020, Green Market Place started as a small initiative to connect urban consumers with rural farmers. Today, we've grown into a thriving online marketplace, serving thousands of customers across the country.</p>
                        <h2>Our Values</h2>
                        <ul>
                            <li>Sustainability: We prioritize eco-friendly farming practices and packaging.</li>
                            <li>Quality: We ensure only the freshest produce reaches your doorstep.</li>
                            <li>Community: We support local farmers and contribute to rural development.</li>
                            <li>Transparency: We believe in clear communication about our sourcing and practices.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- footer -->
        <div class="bg-transparent p-3 text-center">
            <p>All Rights Reserved © By Green Market Place</p>
        </div>
    </div>

    <!-- bootstrap js link -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>

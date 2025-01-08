<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Products</title>
    <link rel="icon" href="img/mainlogo.png" type="image/png">
    <link rel="stylesheet" href="css/style1.css" />
    <link rel="stylesheet" href="css/bootstrap1.min.css" />
    <link rel="stylesheet" href="css/metisMenu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body class="crm_body_bg">
    <nav class="sidebar vertical-scroll ps-container ps-theme-default ps-active-y">
        <div class="logo d-flex justify-content-center m-0">
            <a href="index-2.html"><img src="img/mainlogo.png" alt="Main Logo"></a>
            <div class="sidebar_close_icon d-lg-none">
                <i class="ti-close"></i>
            </div>
        </div>
        <ul id="sidebar_menu">
            <li>
                <a href="index.php" aria-expanded="false">
                    <div class="icon_menu">
                        <img src="img/menu-icon/dashboard.svg" alt="Dashboard Icon">
                    </div>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a class="has-arrow" href="#" aria-expanded="false">
                    <div class="icon_menu">
                        <img src="img/menu-icon/8.svg" alt="Products Icon">
                    </div>
                    <span>Products</span>
                </a>
                <ul>
                    <li><a href="addproducts.php">Add Product</a></li>
                    <li><a href="viewproducts.php">View Products</a></li>
                </ul>
            </li>
            <li>
                <a href="categories.php" aria-expanded="false">
                    <div class="icon_menu">
                        <img src="img/menu-icon/3.svg" alt="Categories Icon">
                    </div>
                    <span>Categories</span>
                </a>
            </li>
            <li>
                <a class="has-arrow" href="#" aria-expanded="false">
                    <div class="icon_menu">
                        <img src="img/menu-icon/6.svg" alt="Orders Icon">
                    </div>
                    <span>Orders</span>
                </a>
                <ul>
                    <li><a href="vieworders.php">View Orders</a></li>
                </ul>
            </li>
            <li>
                <a class="has-arrow" href="#" aria-expanded="false">
                    <div class="icon_menu">
                        <img src="img/menu-icon/users.svg" alt="Users Icon">
                    </div>
                    <span>Users</span>
                </a>
                <ul>
                    <li><a href="adduser.php">Add User</a></li>
                    <li><a href="viewuser.php">View Users</a></li>
                </ul>
            </li>
            <li>
                <a class="has-arrow" href="#" aria-expanded="false">
                    <div class="icon_menu">
                        <img src="img/menu-icon/users.svg" alt="Sellers Icon">
                    </div>
                    <span>Sellers</span>
                </a>
                <ul>
                    <li><a href="addseller.php">Add Sellers</a></li>
                    <li><a href="viewsellers.php">View Sellers</a></li>
                </ul>
            </li>
            <li>
                <a class="has-arrow" href="profile.php" aria-expanded="false">
                    <div class="icon_menu">
                        <img src="img/menu-icon/profile.svg" alt="Profile Icon">
                    </div>
                    <span>Profile</span>
                </a>
            </li>
            <li>
                <a class="has-arrow" href="#" aria-expanded="false">
                    <div class="icon_menu">
                        <img src="img/menu-icon/logout.svg" alt="Logout Icon">
                    </div>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </nav>

    <section class="main_content dashboard_part large_header_bg">
        <div class="main_content_iner ">
            <div class="container-fluid p-0">
                <div class="row justify-content-center">
                    <div class="col-12">
                        <div class="QA_section">
                            <div class="white_box_tittle list_header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h4>Products List</h4>
                                    <a href="generate_products_pdf.php" class="btn btn-primary">
                                        <i class="fas fa-download"></i> Download PDF
                                    </a>
                                </div>
                            </div>
                            <div class="QA_table mb_30">
                                <table class="table lms_table_active">
                                    <thead>
                                        <tr>
                                            <th scope="col">ID</th>
                                            <th scope="col">Product Name</th>
                                            <th scope="col">Quantity</th>
                                            <th scope="col">Category</th>
                                            <th scope="col">Selling Price</th>
                                            <th scope="col">Buying Price</th>
                                            <th scope="col">Seller ID</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Include database connection
                                        include '../include/db_con.php';

                                        // Fetch all products from the database
                                        $query = "SELECT * FROM products ORDER BY pr_id DESC";
                                        $result = mysqli_query($con, $query);

                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr>";
                                            echo "<td>{$row['pr_id']}</td>";
                                            echo "<td>{$row['pr_name']}</td>";
                                            echo "<td>{$row['pr_quantity']}</td>";
                                            echo "<td>{$row['pr_category']}</td>";
                                            echo "<td>₹{$row['pr_price']}</td>";
                                            echo "<td>₹{$row['buying_price']}</td>";
                                            echo "<td>{$row['seller_id']}</td>";
                                            echo "<td>" . ($row['active'] ? 'Active' : 'Inactive') . "</td>";
                                            echo "<td>
                                                    <a href='toggle_product_status.php?id={$row['pr_id']}&status={$row['active']}' 
                                                       class='btn " . ($row['active'] ? 'btn-warning' : 'btn-success') . " btn-sm'
                                                       onclick='return confirm(\"Are you sure you want to " . ($row['active'] ? 'deactivate' : 'activate') . " this product?\")'>
                                                        " . ($row['active'] ? 'Deactivate' : 'Activate') . "
                                                    </a>
                                                  </td>";
                                            echo "</tr>";
                                        }

                                        // Close the database connection
                                        mysqli_close($con);
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="footer_part">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="footer_iner text-center text-align-center">
                        <p class="fs-5">2024 © All Rights are Reserved by Green Market Place</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="back-top" style="display: none;">
        <a title="Go to Top" href="#">
            <i class="ti-angle-up"></i>
        </a>
    </div>

    <script src="js/jquery1-3.4.1.min.js"></script>
    <script src="js/popper1.min.js"></script>
    <script src="js/bootstrap.min.html"></script>
    <script src="js/metisMenu.js"></script>
    <script src="js/custom.js"></script>
</body>

</html>

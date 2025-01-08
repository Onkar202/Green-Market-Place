<?php
session_start();
include('../include/db_con.php');

// Function to check if user is logged in as admin
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

// Check if the user is logged in and is an admin
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>View Orders</title>
    <link rel="icon" href="img/mainlogo.png" type="image/png">
    <link rel="stylesheet" href="css/style1.css" />
    <link rel="stylesheet" href="css/bootstrap1.min.css" />
    <link rel="stylesheet" href="css/metisMenu.css">
    <style>
        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal-dialog {
            max-width: 80%;
        }
        .modal.show .modal-dialog {
            transform: scale(1.02);
            transition: transform 0.3s ease-out;
        }
    </style>
</head>

<body class="crm_body_bg">
    <?php include('sidebar.php'); ?>

    <section class="main_content dashboard_part large_header_bg">
        <div class="main_content_iner ">
            <div class="container-fluid p-0">
                <div class="row justify-content-center">
                    <div class="col-12">
                        <div class="QA_section">
                            <div class="white_box_tittle list_header">
                                <h4>Orders List</h4>
                            </div>
                            <div class="QA_table mb_30">
                                <table class="table lms_table_active">
                                    <thead>
                                        <tr>
                                            <th scope="col">Order ID</th>
                                            <th scope="col">User ID - Name</th>
                                            <th scope="col">Payment Method</th>
                                            <th scope="col">Address</th>
                                            <th scope="col">Created At</th>
                                            <th scope="col">Total Quantity</th>
                                            <th scope="col">Total Price</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Fetch orders from the database in descending order of order_id
                                        $query = "SELECT o.order_id, o.user_id, o.payment_method, o.address, o.created_at, o.total_quantity, o.total_price, u.Name 
                                                  FROM orders o
                                                  JOIN users u ON o.user_id = u.id
                                                  ORDER BY o.order_id DESC";
                                        $result = mysqli_query($con, $query);

                                        if ($result) {
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                echo "<tr>";
                                                echo "<td>{$row['order_id']}</td>";
                                                echo "<td>{$row['user_id']} - {$row['Name']}</td>";
                                                echo "<td>{$row['payment_method']}</td>";
                                                echo "<td>" . substr($row['address'], 0, 30) . (strlen($row['address']) > 30 ? '...' : '') . "</td>";
                                                echo "<td>{$row['created_at']}</td>";
                                                echo "<td>{$row['total_quantity']}</td>";
                                                echo "<td>₹{$row['total_price']}</td>";
                                                echo "<td>
                                                        <button onclick='viewOrderDetails({$row['order_id']})' 
                                                                class='btn btn-primary btn-sm'>
                                                            View Details
                                                        </button>
                                                      </td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='8'>Error: " . mysqli_error($con) . "</td></tr>";
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

    <!-- Modal for Order Details -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1" role="dialog" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderDetailsModalLabel">Order Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="orderDetailsContent">
                    <!-- Order details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="js/popper1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="js/metisMenu.js"></script>
    <script src="js/custom.js"></script>
    <script>
    function viewOrderDetails(orderId) {
        console.log("Fetching details for Order ID: " + orderId); // Debugging
        $.ajax({
            url: 'get_order_details.php',
            type: 'GET',
            data: { id: orderId },
            success: function(response) {
                console.log("Response: ", response); // Debugging
                $('#orderDetailsContent').html(response);
                $('#orderDetailsModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error("Error: ", error); // Log error details
                alert('Error fetching order details');
            }
        });
    }

    $(document).ready(function() {
        // Initialize DataTable if needed
        if ($.fn.DataTable) {
            $('.lms_table_active').DataTable();
        }

        // Ensure modal is available
        if ($.fn.modal) {
            $('#orderDetailsModal').modal({
                show: false
            });
        } else {
            console.error("Bootstrap modal function not available");
        }
    });
    </script>

</body>

</html>

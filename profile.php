<?php
session_start(); // Start the session
include('include/db_con.php');

// Function to check if user is logged in
function isUserLoggedIn() {
    return isset($_SESSION['user_id']); // Assuming 'user_id' is set when the user logs in
}

// Redirect to login if not logged in
if (!isUserLoggedIn()) {
    header("Location: login.php");
    exit();
}

// Fetch user details from the database
$user_id = $_SESSION['user_id'];
$sql = "SELECT Name, user_name, email, address, Mobile, city, state, zip FROM users WHERE id = ?";
$stmt = $con->prepare($sql);

if ($stmt === false) {
    die("Error preparing statement: " . $con->error); // Display error if preparation fails
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>User Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
    <div class="container mt-5">
        <h2>User Profile</h2>
        <div class="card">
            <div class="card-body text-center">
                <h3><?php echo htmlspecialchars($user['user_name']); ?></h3>
                <p>Name: <?php echo htmlspecialchars($user['Name']); ?></p>
                <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
                <p>Address: <?php echo htmlspecialchars($user['address']); ?></p>
                <p>Mobile: <?php echo htmlspecialchars($user['Mobile']); ?></p>
                <p>City: <?php echo htmlspecialchars($user['city']); ?></p>
                <p>State: <?php echo htmlspecialchars($user['state']); ?></p>
                <p>Zip: <?php echo htmlspecialchars($user['zip']); ?></p>
                <a href="edit_profile.php" class="btn btn-primary">Edit Profile</a>
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

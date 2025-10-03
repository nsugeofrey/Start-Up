<?php
// Start the session at the very beginning of the script.
session_start();

// --- CRITICAL SECURITY CHECK ---
// If the user is not logged in, redirect them to the login page.
// This prevents direct access to the dashboard.
if (!isset($_SESSION['user_id'])) {
    header("Location:login.php");
    exit;
}

// Database connection details (replace with your actual credentials)
$host = 'localhost';
$db   = 'admin_panel';
$user = 'root';
$pass = 'root';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Fetch the user's data from the database using the session ID.
    // This is more secure than relying on session data alone.
    $stmt = $pdo->prepare("SELECT * FROM registered_users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user_data = $stmt->fetch();

    // If the user data is not found (e.g., deleted account), log out and redirect.
    if (!$user_data) {
        session_destroy();
        header("Location:login.php");
        exit;
    }

    // Now you have all the user's data in the $user_data array
    $firstname = htmlspecialchars($user_data['firstname']);
    $lastname = htmlspecialchars($user_data['lastname']);
    $email = htmlspecialchars($user_data['email']);

} catch (PDOException $e) {
    // Log the error and show a generic message to the user.
    error_log("Dashboard database error: " . $e->getMessage());
    die("An internal error occurred. Please try again later.");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>

<section class="hero">
    <div class="container">
        <div class="contact-form" data-aos="zoom-in">
            <h1 class="h3 mb-4 text-center">User Dashboard</h1>
            
            <div class="alert alert-success" role="alert">
                <h4 class="alert-heading">Welcome, <?php echo $firstname; ?>!</h4>
                <p>You have successfully logged into your dashboard.</p>
            </div>
            
            <h2 class="h5 mb-3">Your Profile Information</h2>
            <div class="card p-4 mb-4">
                <p><strong>First Name:</strong> <?php echo $firstname; ?></p>
                <p><strong>Last Name:</strong> <?php echo $lastname; ?></p>
                <p><strong>Email:</strong> <?php echo $email; ?></p>
            </div>

            <div class="d-grid gap-2">
                <a href="logout.php" class="btn btn-warning">Logout</a>
            </div>

        </div>
    </div>
</section>

</body>
</html>

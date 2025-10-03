
<?php
session_start(); // Start the session at the very beginning of the script. This is crucial for using $_SESSION variables.

include "navigation-bar.php";

// Database connection details (replace with your actual credentials)
$host = 'localhost';
$db   = 'admin_panel'; // Assuming the same database as your admin login
$user = 'root';
$pass = 'root'; // Default MAMP password, change as needed
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Ensures PDO throws exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false, // Disables emulation for better security and performance
];

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize form data
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? ''; // Password will be hashed, so no trim here


    // Basic server-side validation
    $errors = [];

    if (empty($email)) {

        $errors[] = "Email address is required.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Check if both password fields match and are not empty
    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    // If there are validation errors, store them in session and redirect back to form
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('location:login.php');
        exit;
    }

    try {
        // Establish database connection
        $pdo = new PDO($dsn, $user, $pass, $options);

        // Check if email already exists
        $stmt = $pdo->prepare("SELECT * FROM registered_users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $data = $stmt->rowCount();
        $results = $stmt->fetchALL(PDO::FETCH_ASSOC);

        if($data===0){
          $_SESSION['errors'] = ["Enter Correct Email!"];
        }
        else {
              foreach($results as $result) {

                $db_password = $result['password'];

                if(!password_verify($password,$db_password)){

                      $_SESSION['errors'] = ["Invalid Password!"];
                      header("location: login.php");
                      exit;
                }
                  else{
                      $_SESSION['success'] = ["Thanks Welcome To Your DashBoard"];
                      $_SESSION['agent-login'] = $data; // Store the agent user data in session
                      header("location:agent-dashboard.php");
                      exit;
                }
              }

            }
      
      }
      
 catch (PDOException $e) {
        // Catch PDO-specific exceptions (database connection, query errors)
       error_log("Database Error: " . $e->getMessage()); // Log the error for debugging

        $_SESSION['errors'] = ["An error occurred during login. Please try again later."];
        header('location: login.php');
        exit;

    } catch (Exception $e) {
        // Catch any other general exceptions
        error_log("General Registration Error: " . $e->getMessage()); // Log the error for debugging
        $_SESSION['errors'] = "An unexpected error occurred. Please try again.";
        header('location: login.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Find luxury properties for rent or sale in Dubai. Verified listings, agent support, and a seamless search experience.">
  <title>Dubai Real Estate | Find Luxury Homes & Apartments</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">
  <link rel="stylesheet" href="dist/css/landing-page.css">
</head>
<body>
<!-- Hero Section -->
<section class="hero">
  <div class="container">
    <div class="contact-form" data-aos="zoom-in">
      <h2 class="h3 mb-4" style="color: #ae8b4e !important;">SELL / BUY PROPERTY</h2>

      <?php
      // Display registration errors if any
      if (isset($_SESSION['errors'])):
          foreach ($_SESSION['errors'] as $error): ?>
              <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
          <?php endforeach;
          unset($_SESSION['errors']); // Clear errors after displaying
      endif;?>
     
     <?php if (isset($_SESSION['success'])): ?>
        <div style="color:green;">
          <p><?php echo (print_r($_SESSION['success'])); ?></p>
        </div>
          <?php unset($_SESSION['success']); // Clear message after displaying
      endif;
      ?>

      <form class="row g-3" action="" method="post">
        <div class="col-md-12">
          <p class="form-floating mb-3">
            <input type="email" class="form-control" id="floatingInputEmail" name="email" autocomplete="off" placeholder="email"/>
            <label for="floatingInputEmail">Email Address<span>*</span></label>
          </p>
        </div>

        <!-- Third Row: Password-->
        <div class="col-md-12">
          <p class="form-floating mb-3">
            <input type="password" class="form-control" id="floatingInputPassword" name="password" placeholder="password"/>
            <label for="floatingInputPassword">Password<span>*</span></label>
          </p>
        </div>
        
        <!-- Submit Button (Full Width) -->
        <div class="col-mb-12"> <!-- Takes full width -->
            <button type="submit" class="btn btn-warning w-100" name="form1">Login</button>
        </div>
        <div>
          <p>
            <a class="forgot-password" href="request-password-reset-copy.php">forgot Your Password?</a>
          </p>
        </div>
                <div>
          <p>
            <a class="signup" href="registration_AI.php">Create Acount</a>
          </p>
        </div>


      </form>
    </div>
  </div>
</section>

<?php include "footer.php"; ?>
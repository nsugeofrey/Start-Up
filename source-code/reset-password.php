<?php
session_start(); // Start the session at the very beginning of the script.

//the user receives an email with a url.
//verfiy if the email exits or not before opening the reset form.
//link has the token(unpredicated code), use GET to verify the link.

// Database connection details - Ensure these match your actual credentials
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

$errors = []; // Variable to hold success or error messages for the user
$success_message = [];
$token_valid = false; // Flag to control whether to show the password reset form
$reset_token = ''; // To store the token if it's valid, for POST submission

// here are checking if the url has a token set or existing.

// --- START OF SESSION MESSAGE AND ERROR HANDLING ---
// Retrieve session messages and errors at the top of the file before any output
$errors = isset($_SESSION['errors']) ? [$_SESSION['errors']] : []; // Retrieve the errors array
$success_message = isset($_SESSION['success_message']) ? [$_SESSION['success_message']] : []; // Retrieve the success message string

// Immediately clear the session variables to prevent them from showing on a page refresh
unset($_SESSION['errors']);
unset($_SESSION['success_message']);
// --- END OF SESSION MESSAGE AND ERROR HANDLING ---

$token_valid = false; // Flag to control whether to show the password reset form
$reset_token = ''; // To store the token if it's valid, for POST submission

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['token']))
{

    $reset_token = trim($_GET['token']);

  try {
            $pdo = new PDO($dsn, $user, $pass, $options);

            // 1. Find the user by token and check expiry
            $stmt = $pdo->prepare("SELECT id FROM registered_users WHERE token = ? AND token_expiry > NOW() LIMIT 1");
            $stmt->execute([$reset_token]);
            $user = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($user)
             {
                // Token is valid and not expired, allow password reset
                $token_valid = true;
            } 

            else

             {
                $_SESSION['errors'] = "Invalid or expired password reset link. Please request a new one.";
                header("location:request-password-reset-copy.php");
                exit;
            }

        } catch (PDOException $e) 
        {
            error_log("Reset Password GET Database Error: " . $e->getMessage()); 
            $_SESSION['errors'] = "An internal error occurred. Please try again later.";
            header('Location: request-password-reset-copy.php');
            exit;

        } 
}

// --- Handle POST request (when user submits new password) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_token'])) {

    $reset_token_from_post = trim($_POST['reset_token']);
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';   

    //this variable will store errors when the form is submitted.
    $post_errors = [];

    // Validation checks for the new password
    if (empty($new_password) || empty($confirm_password))
     {
        $post_errors[] = "New password and confirmation are required.";
    } 
    
    elseif ($new_password !== $confirm_password) 
    {
        $post_errors[] = "The new passwords do not match.";
    }
    
    elseif (strlen($new_password) < 8) 
    {
        $post_errors[] = "Password must be at least 8 characters long.";
    }

    if (empty($post_errors)) {

        try {
            $pdo = new PDO($dsn, $user, $pass, $options);
            $pdo->beginTransaction(); // Start a transaction for atomicity

            // Hash the new password securely
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Update the user's password and clear the token in one go
            $stmt = $pdo->prepare("UPDATE registered_users SET password = ?, token = NULL, token_expiry = NULL WHERE token = ?");
            $stmt->execute([$hashed_password, $reset_token_from_post]);
            
            // If the row was updated, commit the transaction
            if ($stmt->rowCount() > 0) {
                $pdo->commit();
                $_SESSION['success_message'] = "Your password has been successfully reset. You can now log in with your new password.";
                header('Location: login.php');
                exit;

            } 
            else {
                $pdo->rollBack();
                $_SESSION['errors'] = ["The password reset link is invalid or has expired."];
                header('Location: request-password-reset-copy.php');
                exit;
            }

            }catch (PDOException $e)
             {
            $pdo->rollBack();// Rollback if an error occurs during transaction
            $_SESSION['errors'] = ["A database error occurred. Please try again later."];
            error_log("Password reset database error: " . $e->getMessage());
            echo $e->getMessage();
            header('Location: request-password-reset-copy.php');
            exit;
        }
    }
    else {
        // If there were validation errors, store them in the session
        $_SESSION['errors'] = $post_errors;

        // The token is also needed for the redirect, so we pass it back via the URL
        header('Location: reset-password.php?token=' . urlencode($reset_token_from_post));
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>

<section class="hero">
    <div class="container">
        <div class="contact-form" data-aos="zoom-in">
            <h1 class="h3 mb-4">Reset Your Password</h1>

            <!-- Display Errors -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li>
                                <?php if(is_string($error)): ?>
                                <?php echo htmlspecialchars($error); ?></li>
                                <?php else: ?>
                                <?php echo htmlspecialchars(print_r($error, true)); ?></li>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($token_valid): ?>
                <!-- This form is only shown if the token is valid -->
                <form action="" method="post">
                    <!-- This hidden field is crucial for passing the token on form submission -->
                    <input type="hidden" name="reset_token" value="<?php echo htmlspecialchars($reset_token); ?>">

                    <p class="form-floating mb-3">
                        <input type="password" class="form-control" id="floatingInputNewPassword" name="new_password" placeholder="New Password" required/>
                        <label for="floatingInputNewPassword">New Password<span>*</span></label>
                    </p>
                    <p class="form-floating mb-3">
                        <input type="password" class="form-control" id="floatingInputConfirmPassword" name="confirm_password" placeholder="Confirm Password" required/>
                        <label for="floatingInputConfirmPassword">Confirm Password<span>*</span></label>
                    </p>
                    <button type="submit" class="btn btn-primary w-100" name="set_new_password">Set New Password</button>
                </form>

                <!-- Display Success Message -->
                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo htmlspecialchars($success_message); ?>
                    </div>
                <?php endif; ?> 


            <?php else: ?>

                <!-- This message is shown if the token is invalid or missing -->
                <p class="mt-3">
                    Your password reset link is invalid or has expired.
                    <a href="request-password-reset-copy.php">Request a new password reset link</a>
                </p>
            <?php endif; ?>

            <p class="mt-3">
                <a class="back-tO-login" href="login.php">Back to Login</a>
            </p>
        </div>
    </div>
</section>

</body>
</html>
<?php
//session_start(); // Start the session at the very beginning of the script.
include "header.php"; // Include your header file once  


// Include PHPMailer classes (assuming Composer's autoload.php handles this)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;           
use PHPMailer\PHPMailer\Exception; 

// Load Composer's autoloader for PHPMailer (ensure path is correct)
require 'vendor/autoload.php';

// Database connection details - Ensure these match your actual credentials
$host = 'localhost';
$db   = 'admin_panel';
$user = 'root';
$pass = 'root'; // Default MAMP password, change as needed
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Ensures PDO throws exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false, // Disables emulation for better security and performance
];

// Initialize message variable for display
$display_message = '';

// --- Process form submission if it's a POST request ---
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? ''); // Use null coalescing to prevent undefined index notice

    // Basic validation for email input
    if (empty($email)) {
        $_SESSION['error_message'] = "Please Enter an Email Address."; // Corrected session variable name

        header("location: request-password-reset-copy.php");
        exit;
    }
    // Verify if the submitted email is valid format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = "Invalid email format!"; // Corrected session variable name

        header("location: request-password-reset-copy.php");
        exit;
    }

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);

        // 1. Check if the email exists in your registered_users table
        // Fetch user data including first_name and last_name if found
        $statement = $pdo->prepare("SELECT id, first_name, last_name FROM registered_users WHERE email = ? LIMIT 1");
        $statement->execute([$email]);
        $user_data = $statement->fetch(PDO::FETCH_ASSOC); // Fetch the user row

        // IMPORTANT SECURITY MEASURE: Always provide a generic success message
        // This prevents email enumeration (an attacker learning which emails are registered).
        // The email sending logic is *inside* this if block, but the user always sees the same generic message.
        if ($user_data) {
            $user_id = $user_data['id'];
            $first_name = htmlspecialchars($user_data['first_name'] ?? 'User'); // Use fetched name or default
            $last_name = htmlspecialchars($user_data['last_name'] ?? '');

            // 2. Generate a secure, time-limited token
            // bin2hex(random_bytes(32)) creates a 64-character hex string
            $token = bin2hex(random_bytes(32)); // Cryptographically secure random token
            $expiry_time = date('Y-m-d H:i:s', strtotime('+5 hour')); // Token expires in 1 hour

            // 3. Store the token and its expiry in the database
            // Ensure your 'registered_users' table has 'reset_token' (VARCHAR(64)) and 'token_expiry' (DATETIME) columns.
            $update_stmt = $pdo->prepare("UPDATE registered_users SET token = ?, token_expiry = ? WHERE id = ?");
            $update_stmt->execute([$token, $expiry_time, $user_id]);

            // 4. Send the password reset email using PHPMailer
            $mail = new PHPMailer(true); // Enable exceptions

            try {
                // Build the reset link
                // !! IMPORTANT: Replace 'http://localhost:8888/Real-Estate-Management-Geofrey/admin-folder/' with your ACTUAL base URL
                $link ='http://localhost:8888/Real-Estate-Management-Geofrey/admin-folder/reset-password.php?token='.urlencode($token);
                $email_subject = 'Password Reset Request for Your Account';
                $email_body_html = "
                    <html>
                    <head>
                        <title>Password Reset</title>
                    </head>
                    <body>
                        <p style=\"text-transform: capitalize;\">Dear {$first_name} {$last_name},</p>
                        <p>You have requested a password reset for your Real Estate Account. Please click the link below to set a new password:</p>
                        <p><a href=\"{$link}\" style=\"background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;\">Reset My Password</a></p>
                        <p>This link is valid for 1 hour. If you did not request this, please ignore this email.</p>
                        <p>Thank you,<br>Real Estate Admin Team</p>
                    </body>
                    </html>
                ";

                // PHPMailer Server settings (your Mailtrap credentials)
                $mail->isSMTP();
                $mail->Host       = 'sandbox.smtp.mailtrap.io';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'b679d7c18e1809'; // Your Mailtrap Username
                $mail->Password   = 'e9d00bc2da84dd'; // Your Mailtrap Password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                // Recipients
                $mail->setFrom('noreply@yourdomain.com', 'Your App Name');
                $mail->addAddress($email, $first_name . ' ' . $last_name); // Use fetched name

                // Content
                $mail->isHTML(true);
                $mail->Subject = $email_subject;
                $mail->Body    = $email_body_html;
                $mail->AltBody = $email_body_alt;

                $mail->send();

                error_log("Password reset email sent to " . $email); // Log for debugging

            } catch (Exception $e) {
                // Log email sending errors, but show generic message to user
                error_log("Failed to send password reset email to {$email}. Mailer Error: {$mail->ErrorInfo}");
            }
        }
        // Generic message displayed to user always to prevent email enumeration
        $_SESSION['success_message'] = "If an account with that email address exists, a password reset link has been sent to it.";

    } catch (PDOException $e) {
       error_log("Forgot Password Database Error: " . $e->getMessage());  
 
        $_SESSION['error_message'] = "An internal database error occurred. Please try again later.";
        $_SESSION['error_message'];

    } catch (Exception $e) {
        error_log("Forgot Password General Error: " . $e->getMessage());
        $_SESSION['error_message'] = "An unexpected error occurred. Please try again later.";

    }
    header('location: request-password-reset-copy.php'); // Redirect after processing POST
    exit;
}

// --- HTML form display (for GET requests or after POST redirect) ---
// Fetch and display messages from session if any
if (isset($_SESSION['error_message'])) {
    $display_message = '<p style="color:red; text-align:center;">' . htmlspecialchars($_SESSION['error_message']) . '</p>';
    unset($_SESSION['error_message']); // Clear the message
} elseif (isset($_SESSION['success_message'])) {
    $display_message = '<p style="color:green; text-align:center;">' . htmlspecialchars($_SESSION['success_message']) . '</p>';
    unset($_SESSION['success_message']); // Clear the message
}

?>
<section class="hero">
    <div class="container">
      <div class="contact-form" data-aos="zoom-in">
        <h1 class="h3 mb-4 text-center" style="color: #ae8b4e !important;">Reset Password</h1>

        <?php echo $display_message; // Display messages here ?>

        <form class="row g-3" action="" method="post">
            <p class="form-floating mb-3">
              <input type="email" class="form-control" id="floatingInput" name="email" placeholder="email" required/>
              <label for="floatingInput">Email Address<span>*</span></label>
            </p>
            <button type="submit" class="btn btn-warning w-100" name="form1">Submit</button>
            
            
            <p class="mt-3">
                <a class="back-tO-login" href="login.php">Back to Login</a>
            </p>
        </form>
      </div>
    </div>
</section>
<?php
include "footer.php"; // Include your footer file
?>
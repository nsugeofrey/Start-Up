/**This code is Generated from ChatGPT, using this Promppt "Set up a registration system with these fields, First Name, 
Last Name, Phone, email, password, retype password. Add necessary PHP code that will be that checks if the all 
the fields are enter not empty, also add php code that checks if the email enter is correct format, for database
connection use PDO extension. While writing HTML code, follow the Author procedures minimize the divs and spans 
as much as you can. use plain css, no libraries no tail wind, no boot straps. "

**/

//db.php – PDO Connection Code
<?php
$host = 'localhost';
$db   = 'auth_demo';
$user = 'root';
$pass = ''; // adjust as needed
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>


//register.php – Form + Logic

<?php
require 'db.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName  = trim($_POST['first_name']);
    $lastName   = trim($_POST['last_name']);
    $phone      = trim($_POST['phone']);
    $email      = trim($_POST['email']);
    $password   = $_POST['password'];
    $retypePass = $_POST['retype_password'];

    // Check for empty fields
    if (!$firstName || !$lastName || !$phone || !$email || !$password || !$retypePass) {
        $errors[] = "All fields are required.";
    }

    // Email format validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Password match check
    if ($password !== $retypePass) {
        $errors[] = "Passwords do not match.";
    }

    // Check if email already exists
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = "Email is already registered.";
        }
    }

    // Insert into DB if valid
    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, phone, email, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$firstName, $lastName, $phone, $email, $hash]);
        $success = "Registration successful!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 500px;
            margin: 40px auto;
            padding: 20px;
        }

        h2 {
            text-align: center;
        }

        label {
            display: block;
            margin-top: 12px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-top: 4px;
            box-sizing: border-box;
        }

        button {
            margin-top: 20px;
            padding: 10px 15px;
            cursor: pointer;
        }

        .error {
            color: red;
            margin-top: 10px;
        }

        .success {
            color: green;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<h2>Register</h2>

<?php
if (!empty($errors)) {
    foreach ($errors as $e) {
        echo "<div class='error'>" . htmlspecialchars($e) . "</div>";
    }
}

if ($success) {
    echo "<div class='success'>" . htmlspecialchars($success) . "</div>";
}
?>

<form method="POST">
    <label>First Name
        <input type="text" name="first_name" value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>">
    </label>

    <label>Last Name
        <input type="text" name="last_name" value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>">
    </label>

    <label>Phone
        <input type="text" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
    </label>

    <label>Email
        <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    </label>

    <label>Password
        <input type="password" name="password">
    </label>

    <label>Retype Password
        <input type="password" name="retype_password">
    </label>

    <button type="submit">Register</button>
</form>

</body>
</html>

//Database Table (MySQL)

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);






/** elow code is updated with two things 1. Email Verification 2. Error Handling both in Development and Production

Workflow Overview (With Email Verification)

    1. User registers → data saved with is_verified = 0, a token is generated.

    2. Verification email sent with a unique link like:
    verify.php?token=XYZ

    3. User clicks the link → token is validated, is_verified = 1

    4. Only verified users can log in.

File Structure
config.php            ← environment setup (dev/prod)
db.php                ← PDO connection
register.php          ← registration form + email logic
verify.php            ← validates the verification token
email_helper.php      ← handles email sending
users table (DB)      ← includes token + verified fields

//config.php – Environment Setup

<?php
define('ENVIRONMENT', 'development'); // Change to 'production' when live

if (ENVIRONMENT === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/error.log');
    error_reporting(E_ALL);
}

//db.php – PDO Setup (Include config)

<?php
require_once 'config.php';

$host = 'localhost';
$db   = 'auth_demo';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Database connection failed.");
}

//email_helper.php – Send Verification Email
//Uses PHP's native mail() for simplicity (you can upgrade to PHPMailer later)

<?php
function send_verification_email($to, $token) {
    $subject = "Verify Your Email";
    $verification_link = "http://localhost/verify.php?token=" . urlencode($token); // adjust domain/path

    $message = "Click the link to verify your email:\n\n$verification_link";
    $headers = "From: noreply@yourapp.com\r\n";

    return mail($to, $subject, $message, $headers);
}

//register.php – Full Form + Email Token Logic
//Add token and is_verified to DB. Here’s a simplified version of the key logic:
<?php
require 'db.php';
require 'email_helper.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and trim
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $retype = $_POST['retype_password'];

    // Validate inputs
    if (!$firstName || !$lastName || !$phone || !$email || !$password || !$retype) {
        $errors[] = "All fields are required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if ($password !== $retype) {
        $errors[] = "Passwords do not match.";
    }

    // Check if email exists
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = "Email already exists.";
        }
    }

    // Register
    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(16));

        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, phone, email, password, verify_token, is_verified)
                               VALUES (?, ?, ?, ?, ?, ?, 0)");
        $stmt->execute([$firstName, $lastName, $phone, $email, $hash, $token]);

        if (send_verification_email($email, $token)) {
            $success = "Registration successful! Please check your email to verify your account.";
        } else {
            $errors[] = "Failed to send verification email.";
        }
    }
}
?>
  <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 500px;
            margin: 40px auto;
            padding: 20px;
        }

        h2 {
            text-align: center;
        }

        label {
            display: block;
            margin-top: 12px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-top: 4px;
            box-sizing: border-box;
        }

        button {
            margin-top: 20px;
            padding: 10px 15px;
            cursor: pointer;
        }

        .error {
            color: red;
            margin-top: 10px;
        }

        .success {
            color: green;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<h2>Register</h2>

<?php
if (!empty($errors)) {
    foreach ($errors as $e) {
        echo "<div class='error'>" . htmlspecialchars($e) . "</div>";
    }
}

if ($success) {
    echo "<div class='success'>" . htmlspecialchars($success) . "</div>";
}
?>

<form method="POST">
    <label>First Name
        <input type="text" name="first_name" value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>">
    </label>

    <label>Last Name
        <input type="text" name="last_name" value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>">
    </label>

    <label>Phone
        <input type="text" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
    </label>

    <label>Email
        <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    </label>

    <label>Password
        <input type="password" name="password">
    </label>

    <label>Retype Password
        <input type="password" name="retype_password">
    </label>

    <button type="submit">Register</button>
</form>

</body>
</html>


//verify.php – Activate the Account
<?php
require 'db.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $pdo->prepare("SELECT id FROM users WHERE verify_token = ? AND is_verified = 0");
    $stmt->execute([$token]);

    if ($user = $stmt->fetch()) {
        $update = $pdo->prepare("UPDATE users SET is_verified = 1, verify_token = NULL WHERE id = ?");
        $update->execute([$user['id']]);

        // Redirect to login with success message
        header("Location: login.php?verified=1");
        exit;
    } else {
        // Token invalid or already used
        header("Location: register.php?verified=0");
        exit;
    }
} else {
    // No token provided
    header("Location: register.php?verified=0");
    exit;
}




//full database table version:
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    verify_token VARCHAR(64),
    is_verified TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


//the .htaccess protocol has several advantages below code describes

# ----------------------------------------
# Enable Rewrite Engine
# ----------------------------------------
RewriteEngine On

# ----------------------------------------
# Force HTTPS
# ----------------------------------------
RewriteCond %{HTTPS} off
RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# ----------------------------------------
# Remove .php extension from URLs
# Example: /about will load /about.php
# ----------------------------------------
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME}\.php -f
RewriteRule ^(.*)$ $1.php [L]

# ----------------------------------------
# Disable Directory Listing
# ----------------------------------------
Options -Indexes

# ----------------------------------------
# Block access to sensitive files
# ----------------------------------------
<FilesMatch "^(\.env|composer\.json|composer\.lock|\.git)">
  Order allow,deny
  Deny from all
</FilesMatch>

# ----------------------------------------
# Security Headers
# ----------------------------------------
<IfModule mod_headers.c>
  Header set X-Content-Type-Options "nosniff"
  Header set X-Frame-Options "SAMEORIGIN"
  Header set X-XSS-Protection "1; mode=block"
</IfModule>

# ----------------------------------------
# Custom Error Pages
# ----------------------------------------
ErrorDocument 404 /errors/404.html
ErrorDocument 403 /errors/403.html



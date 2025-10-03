<?php 
include "navigation-bar.php";
//some supports
//Receive a user's email and a unique token from a verification link.

// Connect securely to your database.

// Check for a user with a matching email and token, ensuring their account is still unverified (status is Pending).

// If a match is found, it will update their account status to Verified and clear the token from the database.

// Finally, it will display a clear success or error message to the user.


ob_start();
session_start();
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

if(isset($_GET['email'])&&isset($_GET['token']))
{
    $email = trim($_GET['email']);
    $token = trim($_GET['token']);

    //now we securely connect to the database to get the email and token so that we can match with those in the url.
    if(empty( $email)||empty( $token)){
        $verification_message = "!Invalid Email and Token";
    }
    else {

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
        $statement = $pdo->prepare("SELECT * FROM registered_users WHERE email =? AND token = ?");
        $statement -> execute([$email, $token]);
        $results = $statement -> fetchAll(PDO::FETCH_ASSOC);

        $total =  $statement->rowCount();
        
        if($total){

            $statement = $pdo->prepare("UPDATE registered_users SET status =?, token = ? WHERE email=?");
             $statement -> execute(["1",'',$email]);
             $verification_message = "Your account has been verified Successfully - Go To Login Page";

            echo '<p style="color:green;">'. htmlspecialchars($verification_message).'<a href="admin-login.php"></a></p>';
            exit;
             }
        else{
            $verification_message = "Error: The verification link is either invalid or has already been used."; 
           echo '<p style="color:red;">'. htmlspecialchars($verification_message). '</p>';
           exit;
        }
    }


   catch (PDOException $e) {
        // Do not expose raw error message in production
        http_response_code(500);

        echo "Database connection failed. Please try again later.";
        $error_messsage = $e->getMessage(); // Log the real error securely
        error_log($error_messsage);
        //$error_messsage; //only for development to display actual error message
      } 
    }
}
?>
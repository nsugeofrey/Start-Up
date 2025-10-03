  <?php 
    include "header.php";
//Step 1: User Initiates Reset Request
//The user navigates     to a "Forgot Password?" page on your app and enters their registered email address.
// Always provide a generic success message, regardless of whether the email exists in your database 
// (e.g., "If an account with that email exists, a password reset link has been sent."). 
// This prevents attackers from knowing which emails are registered (user enumeration).

// Your PHP script receives the email address via $_POST.

// It queries the database to find the user associated with that email.

// If a user is found, generate a unique, cryptographically strong, and time-limited token. 
// This token should be a long, random string (e.g., 32-64 hexadecimal characters).

// Store this token in your users table (or a separate password_resets table)
//  alongside the user's ID and an expiry timestamp (e.g., 15-60 minutes from generation).

// Construct a unique reset link (e.g., https://your-app.com/reset_password.php?token=[generated_token]).   

// Database connection details (replace with your actual credentials)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;           
use PHPMailer\PHPMailer\Exception; 

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

if($_SERVER["REQUEST_METHOD"]=== "POST")
{
    $email = trim($_POST['email']);
    if(empty($email)){
        $_SESSION["Error-message"] = "Enter An Email Address";
        header("location: request-password-reset.php");
        exit;
    }
    //verify if the email submitted is valid
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $_SESSION["Error-message"] = "Invalid Email Format!";
        header("location: request-password-reset.php");
        exit;
    }

    //we connect to the database and check if the mail entered exists in our database.
    try {
            $pdo = new PDO($dsn, $user, $pass, $options);
            $statement = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $statement -> execute([$email]);
            $total = $statement->rowCount();
            $token = bin2hex(random_bytes(32));//more complex token using random
        if(!$total){
            $_SESSION["error-message"] = "Your Email Does Not Exist";
            header("location: request-password-reset.php");
            exit;
        }
        else {
            //if true then we we send an reset link with email and token to the entered mail.
            $link = 'http://localhost:8888/Real-Estate-Management-Geofrey/admin-folder/reset-password.php?email='.$email.'&token='.$token;
            $email_body ="<h1>You can now reset your Apple Account password.</h1><br>";
            $email_body .= "<p>Good news. You can now reset the password for your Account".$email.")</p>";
            $email_body .= '<a href="'.$link.">Click the link</a>";


            //Load Composer's autoloader (created by composer, not included with PHPMailer)
              require 'vendor/autoload.php';

            //Create an instance; passing `true` enables exceptions
            $mail = new PHPMailer(true);
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'sandbox.smtp.mailtrap.io';             //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'b679d7c18e1809';                       //SMTP username
            $mail->Password   = 'e9d00bc2da84dd';                       //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable implicit TLS encryption
            $mail->Port       = 587;                                   //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom('from@example.com', 'Mailer');
            $mail->addAddress($email, $firstname .' '. $lastname);     //Add a recipient
            //$mail->addAddress('ellen@example.comß');               //Name is optional
            $mail->addReplyTo('info@example.com', 'Information');
            //$mail->addCC('cc@example.com');
            //$mail->addBCC('bcc@example.com');

            //Attachments
            //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Registration Email';
            $mail->Body    = $email_body;
            //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();

            }

    }
    catch (PDOException $e){
         // Do not expose raw error message in production
        http_response_code(500);

        echo "Database connection failed. Please try again later.";
        $error_messsage = $e->getMessage(); // Log the real error securely
        error_log($error_messsage);
        //$error_messsage; //only for development to display actual error message

    }
}
?>
<section class="hero">
    <div class="container">
      <div class="contact-form" data-aos="zoom-in">
        <h1 class="h3 mb-4">Reset Password</h1>

    <form class="row g-3" action="" method="post">
  
        <p class="form-floating mb-3">
          <input type="email" class="form-control" id="floatingInput" name="email" placeholder="email"/>
          <label for="floatingInput">Email Address<span>*</span></label>
        </p>
      <button type="submit" class="btn btn-warning w-100" name="form1">Submit</button>
      <p>
        <a href="admin-login.php">Back To Login</a>
      </p>

    </form>
  </section>
  <?php 
    include "footer.php";
  ?>

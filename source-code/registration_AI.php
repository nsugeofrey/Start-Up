
<?php
session_start(); // Start the session at the very beginning of the script. This is crucial for using $_SESSION variables.

include "navigation-bar.php";

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
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

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize form data
    $firstname = trim($_POST['firstname'] ?? ''); // trim() removes whitespace, ?? '' provides a default empty string
    $lastname  = trim($_POST['lastname'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $password  = $_POST['password'] ?? ''; // Password will be hashed, so no trim here

    // Basic server-side validation
    $errors = [];

    if (empty($firstname)) {
        $errors[] = "First name is required.";
    }
    if (empty($lastname)) {
        $errors[] = "Last name is required.";
    }
    if (empty($email)) {
        $errors[] = "Email address is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (empty($phone)) {
        $errors[] = "Phone number is required.";
    }
    // Check if both password fields match and are not empty
    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif ($password !== ($_POST['retype_password'] ?? '')) { // Ensure the re-type password field is named 'retype_password' in your HTML
        $errors[] = "Passwords do not match.";
    } elseif (strlen($password) < 8) { // Example: Minimum password length
        $errors[] = "Password must be at least 8 characters long.";
    }

    // If there are validation errors, store them in session and redirect back to form
    if (!empty($errors)) {
        $_SESSION['registration_errors'] = $errors;
        $_SESSION['form_data'] = $_POST; // Preserve form data to repopulate fields
        header('location:registration_AI.php');
        exit;
    }

    try {
        // Establish database connection
        $pdo = new PDO($dsn, $user, $pass, $options);

        // Check if email already exists
        $stmt_check_email = $pdo->prepare("SELECT COUNT(*) FROM registered_users WHERE email = ?");
        $stmt_check_email->execute([$email]);
        if ($stmt_check_email->fetchColumn() > 0) {
            $_SESSION['registration_errors'] = ["Email address already registered."];
            $_SESSION['form_data'] = $_POST;
            header('location: registration_AI.php');
            exit;
        }

        // Hash the password before storing it
        // PASSWORD_DEFAULT is the recommended algorithm, it will be updated as new, stronger algorithms become available.
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare SQL statement for inserting new user
        // Ensure your 'users' table has columns: firstname, lastname, email, phone, password, role
        // 'role' is set to 'User' as this is a registration form for general users, not admins.
        $stmt_insert_user = $pdo->prepare(
            "INSERT INTO registered_users (first_name, last_name, email, phone, password, role, status, token) VALUES (?, ?, ?, ?, ?, ?,?, ?)"
        );
        $token = time();
        // Execute the statement with the sanitized and hashed data
        $stmt_insert_user->execute([
            $firstname,
            $lastname,
            $email,
            $phone,
            $hashed_password,
            'basic_user',// Assign a default role, e.g., 'User'
            0,
            $token
        ]);
$link = 'http://localhost:8888/Real-Estate-Management-Geofrey/admin-folder/verification.php?email='.$email.'&token='.$token;
$email_body ="Please click on this link to verify your registration:<br>";
$email_body .= '<a href="'.$link.'">';
$email_body .= "click here";
$email_body .= "</a>";

//Load Composer's autoloader (created by composer, not included with PHPMailer)
require 'vendor/autoload.php';

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
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
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
        // If registration is successful
        $_SESSION['registration_success'] = "<h1>Almost there...</h1>";
        $_SESSION['registration_success'] .="<p> We just sent you an email to confirm your address at <strong>". htmlspecialchars($email)."</strong>.Click it and you re in!
        </p>";
        header('location: registration_AI.php'); // Redirect to login page after successful registration
        exit;

    } catch (PDOException $e) {
        // Catch PDO-specific exceptions (database connection, query errors)
       error_log("Registration Database Error: " . $e->getMessage()); // Log the error for debugging

        $_SESSION['registration_errors'] = ["An error occurred during registration. Please try again later."];
        echo $e->getMessage();
        $_SESSION['form_data'] = $_POST; // Preserve form data
        //header('location: registration_AI.php');
     

        // CATCH BLOCK MODIFIED FOR DEBUGGING
        // THIS WILL DISPLAY THE RAW DATABASE ERROR.
        // REMOVE THIS FOR PRODUCTION.

        //echo "An error occurred during registration. The specific error is: <br>";
        //echo "<b>" . $e->getMessage() . "</b>";
        // You can comment out the redirect to keep the error on the page
        // header('location: registration_AI.php');
        exit;

    } catch (Exception $e) {
        // Catch any other general exceptions
        error_log("General Registration Error: " . $e->getMessage()); // Log the error for debugging
        $_SESSION['registration_errors'] = ["An unexpected error occurred. Please try again."];
        $_SESSION['form_data'] = $_POST; // Preserve form data
        header('location: registration_AI.php');
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
      <h1 class="h3 mb-4"style="color: #ae8b4e !important;">Create Acount To Sell/Buy</h1>

      <?php
      // Display registration errors if any
      if (isset($_SESSION['registration_errors'])):
          foreach ($_SESSION['registration_errors'] as $error): ?>
              <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
          <?php endforeach;
          unset($_SESSION['registration_errors']); // Clear errors after displaying
      endif;

      // Display registration success message
      if (isset($_SESSION['registration_success'])): ?>
        <div style="color:green;">
          <p><?php echo $_SESSION['registration_success']; ?></p>
        </div>
          <?php unset($_SESSION['registration_success']); // Clear message after displaying
      endif;

      // Retrieve and populate form data if available (after a redirect with errors)
      $form_data = $_SESSION['form_data'] ?? [];
      unset($_SESSION['form_data']); // Clear form data after retrieving
      ?>

      <form class="row g-3" action="" method="post">
        <!-- First Row: First Name & Last Name -->
        <div class="col-md-6">
          <p class="form-floating mb-3">
            <input type="text" class="form-control" id="floatingInputFirstName" name="firstname" autocomplete="off" placeholder="firstname" value="<?php echo htmlspecialchars($form_data['firstname'] ?? ''); ?>"/>
            <label for="floatingInputFirstName">First Name<span>*</span></label>
          </p>
        </div>
        <div class="col-md-6">
          <p class="form-floating mb-3">
            <input type="text" class="form-control" id="floatingInputLastName" name="lastname" autocomplete="off" placeholder="lastname" value="<?php echo htmlspecialchars($form_data['lastname'] ?? ''); ?>"/>
            <label for="floatingInputLastName">Last Name<span>*</span></label>
          </p>
        </div>

        <!-- Second Row: Email & Phone -->
        <div class="col-md-6">
          <p class="form-floating mb-3">
            <input type="email" class="form-control" id="floatingInputEmail" name="email" autocomplete="off" placeholder="email" value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>"/>
            <label for="floatingInputEmail">Email Address<span>*</span></label>
          </p>
        </div>
        <div class="col-md-6">
          <p class="form-floating mb-3">
            <input type="text" class="form-control" id="floatingInputPhone" name="phone" autocomplete="off" placeholder="phone" value="<?php echo htmlspecialchars($form_data['phone'] ?? ''); ?>"/>
            <label for="floatingInputPhone">Phone<span>*</span></label>
          </p>
        </div>

        <!-- Third Row: Password & Re-type Password -->
        <div class="col-md-6">
          <p class="form-floating mb-3">
            <input type="password" class="form-control" id="floatingInputPassword" name="password" placeholder="password"/>
            <label for="floatingInputPassword">Password<span>*</span></label>
          </p>
        </div>
        <div class="col-md-6">
          <p class="form-floating mb-3">
            <!-- IMPORTANT: Changed 'name' attribute from 'password' to 'retype_password' -->
            <input type="password" class="form-control" id="floatingInputRetypePassword" name="retype_password" placeholder="password"/>
            <label for="floatingInputRetypePassword">Re-type Password<span>*</span></label>
          </p>
        </div>

        <!-- Submit Button (Full Width) -->
        <div class="col-12"> <!-- Takes full width -->
            <button type="submit" class="btn btn-warning w-100" name="form1">Submit</button>
        </div>

      </form>
    </div>
  </div>
</section>

<?php include "footer.php"; ?>
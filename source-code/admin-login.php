
  <?php include "header.php";?>

  <!-- Hero Section -->
  <section class="hero">
    <div class="container">
      <div class="contact-form" data-aos="zoom-in">
        <h1 class="h3 mb-4">Admin Login Panel</h1>
      <?php
        //It's like saying: "Hey PHP, if you find a sticky note named error_message on the door (meaning isset() is true),
        //  then go ahead and read it out loud and then remove it. 
        // Otherwise, just ignore this whole section."
        if(isset($_SESSION['error_message'])):?>
          <p style = "color:red;"><?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']);?></P>
          <?php endif;?>

    <form class="row g-3" action="" method="post">
  
        <p class="form-floating mb-3">
          <input type="email" class="form-control" id="floatingInput" name="email" placeholder="email"/>
          <label for="floatingInput">Email Address<span>*</span></label>
        </p>

        <p class="form-floating">
          <input type="password" class="form-control" id="floatingInput" name="password" placeholder="password"/>
          <label for="floatingInput">Password<span>*</span></label>
        </p>

      <button type="submit" class="btn btn-warning w-100" name="form1">Login</button>

    </form>
  </section>
  <?php
        if($_SERVER['REQUEST_METHOD']==='POST'){
          $email = $_POST['email'] ?? ' ';
          $password = $_POST['password'] ?? ' ';

          if(empty($email) || empty($password)){
              $_SESSION['error_message'] = "All Fields Must Be Filled"; 
              header("location:admin-login.php");
              exit;
          }
          if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $_SESSION['error_message'] = "Invalid Email Format";
            header("location:admin-login.php");
            exit;
          } 
  
          $host = 'localhost';
          $db   = 'admin_panel';
          $user = 'root';
          $pass = 'root'; // Default MAMP password, change as needed
          $charset = 'utf8mb4';

          $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
          $options = [
              PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
              PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
              PDO::ATTR_EMULATE_PREPARES   => false,
          ];

          try {
              $pdo = new PDO($dsn, $user, $pass, $options);
              $statement = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = ?");
              $statement -> execute([$email, 'Admin']);
              $total = $statement->rowCount();

              if(!$total){
                $_SESSION['error_message'] = "Email is not found";   
              } 

              else {
                    $result = $statement->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($result as $row) {
                
                      if(!password_verify($password, $row['password'])) {
                        $_SESSION['error_message'] = "Password Does Not Match";
                        header("location: admin-login.php");
                        exit;
                      }
                      // If login is successful
                      unset($row['password']); // Unset password before storing in session for security
                    }
                    header('location: dashboard.php');
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
?>
  <?php include "footer.php"; ?> 

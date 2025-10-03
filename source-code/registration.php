
  <?php 
  include "navigation-bar.php";
  
  ?>
  <!-- Hero Section -->
  <section class="hero">
    <div class="container">
      <div class="contact-form" data-aos="zoom-in">
        <h1 class="h3 mb-4">REGISTRATION FORM</h1>

    <form class="row g-3" action="" method="post">
        <p class="form-floating mb-3">
          <input type="text" class="form-control" id="floatingInput" name="firstname" autocomplete="off" placeholder="firstname"/>
          <label for="floatingInput">First Name<span>*</span></label>
        </p>

        <p class="form-floating mb-3">
          <input type="text" class="form-control" id="floatingInput" name="lastname" autocomplete="off" placeholder="lastname"/>
          <label for="floatingInput">Last Name<span>*</span></label>
        </p>
        <p class="form-floating mb-3">
          <input type="email" class="form-control" id="floatingInput" name="email" autocomplete="off" placeholder="email"/>
          <label for="floatingInput">Email Address<span>*</span></label>
        </p>
        <p class="form-floating mb-3">
          <input type="text" class="form-control" id="floatingInput" name="phone" autocomplete="off" placeholder="phone"/>
          <label for="floatingInput">Phone<span>*</span></label>
        </p>

        <p class="form-floating">
          <input type="password" class="form-control" id="floatingInput" name="password" placeholder="password"/>
          <label for="floatingInput">Password<span>*</span></label>
        </p>
        <p class="form-floating">
          <input type="password" class="form-control" id="floatingInput" name="retype_password" placeholder="password"/>
          <label for="floatingInput">Re-type Password<span>*</span></label>
        </p>

      <button type="submit" class="btn btn-warning w-100" name="form1">Submit</button>

    </form>
  </section>
  
  <?php include "footer.php"; ?> 


<?php

?>

<form action="" method="post">
    <!-- This hidden field is crucial for passing the token on form submission -->
    <input type="hidden" name="reset_token" value="<?php echo htmlspecialchars($token); ?>">

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
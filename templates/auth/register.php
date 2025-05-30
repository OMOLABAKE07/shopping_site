<?php
// If user is already logged in, redirect to home
if (Session::isLoggedIn()) {
    header('Location: ' . BASE_URL . '/');
    exit;
}
?>

<main>
    <section class="featured-places">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-md-offset-3">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="text-center mb-4">Register</h2>
                            
                            <?php if ($flash = Session::getFlash()): ?>
                                <div class="alert alert-<?php echo $flash['type']; ?>">
                                    <?php echo $flash['message']; ?>
                                </div>
                            <?php endif; ?>
                            
                            <form id="register-form" class="auth-form">
                                <input type="hidden" name="action" value="register">
                                <input type="hidden" name="csrf_token" value="<?php echo Form::generateCSRFToken(); ?>">
                                
                                <div class="form-group mb-3">
                                    <label for="username">Username</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="username" 
                                           name="username" 
                                           required 
                                           minlength="3">
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="email">Email</label>
                                    <input type="email" 
                                           class="form-control" 
                                           id="email" 
                                           name="email" 
                                           required>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="password">Password</label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password" 
                                           name="password" 
                                           required 
                                           minlength="6">
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="confirm_password">Confirm Password</label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="confirm_password" 
                                           name="confirm_password" 
                                           required 
                                           minlength="6">
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100">Register</button>
                            </form>
                            
                            <div class="text-center mt-3">
                                <p>Already have an account? <a href="<?php echo BASE_URL; ?>/auth.php?action=login">Login here</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
jQuery(document).ready(function($) {
    'use strict';
    
    $('#register-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $submitBtn = $form.find('button[type="submit"]');
        var originalBtnText = $submitBtn.text();
        
        // Validate passwords match
        var password = $('#password').val();
        var confirmPassword = $('#confirm_password').val();
        
        if (password !== confirmPassword) {
            alert('Passwords do not match');
            return;
        }
        
        // Disable submit button and show loading state
        $submitBtn.prop('disabled', true).text('Processing...');
        
        $.ajax({
            url: BASE_URL + '/auth/handler.php',
            method: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    }
                } else {
                    alert(response.message || 'An error occurred. Please try again.');
                    $submitBtn.prop('disabled', false).text(originalBtnText);
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
                $submitBtn.prop('disabled', false).text(originalBtnText);
            }
        });
    });
});
</script> 
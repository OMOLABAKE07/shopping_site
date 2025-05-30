<?php
require_once 'config/paths.php';
require_once 'classes/Session.php';
require_once 'classes/Form.php';
require_once 'classes/User.php';

Session::start();

// Redirect if already logged in
if (Session::isLoggedIn()) {
    header('Location: ' . BASE_URL);
    exit;
}

$errors = [];
$userModel = new User();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!Form::validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors['form'] = 'Invalid form submission';
    } else {
        // Ensure $_POST is an array before validation
        $postData = is_array($_POST) ? $_POST : [];
        
        // Validate form data
        $validationRules = [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ];
        
        $errors = Form::validate($postData, $validationRules);
        
        if (empty($errors)) {
            // Sanitize the entire post data array
            $sanitizedData = Form::sanitize($postData);
            $email = $sanitizedData['email'];
            $password = $postData['password']; // Don't sanitize password
            
            $user = $userModel->login($email, $password);
            
            if ($user) {
                Session::set('user_id', $user['id']);
                
                // Redirect to the intended page or home
                $redirect = Session::get('redirect_after_login', BASE_URL);
                Session::remove('redirect_after_login');
                
                header("Location: {$redirect}");
                exit;
            } else {
                $errors['form'] = 'Invalid email or password';
            }
        }
    }
    
    // Store old input for form repopulation
    Form::setOld($postData);
}

$csrfToken = Form::generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/bootstrap.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <?php require_once INCLUDES_PATH . '/header.php'; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Login</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($errors['form'])): ?>
                            <div class="alert alert-danger">
                                <?php echo $errors['form']; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" 
                                       class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                                       id="email" 
                                       name="email" 
                                       value="<?php echo Form::old('email'); ?>"
                                       required>
                                <?php if (isset($errors['email'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['email']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" 
                                       class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                                       id="password" 
                                       name="password" 
                                       required>
                                <?php if (isset($errors['password'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['password']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <a href="<?php echo BASE_URL; ?>/forgot-password.php">Forgot Password?</a>
                            </div>

                            <button type="submit" class="btn btn-primary">Login</button>
                        </form>

                        <div class="mt-3">
                            <p>Don't have an account? <a href="<?php echo BASE_URL; ?>/register.php">Register here</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once INCLUDES_PATH . '/footer.php'; ?>
    <script src="<?php echo ASSETS_URL; ?>/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
// Clear old input after page load
Form::clearOld();
?> 
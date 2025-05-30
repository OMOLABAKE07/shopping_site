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
        // Validate form data
        $validationRules = [
            'username' => 'required|min:3|max:50|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|match:password',
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'phone' => 'required|max:20'
        ];
        
        $errors = Form::validate($_POST, $validationRules);
        
        if (empty($errors)) {
            $userData = Form::sanitize([
                'username' => $_POST['username'],
                'email' => $_POST['email'],
                'password' => $_POST['password'],
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name'],
                'phone' => $_POST['phone'],
                'address' => $_POST['address'] ?? '',
                'role' => 'user'
            ]);
            
            $userId = $userModel->register($userData);
            
            if ($userId) {
                Session::setFlash('success', 'Registration successful! Please login.');
                header('Location: ' . BASE_URL . '/login.php');
                exit;
            } else {
                $errors['form'] = 'Registration failed. Please try again.';
            }
        }
    }
    
    // Store old input for form repopulation
    Form::setOld($_POST);
}

$csrfToken = Form::generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/bootstrap.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <?php require_once INCLUDES_PATH . '/header.php'; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Register</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($errors['form'])): ?>
                            <div class="alert alert-danger">
                                <?php echo $errors['form']; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" 
                                           class="form-control <?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>" 
                                           id="username" 
                                           name="username" 
                                           value="<?php echo Form::old('username'); ?>"
                                           required>
                                    <?php if (isset($errors['username'])): ?>
                                        <div class="invalid-feedback">
                                            <?php echo $errors['username']; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="col-md-6 mb-3">
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
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
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

                                <div class="col-md-6 mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                    <input type="password" 
                                           class="form-control <?php echo isset($errors['password_confirmation']) ? 'is-invalid' : ''; ?>" 
                                           id="password_confirmation" 
                                           name="password_confirmation" 
                                           required>
                                    <?php if (isset($errors['password_confirmation'])): ?>
                                        <div class="invalid-feedback">
                                            <?php echo $errors['password_confirmation']; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input type="text" 
                                           class="form-control <?php echo isset($errors['first_name']) ? 'is-invalid' : ''; ?>" 
                                           id="first_name" 
                                           name="first_name" 
                                           value="<?php echo Form::old('first_name'); ?>"
                                           required>
                                    <?php if (isset($errors['first_name'])): ?>
                                        <div class="invalid-feedback">
                                            <?php echo $errors['first_name']; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input type="text" 
                                           class="form-control <?php echo isset($errors['last_name']) ? 'is-invalid' : ''; ?>" 
                                           id="last_name" 
                                           name="last_name" 
                                           value="<?php echo Form::old('last_name'); ?>"
                                           required>
                                    <?php if (isset($errors['last_name'])): ?>
                                        <div class="invalid-feedback">
                                            <?php echo $errors['last_name']; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" 
                                       class="form-control <?php echo isset($errors['phone']) ? 'is-invalid' : ''; ?>" 
                                       id="phone" 
                                       name="phone" 
                                       value="<?php echo Form::old('phone'); ?>"
                                       required>
                                <?php if (isset($errors['phone'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $errors['phone']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" 
                                          id="address" 
                                          name="address" 
                                          rows="3"><?php echo Form::old('address'); ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">Register</button>
                        </form>

                        <div class="mt-3">
                            <p>Already have an account? <a href="/login.php">Login here</a></p>
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
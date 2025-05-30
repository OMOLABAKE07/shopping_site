<?php
// Load configuration and autoloader first
require_once __DIR__ . '/../config/paths.php';

// Then start session and include header
Session::start();
require_once __DIR__ . '/../includes/header.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title><?php echo SITE_NAME; ?> - Checkout</title>
        
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSS Files -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/fontAwesome.css">
        <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/hero-slider.css">
        <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/owl-carousel.css">
        <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/style.css">

        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,200,300,400,500,600,700,800,900" rel="stylesheet">

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="<?php echo ASSETS_URL; ?>/img/logo.png">
        <link rel="shortcut icon" type="image/png" href="<?php echo ASSETS_URL; ?>/img/logo.png">

        <!-- Modernizr -->
        <script src="<?php echo ASSETS_URL; ?>/js/vendor/modernizr-2.8.3-respond-1.4.2.min.js"></script>

        <!-- Replace the local Font Awesome CSS with CDN version -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    </head>

<body>
 
    <div class="wrap">
        <header id="header">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <button id="primary-nav-button" type="button">Menu</button>
                        <a href="<?php echo BASE_URL; ?>/">
                            <div class="logo">
                                <img src="<?php echo ASSETS_URL; ?>/img/logo.png" alt="<?php echo SITE_NAME; ?> Logo">
                            </div>
                        </a>
                        <nav id="primary-nav" class="dropdown cf">
                            <ul class="dropdown menu">
                                <li><a href="<?php echo url(); ?>">Home</a></li>
                                <li><a href="<?php echo url('pages/products.php'); ?>">Products</a></li>
                                <li class='active'><a href="<?php echo url('pages/checkout.php'); ?>">Checkout</a></li>
                                <li>
                                    <a href="#">About</a>
                                    <ul class="sub-menu">
                                        <li><a href="<?php echo url('pages/about-us.php'); ?>">About Us</a></li>
                                        <li><a href="<?php echo url('pages/blog.php'); ?>">Blog</a></li>
                                        <li><a href="<?php echo url('pages/testimonials.php'); ?>">Testimonials</a></li>
                                        <li><a href="<?php echo url('pages/terms.php'); ?>">Terms</a></li>
                                    </ul>
                                </li>
                                <li><a href="<?php echo url('pages/contact.php'); ?>">Contact Us</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </header>
    </div>
      
    <section class="banner banner-secondary" id="top" style="background-image: url('<?php echo ASSETS_URL; ?>/img/banner-image-1-1920x300.jpg');">
        <div class="container">
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <div class="banner-caption">
                        <div class="line-dec"></div>
                        <h2>Checkout</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <main>
        <section class="featured-places">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-md-5 pull-right">
                        <ul class="list-group">
                          <li class="list-group-item">
                            <div class="row">
                                  <div class="col-xs-6">
                                       <em>Sub-total</em>
                                  </div>
                                  
                                  <div class="col-xs-6 text-right">
                                       <strong>$ 128.00</strong>
                                  </div>
                             </div>
                          </li>
                          
                          <li class="list-group-item">
                               <div class="row">
                                    <div class="col-xs-6">
                                         <em>Extra</em>
                                    </div>

                                    <div class="col-xs-6 text-right">
                                         <strong>$ 0.00</strong>
                                    </div>
                               </div>
                          </li>

                          <li class="list-group-item">
                               <div class="row">
                                    <div class="col-xs-6">
                                         <em>Tax</em>
                                    </div>

                                    <div class="col-xs-6 text-right">
                                         <strong>$ 10.00</strong>
                                    </div>
                               </div>
                          </li>

                          <li class="list-group-item">
                               <div class="row">
                                    <div class="col-xs-6">
                                         <em>Total</em>
                                    </div>

                                    <div class="col-xs-6 text-right">
                                         <strong>$ 138.00</strong>
                                    </div>
                               </div>
                          </li>

                          <li class="list-group-item">
                               <div class="row">
                                    <div class="col-xs-6">
                                         <em>Deposit</em>
                                    </div>

                                    <div class="col-xs-6 text-right">
                                         <strong>$ 20.00</strong>
                                    </div>
                               </div>
                          </li>
                        </ul>
                    </div>

                    <div class="col-lg-8 col-md-7">
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                           <div class="row">
                                <div class="col-sm-6 col-xs-12">
                                     <div class="form-group">
                                          <label class="control-label">Title:</label>
                                          <select name="title" class="form-control" data-msg-required="This field is required.">
                                               <option value="">-- Choose --</option>
                                               <option value="dr">Dr.</option>
                                               <option value="miss">Miss</option>
                                               <option value="mr">Mr.</option>
                                               <option value="mrs">Mrs.</option>
                                               <option value="ms">Ms.</option>
                                               <option value="other">Other</option>
                                               <option value="prof">Prof.</option>
                                               <option value="rev">Rev.</option>
                                          </select>
                                     </div>
                                </div>
                                <div class="col-sm-6 col-xs-12">
                                     <div class="form-group">
                                          <label class="control-label">Name:</label>
                                          <input type="text" name="name" class="form-control">
                                     </div>
                                </div>
                           </div>
                           <div class="row">
                                <div class="col-sm-6 col-xs-12">
                                     <div class="form-group">
                                          <label class="control-label">Email:</label>
                                          <input type="email" name="email" class="form-control">
                                     </div>
                                </div>
                                <div class="col-sm-6 col-xs-12">
                                     <div class="form-group">
                                          <label class="control-label">Phone:</label>
                                          <input type="text" name="phone" class="form-control">
                                     </div>
                                </div>
                           </div>
                           <div class="row">
                                <div class="col-sm-6 col-xs-12">
                                     <div class="form-group">
                                          <label class="control-label">Address 1:</label>
                                          <input type="text" name="address1" class="form-control">
                                     </div>
                                </div>
                                <div class="col-sm-6 col-xs-12">
                                     <div class="form-group">
                                          <label class="control-label">Address 2:</label>
                                          <input type="text" name="address2" class="form-control">
                                     </div>
                                </div>
                           </div>
                           <div class="row">
                                <div class="col-sm-6 col-xs-12">
                                     <div class="form-group">
                                          <label class="control-label">City:</label>
                                          <input type="text" name="city" class="form-control">
                                     </div>
                                </div>
                                <div class="col-sm-6 col-xs-12">
                                     <div class="form-group">
                                          <label class="control-label">State:</label>
                                          <input type="text" name="state" class="form-control">
                                     </div>
                                </div>
                           </div>
                           <div class="row">
                                <div class="col-sm-6 col-xs-12">
                                     <div class="form-group">
                                          <label class="control-label">Zip:</label>
                                          <input type="text" name="zip" class="form-control">
                                     </div>
                                </div>
                                <div class="col-sm-6 col-xs-12">
                                     <div class="form-group">
                                          <label class="control-label">Country:</label>
                                          <select name="country" class="form-control">
                                               <option value="">-- Choose --</option>
                                               <option value="us">United States</option>
                                               <option value="uk">United Kingdom</option>
                                               <option value="ca">Canada</option>
                                          </select>
                                     </div>
                                </div>
                           </div>

                           <div class="row">
                                <div class="col-sm-6 col-xs-12">
                                     <div class="form-group">
                                          <label class="control-label">Payment method</label>

                                          <select name="payment_method" class="form-control">
                                               <option value="">-- Choose --</option>
                                               <option value="bank">Bank account</option>
                                               <option value="cash">Cash</option>
                                               <option value="paypal">PayPal</option>
                                          </select>
                                     </div>
                                </div>

                                <div class="col-sm-6 col-xs-12">
                                     <div class="form-group">
                                          <label class="control-label">Captcha</label>
                                          <input type="text" name="captcha" class="form-control">
                                     </div>
                                </div>
                           </div>

                           <div class="form-group">
                                <label class="control-label">
                                     <input type="checkbox" name="terms" value="1">

                                     I agree with the <a href="terms.php" target="_blank">Terms &amp; Conditions</a>
                                </label>
                           </div>

                           <div class="clearfix">
                                <div class="blue-button pull-left">
                                    <a href="javascript:history.back()">Back</a>
                                </div>

                                <div class="blue-button pull-right">
                                    <input type="submit" value="Finish" class="btn btn-primary">
                                </div>
                           </div>
                        </form>
                    </div>
                </div>
                
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-5">
                    <div class="about-veno">
                        <div class="logo">
                            <img src="<?php echo ASSETS_URL; ?>/img/footer_logo.png" alt="<?php echo SITE_NAME; ?> Logo">
                        </div>
                        <p>Mauris sit amet quam congue, pulvinar urna et, congue diam. Suspendisse eu lorem massa. Integer sit amet posuere tellustea dictumst.</p>
                        <ul class="social-icons">
                            <li>
                                <a href="#"><i class="fa fa-facebook"></i></a>
                                <a href="#"><i class="fa fa-twitter"></i></a>
                                <a href="#"><i class="fa fa-linkedin"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="useful-links">
                        <div class="footer-heading">
                            <h4>Useful Links</h4>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <ul>
                                    <li><a href="<?php echo url(); ?>"><i class="fa fa-stop"></i>Home</a></li>
                                    <li><a href="<?php echo url('pages/about-us.php'); ?>"><i class="fa fa-stop"></i>About</a></li>
                                    <li><a href="<?php echo url('pages/contact.php'); ?>"><i class="fa fa-stop"></i>Contact Us</a></li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul>
                                    <li><a href="<?php echo url('pages/products.php'); ?>"><i class="fa fa-stop"></i>Products</a></li>
                                    <li><a href="<?php echo url('pages/testimonials.php'); ?>"><i class="fa fa-stop"></i>Testimonials</a></li>
                                    <li><a href="<?php echo url('pages/blog.php'); ?>"><i class="fa fa-stop"></i>Blog</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="contact-info">
                        <div class="footer-heading">
                            <h4>Contact Information</h4>
                        </div>
                        <p><i class="fa fa-map-marker"></i> 212 Barrington Court New York, ABC</p>
                        <ul>
                            <li><span>Phone:</span><a href="#">+1 333 4040 5566</a></li>
                            <li><span>Email:</span><a href="#">contact@company.com</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <div class="sub-footer">
        <p>Copyright © <?php echo date('Y'); ?> <?php echo SITE_NAME; ?> - Template by: <a href="https://www.phpjabbers.com/">PHPJabbers.com</a></p>
    </div>

    <!-- JavaScript Files -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="<?php echo ASSETS_URL; ?>/js/vendor/jquery-1.11.2.min.js"><\/script>')</script>

    <script src="<?php echo ASSETS_URL; ?>/js/vendor/bootstrap.min.js"></script>
    <script src="<?php echo ASSETS_URL; ?>/js/datepicker.js"></script>
    <script src="<?php echo ASSETS_URL; ?>/js/plugins.js"></script>
    <script src="<?php echo ASSETS_URL; ?>/js/main.js"></script>

<?php
// PHP form processing logic can be added here
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Process form data
    $title = isset($_POST['title']) ? $_POST['title'] : '';
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    $address1 = isset($_POST['address1']) ? $_POST['address1'] : '';
    $address2 = isset($_POST['address2']) ? $_POST['address2'] : '';
    $city = isset($_POST['city']) ? $_POST['city'] : '';
    $state = isset($_POST['state']) ? $_POST['state'] : '';
    $zip = isset($_POST['zip']) ? $_POST['zip'] : '';
    $country = isset($_POST['country']) ? $_POST['country'] : '';
    $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';
    $captcha = isset($_POST['captcha']) ? $_POST['captcha'] : '';
    $terms = isset($_POST['terms']) ? $_POST['terms'] : '';
    
    // Add your form processing logic here
    // For example: validate data, save to database, send emails, etc.
}
?>
</body>
</html>
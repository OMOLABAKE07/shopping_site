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
        <title><?php echo SITE_NAME; ?> - Testimonials</title>
        
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSS Files -->
        <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
                                <li><a href="<?php echo BASE_URL; ?>/">Home</a></li>
                                <li><a href="<?php echo BASE_URL; ?>/pages/products.php">Products</a></li>
                                <li><a href="<?php echo BASE_URL; ?>/pages/checkout.php">Checkout</a></li>
                                <li class='active'>
                                    <a href="#">About</a>
                                    <ul class="sub-menu">
                                        <li><a href="<?php echo BASE_URL; ?>/pages/about-us.php">About Us</a></li>
                                        <li><a href="<?php echo BASE_URL; ?>/pages/blog.php">Blog</a></li>
                                        <li class='active'><a href="<?php echo BASE_URL; ?>/pages/testimonials.php">Testimonials</a></li>
                                        <li><a href="<?php echo BASE_URL; ?>/pages/terms.php">Terms</a></li>
                                    </ul>
                                </li>
                                <li><a href="<?php echo BASE_URL; ?>/pages/contact.php">Contact Us</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </header>
    </div>
      
    <section class="banner banner-secondary" id="top" style="background-image: url('<?php echo ASSETS_URL; ?>/img/banner-image-3-1920x300.jpg');">
        <div class="container">
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <div class="banner-caption">
                        <div class="line-dec"></div>
                        <h2>Testimonials</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <main>
        <section class="featured-places">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="featured-item">
                            <div class="thumb">
                                <div class="thumb-img">
                                    <img src="<?php echo ASSETS_URL; ?>/img/popular_item_1.jpg" alt="Testimonial 1">
                                </div>
                            </div>
                            <div class="down-content">
                                <h4>John Doe</h4>
                                <span>Category One</span>
                                <p>Vestibulum id est eu felis vulputate hendrerit. Suspendisse dapibus turpis in dui pulvinar imperdiet. Nunc consectetur.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="featured-item">
                            <div class="thumb">
                                <div class="thumb-img">
                                    <img src="<?php echo ASSETS_URL; ?>/img/popular_item_2.jpg" alt="Testimonial 2">
                                </div>
                            </div>
                            <div class="down-content">
                                <h4>Jane Smith</h4>
                                <span>Category Two</span>
                                <p>Integer dapibus, est vel dapibus mattis, sem mauris luctus leo, ac pulvinar quam tortor a velit. Praesent ultrices erat ante.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Continue with other testimonials, updating image paths -->
                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="featured-item">
                            <div class="thumb">
                                <div class="thumb-img">
                                    <img src="<?php echo ASSETS_URL; ?>/img/popular_item_3.jpg" alt="Testimonial 3">
                                </div>
                            </div>
                            <!-- Rest of testimonial content -->
                        </div>
                    </div>

                    <!-- Add more testimonials with updated image paths -->
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
                        <!-- Rest of the footer content -->
                    </div>
                </div>
                <!-- Rest of the footer columns -->
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
</body>
</html>
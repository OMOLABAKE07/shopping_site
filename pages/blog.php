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
        <title><?php echo SITE_NAME; ?> - Blog</title>
        
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSS Files -->
        <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/hero-slider.css">
        <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/owl-carousel.css">
        <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/style.css">
        <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/datepicker.css">

        <!-- Preload Raleway font to avoid slow network warning -->
        <link rel="preload" href="https://fonts.gstatic.com/s/raleway/v34/1Ptug8zYS_SKggPNyC0IT4ttDfA.woff2" as="font" type="font/woff2" crossorigin>
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,200,300,400,500,600,700,800,900" rel="stylesheet">

        <!-- JavaScript Files -->
        <script src="<?php echo ASSETS_URL; ?>/js/vendor/modernizr-2.8.3-respond-1.4.2.min.js"></script>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="<?php echo ASSETS_URL; ?>/img/logo.png">
        <link rel="shortcut icon" type="image/png" href="<?php echo ASSETS_URL; ?>/img/logo.png">
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
                                        <li class='active'><a href="<?php echo BASE_URL; ?>/pages/blog.php">Blog</a></li>
                                        <li><a href="<?php echo BASE_URL; ?>/pages/testimonials.php">Testimonials</a></li>
                                        <li><a href="<?php echo BASE_URL; ?>/pages/terms.php">Terms</a></li>
                                    </ul>
                                </li>
                                <li><a href="<?php echo BASE_URL; ?>/pages/contact.php">Contact Us</a></li>
                            </ul>
                        </nav><!-- / #primary-nav -->
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
                        <h2>Blog</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <main>
        <section class="featured-places">
            <div class="container">
                <div class="row">
                    <div class="col-lg-9 col-md-8 col-xs-12">
                        <div class="row">
                            <div class="col-sm-6 col-xs-12">
                                <div class="featured-item">
                                    <div class="thumb">
                                        <div class="thumb-img">
                                            <img src="<?php echo ASSETS_URL; ?>/img/blog-1-720x480.jpg" alt="Blog Post 1">
                                        </div>

                                        <div class="overlay-content">
                                         <strong title="Author"><i class="fa fa-user"></i> John Doe</strong> &nbsp;&nbsp;&nbsp;&nbsp;
                                         <strong title="Posted on"><i class="fa fa-calendar"></i> 12/06/2020 10:30</strong> &nbsp;&nbsp;&nbsp;&nbsp;
                                         <strong title="Views"><i class="fa fa-map-marker"></i> 115</strong>
                                        </div>
                                    </div>

                                    <div class="down-content">
                                        <h4>Lorem ipsum dolor sit amet, consectetur adipisicing elit hic</h4>

                                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Enim consectetur assumenda nam facere voluptatibus totam veritatis. </p>

                                        <div class="text-button">
                                            <a href="<?php echo BASE_URL; ?>/pages/blog-details.php">Read More</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 col-xs-12">
                                <div class="featured-item">
                                    <div class="thumb">
                                        <div class="thumb-img">
                                            <img src="<?php echo ASSETS_URL; ?>/img/blog-2-720x480.jpg" alt="Blog Post 2">
                                        </div>

                                        <div class="overlay-content">
                                         <strong title="Author"><i class="fa fa-user"></i> John Doe</strong> &nbsp;&nbsp;&nbsp;&nbsp;
                                         <strong title="Posted on"><i class="fa fa-calendar"></i> 12/06/2020 10:30</strong> &nbsp;&nbsp;&nbsp;&nbsp;
                                         <strong title="Views"><i class="fa fa-map-marker"></i> 115</strong>
                                        </div>
                                    </div>

                                    <div class="down-content">
                                        <h4>Lorem ipsum dolor sit amet, consectetur adipisicing elit hic</h4>

                                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Enim consectetur assumenda nam facere voluptatibus totam veritatis. </p>

                                        <div class="text-button">
                                            <a href="<?php echo BASE_URL; ?>/pages/blog-details.php">Read More</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 col-xs-12">
                                <div class="featured-item">
                                    <div class="thumb">
                                        <div class="thumb-img">
                                            <img src="<?php echo ASSETS_URL; ?>/img/blog-3-720x480.jpg" alt="Blog Post 3">
                                        </div>

                                        <div class="overlay-content">
                                         <strong title="Author"><i class="fa fa-user"></i> John Doe</strong> &nbsp;&nbsp;&nbsp;&nbsp;
                                         <strong title="Posted on"><i class="fa fa-calendar"></i> 12/06/2020 10:30</strong> &nbsp;&nbsp;&nbsp;&nbsp;
                                         <strong title="Views"><i class="fa fa-map-marker"></i> 115</strong>
                                        </div>
                                    </div>

                                    <div class="down-content">
                                        <h4>Lorem ipsum dolor sit amet, consectetur adipisicing elit hic</h4>

                                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Enim consectetur assumenda nam facere voluptatibus totam veritatis. </p>

                                        <div class="text-button">
                                            <a href="<?php echo BASE_URL; ?>/pages/blog-details.php">Read More</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 col-xs-12">
                                <div class="featured-item">
                                    <div class="thumb">
                                        <div class="thumb-img">
                                            <img src="<?php echo ASSETS_URL; ?>/img/blog-4-720x480.jpg" alt="Blog Post 4">
                                        </div>

                                        <div class="overlay-content">
                                         <strong title="Author"><i class="fa fa-user"></i> John Doe</strong> &nbsp;&nbsp;&nbsp;&nbsp;
                                         <strong title="Posted on"><i class="fa fa-calendar"></i> 12/06/2020 10:30</strong> &nbsp;&nbsp;&nbsp;&nbsp;
                                         <strong title="Views"><i class="fa fa-map-marker"></i> 115</strong>
                                        </div>
                                    </div>

                                    <div class="down-content">
                                        <h4>Lorem ipsum dolor sit amet, consectetur adipisicing elit hic</h4>

                                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Enim consectetur assumenda nam facere voluptatibus totam veritatis. </p>

                                        <div class="text-button">
                                            <a href="<?php echo BASE_URL; ?>/pages/blog-details.php">Read More</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 col-xs-12">
                                <div class="featured-item">
                                    <div class="thumb">
                                        <div class="thumb-img">
                                            <img src="<?php echo ASSETS_URL; ?>/img/blog-5-720x480.jpg" alt="Blog Post 5">
                                        </div>

                                        <div class="overlay-content">
                                         <strong title="Author"><i class="fa fa-user"></i> John Doe</strong> &nbsp;&nbsp;&nbsp;&nbsp;
                                         <strong title="Posted on"><i class="fa fa-calendar"></i> 12/06/2020 10:30</strong> &nbsp;&nbsp;&nbsp;&nbsp;
                                         <strong title="Views"><i class="fa fa-map-marker"></i> 115</strong>
                                        </div>
                                    </div>

                                    <div class="down-content">
                                        <h4>Lorem ipsum dolor sit amet, consectetur adipisicing elit hic</h4>

                                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Enim consectetur assumenda nam facere voluptatibus totam veritatis. </p>

                                        <div class="text-button">
                                            <a href="<?php echo BASE_URL; ?>/pages/blog-details.php">Read More</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 col-xs-12">
                                <div class="featured-item">
                                    <div class="thumb">
                                        <div class="thumb-img">
                                            <img src="<?php echo ASSETS_URL; ?>/img/blog-6-720x480.jpg" alt="Blog Post 6">
                                        </div>

                                        <div class="overlay-content">
                                         <strong title="Author"><i class="fa fa-user"></i> John Doe</strong> &nbsp;&nbsp;&nbsp;&nbsp;
                                         <strong title="Posted on"><i class="fa fa-calendar"></i> 12/06/2020 10:30</strong> &nbsp;&nbsp;&nbsp;&nbsp;
                                         <strong title="Views"><i class="fa fa-map-marker"></i> 115</strong>
                                        </div>
                                    </div>

                                    <div class="down-content">
                                        <h4>Lorem ipsum dolor sit amet, consectetur adipisicing elit hic</h4>

                                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Enim consectetur assumenda nam facere voluptatibus totam veritatis. </p>

                                        <div class="text-button">
                                            <a href="<?php echo BASE_URL; ?>/pages/blog-details.php">Read More</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-4 col-xs-12">
                        <div class="form-group">
                            <h4>Blog Search</h4>
                        </div>

                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search" aria-label="Search" aria-describedby="basic-addon2">

                                <span class="input-group-addon"><a href="#"><i class="fa fa-search"></i></a></span>
                            </div>
                        </div>

                        <br>

                        <div class="form-group">
                            <h4>Lorem ipsum dolor sit amet</h4>
                        </div>

                        <p><a href="#">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos quae animi sint.</a></p>
                        <p><a href="#">Non, magni, sequi. Explicabo illum quas debitis ut hic possimus, nesciunt mag!</a></p>
                        <p><a href="#">Vatae expedita deleniti optio ex adipisci animi, iusto tempora. </a></p>
                        <p><a href="#">Soluta non modi dolorem voluptates. Maiores est, molestiae dolor laborum.</a></p>
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
                                    <li><a href="index.php"><i class="fa fa-stop"></i>Home</a></li>
                                    <li><a href="about-us.php"><i class="fa fa-stop"></i>About</a></li>
                                    <li><a href="contact.php"><i class="fa fa-stop"></i>Contact Us</a></li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul>
                                    <li><a href="products.php"><i class="fa fa-stop"></i>Products</a></li>
                                    <li><a href="testimonials.php"><i class="fa fa-stop"></i>Testimonials</a></li>
                                    <li><a href="blog.php"><i class="fa fa-stop"></i>Blog</a></li>
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

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js" type="text/javascript"></script>
    <script>window.jQuery || document.write('<script src="<?php echo ASSETS_URL; ?>/js/vendor/jquery-1.11.2.min.js"><\/script>')</script>

    <script src="<?php echo ASSETS_URL; ?>/js/vendor/bootstrap.min.js"></script>
    
    <script src="<?php echo ASSETS_URL; ?>/js/datepicker.js"></script>
    <script src="<?php echo ASSETS_URL; ?>/js/plugins.js"></script>
    <script src="<?php echo ASSETS_URL; ?>/js/main.js"></script>
</body>
</html>
<?php
// Load configuration and autoloader first
require_once __DIR__ . '/../config/paths.php';

// Then start session and include header
Session::start();
require_once __DIR__ . '/../includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?php echo SITE_NAME; ?> - Home</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSS Files -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/hero-slider.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/owl-carousel.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,200,300,400,500,600,700,800,900" rel="stylesheet">
    <link rel="preload" href="https://fonts.gstatic.com/s/raleway/v34/1Ptug8zYS_SKggPNyC0IT4ttDfA.woff2" as="font" type="font/woff2" crossorigin>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo ASSETS_URL; ?>/img/logo.png">
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
                                <li class='active'><a href="<?php echo BASE_URL; ?>/">Home</a></li>
                                <li><a href="<?php echo BASE_URL; ?>/pages/products.php">Products</a></li>
                                <li><a href="<?php echo BASE_URL; ?>/checkout.php">Checkout</a></li>
                                <li>
                                    <a href="#">About</a>
                                    <ul class="sub-menu">
                                        <li><a href="<?php echo BASE_URL; ?>/pages/about-us.php">About Us</a></li>
                                        <li><a href="<?php echo BASE_URL; ?>/pages/blog.php">Blog</a></li>
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
      
    <section class="banner" id="top" style="background-image: url(<?php echo ASSETS_URL; ?>/img/homepage-banner-image-1920x700.jpg);">
        <div class="container">
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <div class="banner-caption">
                        <div class="line-dec"></div>
                        <h2>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</h2>
                        <div class="blue-button">
                            <a href="contact.php">Contact Us</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <main>
        <section class="our-services">
            <div class="container">
                <div class="row">
                    <div class="col-md-7">
                        <div class="left-content">
                            <br>
                            <h4>About us</h4>
                            <p>Aenean hendrerit metus leo, quis viverra purus condimentum nec. Pellentesque a sem semper, lobortis mauris non, varius urna. Quisque sodales purus eu tellus fringilla.<br><br>Mauris sit amet quam congue, pulvinar urna et, congue diam. Suspendisse eu lorem massa. Integer sit amet posuere tellus, id efficitur leo. In hac habitasse platea dictumst. Vel sequi odit similique repudiandae ipsum iste, quidem tenetur id impedit, eaque et, aliquam quod.</p>
                            <div class="blue-button">
                                <a href="about-us.php">Discover More</a>
                            </div>

                            <br>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <img src="<?php echo ASSETS_URL; ?>/img/about-1-720x480.jpg" class="img-fluid" alt="About Us">
                    </div>
                </div>
            </div>
        </section>

        <section class="featured-places">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="section-heading">
                            <span>Featured Products</span>
                            <h2>Lorem ipsum dolor sit amet ctetur.</h2>
                        </div>
                    </div> 
                </div> 
                <div class="row">
                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="featured-item">
                            <div class="thumb">
                                <img src="<?php echo ASSETS_URL; ?>/img/product-1-720x480.jpg" alt="Product 1">
                            </div>
                            <div class="down-content">
                                <h4>Lorem ipsum dolor sit amet.</h4>

                                <span><del><sup>$</sup>99.00 </del> <strong><sup>$</sup>79.00</strong></span>

                                <p>Vestibulum id est eu felis vulputate hendrerit. Suspendisse dapibus turpis in dui pulvinar imperdiet. Nunc consectetur.</p>

                                <div class="text-button">
                                    <a href="product-details.php">View More</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="featured-item">
                            <div class="thumb">
                                <img src="<?php echo ASSETS_URL; ?>/img/product-2-720x480.jpg" alt="Product 2">
                            </div>
                            <div class="down-content">
                                <h4>Lorem ipsum dolor sit.</h4>

                                <span><del><sup>$</sup>999.00 </del> <strong><sup>$</sup>779.00</strong></span>

                                <p>Vestibulum id est eu felis vulputate hendrerit. Suspendisse dapibus turpis in dui pulvinar imperdiet. Nunc consectetur.</p>

                                <div class="text-button">
                                    <a href="product-details.php">View More</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="featured-item">
                            <div class="thumb">
                                <img src="<?php echo ASSETS_URL; ?>/img/product-3-720x480.jpg" alt="Product 3">
                            </div>
                            <div class="down-content">
                                <h4>Lorem ipsum dolor sit amet.</h4>

                                <span><del><sup>$</sup>1999.00 </del> <strong><sup>$</sup>1779.00</strong></span>

                                <p>Vestibulum id est eu felis vulputate hendrerit. Suspendisse dapibus turpis in dui pulvinar imperdiet. Nunc consectetur.</p>

                                <div class="text-button">
                                    <a href="product-details.php">View More</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="featured-item">
                            <div class="thumb">
                                <img src="<?php echo ASSETS_URL; ?>/img/product-4-720x480.jpg" alt="Product 4">
                            </div>
                            <div class="down-content">
                                <h4>Lorem ipsum dolor sit amet.</h4>

                                <span><del><sup>$</sup>99.00 </del> <strong><sup>$</sup>79.00</strong></span>

                                <p>Vestibulum id est eu felis vulputate hendrerit. Suspendisse dapibus turpis in dui pulvinar imperdiet. Nunc consectetur.</p>

                                <div class="text-button">
                                    <a href="product-details.php">View More</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="featured-item">
                            <div class="thumb">
                                <img src="<?php echo ASSETS_URL; ?>/img/product-5-720x480.jpg" alt="Product 5">
                            </div>
                            <div class="down-content">
                                <h4>Lorem ipsum dolor sit.</h4>

                                <span><del><sup>$</sup>999.00 </del> <strong><sup>$</sup>779.00</strong></span>

                                <p>Vestibulum id est eu felis vulputate hendrerit. Suspendisse dapibus turpis in dui pulvinar imperdiet. Nunc consectetur.</p>

                                <div class="text-button">
                                    <a href="product-details.php">View More</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="featured-item">
                            <div class="thumb">
                                <img src="<?php echo ASSETS_URL; ?>/img/product-6-720x480.jpg" alt="Product 6">
                            </div>
                            <div class="down-content">
                                <h4>Lorem ipsum dolor sit amet.</h4>

                                <span><del><sup>$</sup>1999.00 </del> <strong><sup>$</sup>1779.00</strong></span>

                                <p>Vestibulum id est eu felis vulputate hendrerit. Suspendisse dapibus turpis in dui pulvinar imperdiet. Nunc consectetur.</p>

                                <div class="text-button">
                                    <a href="product-details.php">View More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="featured-places">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="section-heading">
                            <span>Latest blog posts</span>
                            <h2>Lorem ipsum dolor sit amet ctetur.</h2>
                        </div>
                    </div> 
                </div> 
                <div class="row">
                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="featured-item">
                            <div class="thumb">
                                <div class="thumb-img">
                                    <img src="<?php echo ASSETS_URL; ?>/img/blog-1-720x480.jpg" alt="">
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
                                    <a href="blog-details.php">Read More</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="featured-item">
                            <div class="thumb">
                                <div class="thumb-img">
                                    <img src="<?php echo ASSETS_URL; ?>/img/blog-2-720x480.jpg" alt="">
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
                                    <a href="blog-details.php">Read More</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="featured-item">
                            <div class="thumb">
                                <div class="thumb-img">
                                    <img src="<?php echo ASSETS_URL; ?>/img/blog-3-720x480.jpg" alt="">
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
                                    <a href="blog-details.php">Read More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="video-container">
            <div class="video-overlay"></div>
            <div class="video-content">
                <div class="inner">
                      <div class="section-heading">
                          <span>Contact Us</span>
                          <h2>Vivamus nec vehicula felis</h2>
                      </div>
                      <!-- Modal button -->

                      <div class="blue-button">
                        <a href="contact.php">Talk to us</a>
                      </div>
                </div>
            </div>
        </section>

        <section class="popular-places" id="popular">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="section-heading">
                            <span>Testimonials</span>
                            <h2>Lorem ipsum dolor sit amet</h2>
                        </div>
                    </div> 
                </div> 

                <div class="owl-carousel owl-theme">
                    <div class="item popular-item">
                        <div class="thumb">
                            <img src="<?php echo ASSETS_URL; ?>/img/popular_item_1.jpg" alt="">
                            <div class="text-content">
                                <h4>John Doe</h4>
                                <span>"Lorem ipsum dolor sit amet, consectetur an adipisicing elit. Itaque, corporis nulla at quia quaerat."</span>
                            </div>
                            <div class="plus-button">
                                <a href="testimonials.php"><i class="fa fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="item popular-item">
                        <div class="thumb">
                            <img src="<?php echo ASSETS_URL; ?>/img/popular_item_2.jpg" alt="">
                            <div class="text-content">
                                <h4>John Doe</h4>
                                <span>"Lorem ipsum dolor sit amet, consectetur an adipisicing elit. Itaque, corporis nulla at quia quaerat."</span>
                            </div>
                            <div class="plus-button">
                                <a href="testimonials.php"><i class="fa fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="item popular-item">
                        <div class="thumb">
                            <img src="<?php echo ASSETS_URL; ?>/img/popular_item_3.jpg" alt="">
                            <div class="text-content">
                                <h4>John Doe</h4>
                                <span>"Lorem ipsum dolor sit amet, consectetur an adipisicing elit. Itaque, corporis nulla at quia quaerat."</span>
                            </div>
                            <div class="plus-button">
                                <a href="testimonials.php"><i class="fa fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="item popular-item">
                        <div class="thumb">
                            <img src="<?php echo ASSETS_URL; ?>/img/popular_item_4.jpg" alt="">
                            <div class="text-content">
                                <h4>John Doe</h4>
                                <span>"Lorem ipsum dolor sit amet, consectetur an adipisicing elit. Itaque, corporis nulla at quia quaerat."</span>
                            </div>
                            <div class="plus-button">
                                <a href="testimonials.php"><i class="fa fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="item popular-item">
                        <div class="thumb">
                            <img src="<?php echo ASSETS_URL; ?>/img/popular_item_5.jpg" alt="">
                            <div class="text-content">
                                <h4>John Doe</h4>
                                <span>"Lorem ipsum dolor sit amet, consectetur an adipisicing elit. Itaque, corporis nulla at quia quaerat."</span>
                            </div>
                            <div class="plus-button">
                                <a href="testimonials.php"><i class="fa fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="item popular-item">
                        <div class="thumb">
                            <img src="<?php echo ASSETS_URL; ?>/img/popular_item_1.jpg" alt="">
                            <div class="text-content">
                                <h4>John Doe</h4>
                                <span>"Lorem ipsum dolor sit amet, consectetur an adipisicing elit. Itaque, corporis nulla at quia quaerat."</span>
                            </div>
                            <div class="plus-button">
                                <a href="testimonials.php"><i class="fa fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="item popular-item">
                        <div class="thumb">
                            <img src="<?php echo ASSETS_URL; ?>/img/popular_item_2.jpg" alt="">
                            <div class="text-content">
                                <h4>John Doe</h4>
                                <span>"Lorem ipsum dolor sit amet, consectetur an adipisicing elit. Itaque, corporis nulla at quia quaerat."</span>
                            </div>
                            <div class="plus-button">
                                <a href="testimonials.php"><i class="fa fa-plus"></i></a>
                            </div>
                        </div>
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
                                    <li><a href="<?php echo BASE_URL; ?>/index.php"><i class="fa fa-stop"></i>Home</a></li>
                                    <li><a href="<?php echo BASE_URL; ?>/pages/about-us.php"><i class="fa fa-stop"></i>About</a></li>
                                    <li><a href="<?php echo BASE_URL; ?>/pages/contact.php"><i class="fa fa-stop"></i>Contact Us</a></li>
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


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="<?php echo ASSETS_URL; ?>/js/vendor/jquery-1.11.2.min.js"><\/script>')</script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="<?php echo ASSETS_URL; ?>/js/plugins.js"></script>
<script src="<?php echo ASSETS_URL; ?>/js/main.js"></script>

</body>
</html><div class="container">
    <?php
    // Example of dynamic content
    $featured_products_query = "SELECT * FROM products WHERE featured = 1 LIMIT 4";
    $result = mysqli_query($conn, $featured_products_query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($product = mysqli_fetch_assoc($result)) {
            // Display product
            echo '<div class="product-item">';
            echo '<h3>' . htmlspecialchars($product['name']) . '</h3>';
            echo '<p>' . htmlspecialchars($product['description']) . '</p>';
            echo '<p>Price: $' . number_format($product['price'], 2) . '</p>';
            echo '</div>';
        }
    }
    ?>
</div>

<?php
require_once 'includes/footer.php';
?>


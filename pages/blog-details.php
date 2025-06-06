<?php

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>PHPJabbers.com | Free Shopping Website Template</title>
        
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="css/hero-slider.css">
        <link rel="stylesheet" href="css/owl-carousel.css">
        <link rel="stylesheet" href="css/style.css">

        <link href="https://fonts.googleapis.com/css?family=Raleway:100,200,300,400,500,600,700,800,900" rel="stylesheet">

        <script src="js/vendor/modernizr-2.8.3-respond-1.4.2.min.js"></script>
    </head>

<body>
 
    <div class="wrap">
        <header id="header">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <button id="primary-nav-button" type="button">Menu</button>
                        <a href="index.php"><div class="logo">
                            <img src="img/logo.png" alt="Venue Logo">
                        </div></a>
                        <nav id="primary-nav" class="dropdown cf">
                            <ul class="dropdown menu">
                                <li><a href="<?php echo url(); ?>">Home</a></li>
                                <li><a href="<?php echo url('pages/products.php'); ?>">Products</a></li>
                                <li><a href="<?php echo url('pages/checkout.php'); ?>">Checkout</a></li>
                                <li class='active'>
                                    <a href="#">About</a>
                                    <ul class="sub-menu">
                                        <li><a href="<?php echo url('pages/about-us.php'); ?>">About Us</a></li>
                                        <li class='active'><a href="<?php echo url('pages/blog.php'); ?>">Blog</a></li>
                                        <li><a href="<?php echo url('pages/testimonials.php'); ?>">Testimonials</a></li>
                                        <li><a href="<?php echo url('pages/terms.php'); ?>">Terms</a></li>
                                    </ul>
                                </li>
                                <li><a href="<?php echo url('pages/contact.php'); ?>">Contact Us</a></li>
                            </ul>
                        </nav><!-- / #primary-nav -->
                    </div>
                </div>
            </div>
        </header>
    </div>
      
    <section class="banner banner-secondary" id="top" style="background-image: url(img/banner-image-1-1920x300.jpg);">
        <div class="container">
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <div class="banner-caption">
                        <div class="line-dec"></div>
                        <h2>LOREM IPSUM DOLOR SIT AMET, CONSECTETUR ADIPISICING</h2>

                        <h4><i class="fa fa-user"></i>John Doe  &nbsp;&nbsp;&nbsp;&nbsp;  <i class="fa fa-calendar"></i> 12/06/2020 10:30   &nbsp;&nbsp;&nbsp;&nbsp; <i class="fa fa-eye"></i> 114</h4>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <main>
        <section class="featured-places">
            <div class="container">
                <div class="form-group">
                    <img src="img/blog-image-fullscren-1-1920x700.jpg" class="img-responsive" alt="">
                </div>

                <br>

                <h2>Lorem ipsum dolor sit amet, consectetur adipisicing.</h2>
                
                <p>Aenean hendrerit metus leo, quis viverra purus condimentum nec. Pellentesque a sem semper, lobortis mauris non, varius urna. Quisque sodales purus eu tellus fringilla.<br><br>Mauris sit amet quam congue, pulvinar urna et, congue diam. Suspendisse eu lorem massa. Integer sit amet posuere tellus, id efficitur leo. In hac habitasse platea dictumst. Vel sequi odit similique repudiandae ipsum iste, quidem tenetur id impedit, eaque et, aliquam quod.</p>

                <br>
                
                <h4>Lorem ipsum dolor sit amet.</h4>
                
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nulla, id quia maxime quas, unde sunt quaerat non! Sapiente aperiam, voluptatum voluptas recusandae qui veniam numquam voluptate ipsa earum quia dicta? Non praesentium quod vel ratione rerum dolor animi eligendi nisi, dolores culpa atque, deserunt veritatis incidunt quibusdam cumque obcaecati sit.</p>

                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quas unde tenetur dolorem eos esse, voluptatum iste iure voluptas dolor quo sit beatae. Odio laudantium eligendi ipsa pariatur eveniet doloremque quis voluptas veritatis repellendus laborum aspernatur sapiente mollitia totam fugit quod saepe earum, iste voluptatibus, aut aperiam iure omnis! Id libero quibusdam nisi fugiat, optio necessitatibus vitae magni incidunt ea tenetur?</p>

                <br>
                <br>


                <h4>Leave a comment</h4>

                <div class="row">
                    <div class="col-md-8">
                        <form id="contact" action="" method="post">
                            <div class="row">
                              <div class="col-lg-12 col-md-12 col-sm-12">
                                <fieldset>
                                  <input name="name" type="text" class="form-control" id="name" placeholder="Full Name" required="">
                                </fieldset>
                              </div>
                              <div class="col-lg-12 col-md-12 col-sm-12">
                                <fieldset>
                                  <input name="email" type="text" class="form-control" id="email" placeholder="E-Mail Address" required="">
                                </fieldset>
                              </div>
                              <div class="col-lg-12">
                                <fieldset>
                                  <textarea name="message" rows="6" class="form-control" id="message" placeholder="Your Message" required=""></textarea>
                                </fieldset>
                              </div>
                              <div class="col-lg-12">
                                <div class="blue-button">
                                    <a href="#">Submit</a>
                                </div>
                              </div>
                            </div>
                          </form>
                    </div>

                    <div class="col-md-4">
                      <div class="left-content">

                        <p>Lorem ipsum dolor sit amet, consectetur adipisic elit. Sed voluptate nihil eumester consectetur similiqu consectetur. Lorem ipsum dolor sit amet, consectetur adipisic elit. Et, consequuntur, modi mollitia corporis ipsa voluptate corrupti.</p>

                        <br> 

                        <ul class="list-inline">
                          <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                          <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                          <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                          <li><a href="#"><i class="fa fa-behance"></i></a></li>
                        </ul>
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
                            <img src="img/footer_logo.png" alt="Venue Logo">
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
        <p>Copyright © 2020 Company Name - Template by: <a href="https://www.phpjabbers.com/">PHPJabbers.com</a></p>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js" type="text/javascript"></script>
    <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.2.min.js"><\/script>')</script>

    <script src="js/vendor/bootstrap.min.js"></script>
    
    <script src="js/datepicker.js"></script>
    <script src="js/plugins.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
<?php
// Load configuration and autoloader first
require_once __DIR__ . '/../config/paths.php';
require_once __DIR__ . '/../config/database.php';  // Add database connection

// Start session
Session::start();

// Check if product ID is provided
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($product_id <= 0) {
    header("Location: " . BASE_URL . "/pages/products.php");
    exit;
}

// Fetch product details
$stmt = $pdo->prepare("SELECT id, name, description, price, sale_price, image_url FROM products WHERE id = ? AND status = 'active'");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$product) {
    header("Location: " . BASE_URL . "/pages/products.php");
    exit;
}

// Fetch product images
$stmt = $pdo->prepare("SELECT image_url, is_primary FROM product_images WHERE product_id = ?");
$stmt->execute([$product_id]);
$product_images = $stmt->fetchAll(PDO::FETCH_ASSOC);
$primary_image = $product['image_url'];
foreach ($product_images as $image) {
    if ($image['is_primary']) {
        $primary_image = $image['image_url'];
        break;
    }
}

// Handle Add to Cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: " . BASE_URL . "/pages/login.php?error=login_required");
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    $extra1 = isset($_POST['extra1']) ? htmlspecialchars($_POST['extra1']) : '';

    // Validate quantity
    if ($quantity <= 0) {
        $error = "Invalid quantity.";
    } else {
        // Check if product is already in cart
        $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
        $cart_item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cart_item) {
            // Update quantity if item exists
            $new_quantity = $cart_item['quantity'] + $quantity;
            $stmt = $pdo->prepare("UPDATE cart SET quantity = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$new_quantity, $cart_item['id']]);
        } else {
            // Insert new cart item
            $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $product_id, $quantity]);
        }

        $success = "Product added to cart!";
    }
}

// Now include the header after all potential redirects
require_once __DIR__ . '/../includes/header.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title><?php echo SITE_NAME; ?> - Product Details</title>
        
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
                        <a href="<?php echo BASE_URL; ?>/pages/index.php">
                            <div class="logo">
                                <img src="<?php echo ASSETS_URL; ?>/img/logo.png" alt="<?php echo SITE_NAME; ?> Logo">
                            </div>
                        </a>
                        <nav id="primary-nav" class="dropdown cf">
                            <ul class="dropdown menu">
                                <li><a href="<?php echo BASE_URL; ?>/pages/index.php">Home</a></li>
                                <li class='active'><a href="<?php echo BASE_URL; ?>/pages/products.php">Products</a></li>
                                <li><a href="<?php echo BASE_URL; ?>/pages/checkout.php">Checkout</a></li>
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
                        </nav>
                    </div>
                </div>
            </div>
        </header>
    </div>
      
    <section class="banner banner-secondary" id="top" style="background-image: url(<?php echo ASSETS_URL; ?>/img/banner-image-1-1920x300.jpg);">
        <div class="container">
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <div class="banner-caption">
                        <div class="line-dec"></div>
                        <h2>Lorem ipsum dolor sit amet, consectetur.</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <main>
        <section class="featured-places">
            <div class="container">
               <div class="row">
                  <div class="col-lg-6">
                    <div class="product-image">
                        <img src="<?php echo htmlspecialchars($primary_image); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-fluid" id="main-product-image">
                    </div>
                    <?php if (!empty($product_images)): ?>
                    <div class="product-thumbnails mt-3">
                        <div class="row">
                            <?php foreach ($product_images as $image): ?>
                            <div class="col-3">
                                <img src="<?php echo htmlspecialchars($image['image_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?> thumbnail" 
                                     class="img-fluid thumbnail" 
                                     onclick="updateMainImage('<?php echo htmlspecialchars($image['image_url']); ?>')"
                                     style="cursor: pointer;">
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                  </div>

                  <div class="col-lg-6">
                    <div class="product-info">
                        <h2><?php echo htmlspecialchars($product['name']); ?></h2>
                        <div class="price">
                            <?php if ($product['sale_price']): ?>
                                <span class="sale-price">$<?php echo number_format($product['sale_price'], 2); ?></span>
                                <span class="original-price">$<?php echo number_format($product['price'], 2); ?></span>
                            <?php else: ?>
                                <span class="regular-price">$<?php echo number_format($product['price'], 2); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="description">
                            <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                        </div>
                        <form action="" method="POST" class="add-to-cart-form">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <div class="form-group">
                                <label for="quantity">Quantity:</label>
                                <input type="number" id="quantity" name="quantity" class="form-control" value="1" min="1" max="<?php echo $product['stock']; ?>">
                            </div>
                            <button type="submit" name="add_to_cart" class="btn btn-primary">Add to Cart</button>
                        </form>
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
                                    <li><a href="inde.php"><i class="fa fa-stop"></i>Home</a></li>
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

    <!-- JavaScript Files -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="<?php echo ASSETS_URL; ?>/js/vendor/jquery-1.11.2.min.js"><\/script>')</script>

    <script src="<?php echo ASSETS_URL; ?>/js/vendor/bootstrap.min.js"></script>
    <script src="<?php echo ASSETS_URL; ?>/js/datepicker.js"></script>
    <script src="<?php echo ASSETS_URL; ?>/js/plugins.js"></script>
    <script src="<?php echo ASSETS_URL; ?>/js/main.js"></script>

    <script>
    function updateMainImage(src) {
        document.getElementById('main-product-image').src = src;
    }
    </script>
</body>
</html>
<?php
require_once CONFIG_PATH . '/paths.php';
Session::start();
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
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/hero-slider.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/owl-carousel.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/style.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/datepicker.css">

    <!-- Preload Raleway font -->
    <link rel="preload" href="https://fonts.gstatic.com/s/raleway/v34/1Ptug8zYS_SKggPNyC0IT4ttDfA.woff2" as="font" type="font/woff2" crossorigin>
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,200,300,400,500,600,700,800,900" rel="stylesheet">
</head>
<body>
    <?php require_once INCLUDES_PATH . '/header.php'; ?>

    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h1>Welcome to <?php echo SITE_NAME; ?></h1>
                <p class="lead">Your one-stop shop for all your needs.</p>
            </div>
        </div>
    </div>

    <?php require_once INCLUDES_PATH . '/footer.php'; ?>
</body>
</html> 
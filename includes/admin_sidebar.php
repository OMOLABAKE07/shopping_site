<?php
// This file contains the HTML structure for the admin sidebar navigation
// It is included in the admin pages (dashboard.php, orders.php, products.php, etc.)
?>

<nav class="admin-sidebar">
    <div class="sidebar-header">
        <img src="<?php echo ASSETS_URL; ?>/img/logo.png" alt="<?php echo SITE_NAME; ?> Logo">
        <h3>Admin Panel</h3>
    </div>
    <ul class="sidebar-menu">
        <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="products.php"><i class="fa fa-shopping-bag"></i> Products</a></li>
        <li><a href="orders.php"><i class="fa fa-shopping-cart"></i> Orders</a></li>
        <li><a href="categories.php"><i class="fa fa-tags"></i> Categories</a></li>
        <li><a href="users.php"><i class="fa fa-users"></i> Users</a></li>
        <li><a href="reviews.php"><i class="fa fa-star"></i> Reviews</a></li>
        <li><a href="<?php echo BASE_URL; ?>/"><i class="fa fa-home"></i> View Site</a></li>
        <li><a href="<?php echo url('logout.php'); ?>"><i class="fa fa-sign-out"></i> Logout</a></li>
    </ul>
</nav> 
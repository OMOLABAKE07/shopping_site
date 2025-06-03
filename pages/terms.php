<?php
// Load configuration and autoloader first
require_once __DIR__ . '/../config/paths.php';

// Start session
Session::start();

// Include header
include INCLUDES_PATH . '/header.php';
?>

<!-- Banner Section -->
<section class="banner banner-secondary" id="top" style="background-image: url('<?php echo ASSETS_URL; ?>/img/banner-image-3-1920x300.jpg');">
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="banner-caption">
                    <div class="line-dec"></div>
                    <h2>Terms and Conditions</h2>
                </div>
            </div>
        </div>
    </div>
</section>

<main>
    <section class="featured-places">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="section-heading">
                        <h2>Our Terms of Service</h2>
                        <p>Please read these terms carefully before using our services</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-body p-4">
                            <div class="terms-content">
                                <div class="terms-section mb-4">
                                    <h3 class="text-primary mb-3">1. Introduction</h3>
                                    <div class="terms-text">
                                        <p>Welcome to our online shopping website. These terms and conditions outline the rules and regulations for the use of our website.</p>
                                    </div>
                                </div>

                                <div class="terms-section mb-4">
                                    <h3 class="text-primary mb-3">2. Intellectual Property Rights</h3>
                                    <div class="terms-text">
                                        <p>Unless otherwise stated, we own the intellectual property rights for all material on this website. All intellectual property rights are reserved.</p>
                                    </div>
                                </div>

                                <div class="terms-section mb-4">
                                    <h3 class="text-primary mb-3">3. User Account</h3>
                                    <div class="terms-text">
                                        <p>To access certain features of the website, you must register for an account. You are responsible for maintaining the confidentiality of your account and password.</p>
                                    </div>
                                </div>

                                <div class="terms-section mb-4">
                                    <h3 class="text-primary mb-3">4. Product Information</h3>
                                    <div class="terms-text">
                                        <p>We strive to display accurate product information, including prices and availability. However, we do not guarantee that all information is accurate, complete, or current.</p>
                                    </div>
                                </div>

                                <div class="terms-section mb-4">
                                    <h3 class="text-primary mb-3">5. Pricing and Payment</h3>
                                    <div class="terms-text">
                                        <p>All prices are in the currency specified on the website. We reserve the right to change prices at any time. Payment must be made in full before goods are dispatched.</p>
                                    </div>
                                </div>

                                <div class="terms-section mb-4">
                                    <h3 class="text-primary mb-3">6. Shipping and Delivery</h3>
                                    <div class="terms-text">
                                        <p>Delivery times are estimates only. We are not responsible for delays beyond our control. Risk of loss and title for items purchased pass to you upon delivery.</p>
                                    </div>
                                </div>

                                <div class="terms-section mb-4">
                                    <h3 class="text-primary mb-3">7. Returns and Refunds</h3>
                                    <div class="terms-text">
                                        <p>You may return items within 30 days of delivery. Items must be unused and in original packaging. Refunds will be processed within 14 days of receiving returned items.</p>
                                    </div>
                                </div>

                                <div class="terms-section mb-4">
                                    <h3 class="text-primary mb-3">8. Privacy Policy</h3>
                                    <div class="terms-text">
                                        <p>Your use of this website is also governed by our Privacy Policy, which is incorporated into these terms by reference.</p>
                                    </div>
                                </div>

                                <div class="terms-section mb-4">
                                    <h3 class="text-primary mb-3">9. Limitation of Liability</h3>
                                    <div class="terms-text">
                                        <p>We shall not be liable for any indirect, incidental, special, consequential, or punitive damages resulting from your use of or inability to use the service.</p>
                                    </div>
                                </div>

                                <div class="terms-section mb-4">
                                    <h3 class="text-primary mb-3">10. Changes to Terms</h3>
                                    <div class="terms-text">
                                        <p>We reserve the right to modify these terms at any time. Changes will be effective immediately upon posting to the website.</p>
                                    </div>
                                </div>

                                <div class="terms-section mb-4">
                                    <h3 class="text-primary mb-3">11. Governing Law</h3>
                                    <div class="terms-text">
                                        <p>These terms shall be governed by and construed in accordance with the laws of your jurisdiction.</p>
                                    </div>
                                </div>

                                <div class="terms-section mb-4">
                                    <h3 class="text-primary mb-3">12. Contact Information</h3>
                                    <div class="terms-text">
                                        <p>If you have any questions about these Terms and Conditions, please contact us at <a href="mailto:support@shopping-site.com">support@shopping-site.com</a></p>
                                    </div>
                                </div>

                                <div class="terms-footer mt-5 pt-4 border-top">
                                    <p class="text-muted"><strong>Last updated:</strong> <?php echo date('F d, Y'); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<style>
.terms-content {
    font-size: 1.1rem;
    line-height: 1.6;
}

.terms-section {
    padding: 1rem;
    border-radius: 8px;
    background-color: #f8f9fa;
    transition: all 0.3s ease;
}

.terms-section:hover {
    background-color: #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.terms-text {
    color: #555;
}

.terms-text p {
    margin-bottom: 0;
}

.terms-footer {
    font-size: 0.9rem;
}

.banner-secondary {
    background-color: #f8f9fa;
    padding: 60px 0;
    text-align: center;
    position: relative;
}

.banner-secondary::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
}

.banner-caption {
    position: relative;
    z-index: 1;
    color: #fff;
}

.banner-caption h2 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.line-dec {
    width: 60px;
    height: 3px;
    background-color: #fff;
    margin: 0 auto 20px;
}

.section-heading {
    text-align: center;
    margin-bottom: 40px;
}

.section-heading h2 {
    font-size: 2rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 10px;
}

.section-heading p {
    color: #666;
    font-size: 1.1rem;
}
</style>

<?php include INCLUDES_PATH . '/footer.php'; ?>
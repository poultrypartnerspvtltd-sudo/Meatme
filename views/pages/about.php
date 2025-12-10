<!-- About Hero Section -->
<div class="container-fluid bg-gradient-success py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold text-white mb-3">About MeatMe</h1>
                <p class="lead text-white-50 mb-4">
                    Your trusted partner for fresh, high-quality chicken products delivered straight to your doorstep. 
                    We're passionate about providing the finest poultry with uncompromising quality and service.
                </p>
                <div class="d-flex gap-3">
                    <a href="<?= e(\App\Core\View::url('products')) ?>" class="btn btn-light btn-lg">
                        <i class="fas fa-shopping-cart me-2"></i>Shop Now
                    </a>
                    <a href="<?= e(\App\Core\View::url('contact')) ?>" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-phone me-2"></i>Contact Us
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <div class="position-relative">
                    <i class="fas fa-drumstick-bite display-1 text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Our Story Section -->
<div class="container py-5">
    <div class="row">
        <div class="col-lg-6 mb-4">
            <h2 class="fw-bold mb-4">Our Story</h2>
            <p class="text-muted mb-3">
                Founded with a simple mission: to provide fresh, high-quality chicken products to families across Nepal. 
                MeatMe started as a small family business with a commitment to excellence and has grown into a trusted 
                name in the poultry industry.
            </p>
            <p class="text-muted mb-3">
                We understand that quality matters when it comes to feeding your family. That's why we work directly 
                with local farmers who share our commitment to raising healthy, happy chickens in clean, humane conditions.
            </p>
            <p class="text-muted">
                Every product that reaches your table goes through rigorous quality checks to ensure freshness, 
                safety, and taste that you can trust.
            </p>
        </div>
        <div class="col-lg-6">
            <div class="bg-light rounded p-4 h-100 d-flex align-items-center justify-content-center">
                <div class="text-center">
                    <i class="fas fa-heart text-success fa-3x mb-3"></i>
                    <h4 class="text-success">Made with Love</h4>
                    <p class="text-muted mb-0">Every product is handled with care from farm to your table</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Our Values Section -->
<div class="bg-light py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold mb-3">Our Values</h2>
            <p class="text-muted">The principles that guide everything we do</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-leaf text-success fa-2x"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Fresh & Natural</h5>
                        <p class="text-muted mb-0">
                            We source only the freshest, naturally-raised chicken from trusted local farms. 
                            No artificial additives or preservatives.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-shield-alt text-success fa-2x"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Quality Assured</h5>
                        <p class="text-muted mb-0">
                            Every product undergoes strict quality control measures. We maintain the highest 
                            standards of hygiene and safety.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-truck text-success fa-2x"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Fast Delivery</h5>
                        <p class="text-muted mb-0">
                            Quick and reliable delivery service ensures your chicken products reach you fresh 
                            and on time, every time.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Section -->
<div class="container py-5">
    <div class="row text-center">
        <div class="col-md-3 mb-4">
            <div class="p-3">
                <h3 class="display-4 fw-bold text-success mb-2">5000+</h3>
                <p class="text-muted mb-0">Happy Customers</p>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="p-3">
                <h3 class="display-4 fw-bold text-success mb-2">50+</h3>
                <p class="text-muted mb-0">Partner Farms</p>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="p-3">
                <h3 class="display-4 fw-bold text-success mb-2">24/7</h3>
                <p class="text-muted mb-0">Customer Support</p>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="p-3">
                <h3 class="display-4 fw-bold text-success mb-2">99%</h3>
                <p class="text-muted mb-0">Satisfaction Rate</p>
            </div>
        </div>
    </div>
</div>

<!-- Team Section -->
<div class="bg-light py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold mb-3">Meet Our Team</h2>
            <p class="text-muted">The passionate people behind MeatMe</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px;">
                            <i class="fas fa-user text-success fa-3x"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Rajesh Sharma</h5>
                        <p class="text-success mb-2">Founder & CEO</p>
                        <p class="text-muted small mb-0">
                            With over 15 years in the poultry industry, Rajesh founded MeatMe to bring 
                            quality chicken products to every household.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px;">
                            <i class="fas fa-user text-success fa-3x"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Priya Thapa</h5>
                        <p class="text-success mb-2">Quality Manager</p>
                        <p class="text-muted small mb-0">
                            Priya ensures every product meets our high standards through rigorous 
                            quality control and testing procedures.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px;">
                            <i class="fas fa-user text-success fa-3x"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Amit Gurung</h5>
                        <p class="text-success mb-2">Operations Head</p>
                        <p class="text-muted small mb-0">
                            Amit manages our supply chain and delivery operations to ensure fresh 
                            products reach customers on time.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Why Choose Us Section -->
<div class="container py-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold mb-3">Why Choose MeatMe?</h2>
        <p class="text-muted">Here's what makes us different</p>
    </div>
    
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="d-flex align-items-start mb-4">
                <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px; min-width: 60px;">
                    <i class="fas fa-clock text-success"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-2">Same Day Delivery</h5>
                    <p class="text-muted mb-0">
                        Order before 2 PM and get your fresh chicken products delivered the same day. 
                        Perfect for last-minute meal planning.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="d-flex align-items-start mb-4">
                <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px; min-width: 60px;">
                    <i class="fas fa-thermometer-half text-success"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-2">Cold Chain Maintained</h5>
                    <p class="text-muted mb-0">
                        Our products are kept at optimal temperatures throughout the supply chain 
                        to ensure maximum freshness and safety.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="d-flex align-items-start mb-4">
                <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px; min-width: 60px;">
                    <i class="fas fa-money-bill-wave text-success"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-2">Best Prices</h5>
                    <p class="text-muted mb-0">
                        We work directly with farmers to eliminate middlemen, passing the savings 
                        on to you without compromising on quality.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="d-flex align-items-start mb-4">
                <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px; min-width: 60px;">
                    <i class="fas fa-headset text-success"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-2">24/7 Support</h5>
                    <p class="text-muted mb-0">
                        Our customer support team is always ready to help you with orders, 
                        queries, or any concerns you might have.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Call to Action Section -->
<div class="bg-success py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h3 class="fw-bold text-white mb-2">Ready to Experience Fresh Quality?</h3>
                <p class="text-white-50 mb-0">
                    Join thousands of satisfied customers who trust MeatMe for their chicken needs.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="<?= e(\App\Core\View::url('products')) ?>" class="btn btn-light btn-lg">
                    <i class="fas fa-shopping-cart me-2"></i>Start Shopping
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Contact Page -->
<div class="container py-5">
    
    <!-- Page Header -->
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold text-success mb-3">Contact Us</h1>
        <p class="lead text-muted">We'd love to hear from you! Reach us anytime through the following channels.</p>
    </div>
    
    <div class="row">
        
        <!-- Contact Information -->
        <div class="col-lg-6 mb-5">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body p-4">
                    <h3 class="card-title fw-bold mb-4">
                        <i class="fas fa-info-circle text-success me-2"></i>Get In Touch
                    </h3>
                    
                    <!-- Contact Details -->
                    <div class="contact-details">
                        <div class="row mb-4">
                            <div class="col-2 text-center">
                                <i class="fas fa-phone fa-2x text-success"></i>
                            </div>
                            <div class="col-10">
                                <h5 class="fw-bold mb-1">Phone</h5>
                                <p class="mb-0">
                                    <a href="tel:<?= e($contact_info['phone']) ?>" class="text-decoration-none text-success fw-bold">
                                        <?= e($contact_info['phone']) ?>
                                    </a>
                                </p>
                                <small class="text-muted">Call us for immediate assistance</small>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-2 text-center">
                                <i class="fas fa-envelope fa-2x text-success"></i>
                            </div>
                            <div class="col-10">
                                <h5 class="fw-bold mb-1">Email</h5>
                                <p class="mb-0">
                                    <a href="mailto:<?= e($contact_info['email']) ?>" class="text-decoration-none text-success fw-bold">
                                        <?= e($contact_info['email']) ?>
                                    </a>
                                </p>
                                <small class="text-muted">Send us an email anytime</small>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-2 text-center">
                                <i class="fas fa-map-marker-alt fa-2x text-success"></i>
                            </div>
                            <div class="col-10">
                                <h5 class="fw-bold mb-1">Address</h5>
                                <p class="mb-0 fw-bold"><?= e($contact_info['address']) ?></p>
                                <small class="text-muted">Visit our location</small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-2 text-center">
                                <i class="fas fa-clock fa-2x text-success"></i>
                            </div>
                            <div class="col-10">
                                <h5 class="fw-bold mb-1">Business Hours</h5>
                                <p class="mb-1"><strong>Mon - Fri:</strong> <?= e($contact_info['business_hours']['monday_friday']) ?></p>
                                <p class="mb-1"><strong>Saturday:</strong> <?= e($contact_info['business_hours']['saturday']) ?></p>
                                <p class="mb-0"><strong>Sunday:</strong> <?= e($contact_info['business_hours']['sunday']) ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="mt-4 pt-4 border-top">
                        <div class="row g-2">
                            <div class="col-6">
                                <a href="tel:<?= e($contact_info['phone']) ?>" class="btn btn-success w-100">
                                    <i class="fas fa-phone me-2"></i>Call Now
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="mailto:<?= e($contact_info['email']) ?>" class="btn btn-outline-success w-100">
                                    <i class="fas fa-envelope me-2"></i>Email Us
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Visual Highlight -->
        <div class="col-lg-6 mb-5">
            <div class="card h-100 shadow-sm border-0 overflow-hidden">
                <div class="card-body p-0 d-flex align-items-center justify-content-center bg-warning-subtle">
                    <img src="<?= e(\App\Core\Helpers::asset('images/deliveryguy.png')) ?>" 
                         alt="Delivery truck arriving from mobile order" 
                         class="img-fluid">
                </div>
            </div>
        </div>
    </div>
    
    
    <!-- Map Section -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-map-marked-alt me-2"></i>Find Us on Map
                    </h4>
                </div>
                <div class="card-body p-0">
                    <div class="map-container">
                        <iframe
                            src="https://www.google.com/maps?q=Sita+poultry+farm+Butwal+bellbariya&output=embed&z=15"
                            width="100%"
                            height="400"
                            style="border:0;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <p class="mb-0">
                                <i class="fas fa-map-marker-alt text-success me-2"></i>
                                <strong><?= e($contact_info['address']) ?></strong>
                            </p>
                            <small class="text-muted">Click on the map to get directions</small>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <a href="https://maps.google.com/?q=Sita+poultry+farm+Butwal+bellbariya" target="_blank" class="btn btn-outline-success">
                                <i class="fas fa-directions me-2"></i>Get Directions
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Additional Information -->
    <div class="row mt-5">
        <div class="col-md-4 mb-4">
            <div class="card text-center h-100 border-0 bg-light">
                <div class="card-body">
                    <i class="fas fa-shipping-fast fa-3x text-success mb-3"></i>
                    <h5 class="fw-bold">Fast Delivery</h5>
                    <p class="text-muted">Same-day delivery within Butwal area for orders placed before 6 PM.</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card text-center h-100 border-0 bg-light">
                <div class="card-body">
                    <i class="fas fa-leaf fa-3x text-success mb-3"></i>
                    <h5 class="fw-bold">Fresh Quality</h5>
                    <p class="text-muted">Farm-fresh chicken delivered within 24 hours of processing.</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card text-center h-100 border-0 bg-light">
                <div class="card-body">
                    <i class="fas fa-headset fa-3x text-success mb-3"></i>
                    <h5 class="fw-bold">24/7 Support</h5>
                    <p class="text-muted">Our customer support team is available to help you anytime.</p>
                </div>
            </div>
        </div>
    </div>
    
</div>

<!-- Floating WhatsApp Icon -->
<a href="https://wa.me/9779811075627" target="_blank" class="whatsapp-floating" title="Chat with us on WhatsApp">
    <i class="fab fa-whatsapp"></i>
</a>

<!-- Custom WhatsApp Styles -->
<style>
/* Floating WhatsApp Icon */
.whatsapp-floating {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: #25D366;
    color: white;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    font-size: 28px;
    z-index: 999;
    box-shadow: 0 4px 20px rgba(37, 211, 102, 0.4);
    transition: all 0.3s ease;
    animation: whatsapp-pulse 2s infinite;
}

.whatsapp-floating:hover {
    background-color: #20b358;
    transform: scale(1.1);
    box-shadow: 0 6px 25px rgba(37, 211, 102, 0.5);
    color: white;
    text-decoration: none;
}

@keyframes whatsapp-pulse {
    0% {
        box-shadow: 0 4px 20px rgba(37, 211, 102, 0.4);
    }
    50% {
        box-shadow: 0 4px 30px rgba(37, 211, 102, 0.6);
    }
    100% {
        box-shadow: 0 4px 20px rgba(37, 211, 102, 0.4);
    }
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .whatsapp-floating {
        bottom: 15px;
        right: 15px;
        width: 55px;
        height: 55px;
        font-size: 24px;
    }
    
}

.contact-details .row {
    transition: all 0.3s ease;
}

.contact-details .row:hover {
    background-color: #f8f9fa;
    border-radius: 10px;
    padding: 15px;
    margin: -15px;
}

.contact-details a {
    transition: all 0.3s ease;
}

.contact-details a:hover {
    transform: translateY(-2px);
}

.map-container iframe {
    transition: all 0.3s ease;
}

.map-container:hover iframe {
    filter: brightness(1.1);
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
}

@media (max-width: 768px) {
    .contact-details .row {
        margin-bottom: 2rem;
    }
    
    .contact-details .col-2 {
        flex: 0 0 20%;
        max-width: 20%;
    }
    
    .contact-details .col-10 {
        flex: 0 0 80%;
        max-width: 80%;
    }
}
</style>

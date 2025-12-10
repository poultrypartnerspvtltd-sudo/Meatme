<!-- Order Success Page -->
<div class="container py-5">
    <!-- Success Message -->
    <div class="text-center mb-5">
        <div class="success-icon mb-4">
            <span style="font-size: 5rem;">😊</span>
        </div>
        <h1 class="display-4 fw-bold text-success mb-3">Order Submitted Successfully!</h1>
        <p class="lead text-muted mb-4">😊 Your order has been submitted successfully. Thank you for choosing MeatMe!</p>

        <?php if (isset($_GET['order_number'])): ?>
            <div class="alert alert-success border-0 shadow-sm">
                <h4 class="alert-heading mb-2">
                    <i class="fas fa-receipt me-2"></i>Order #<?= htmlspecialchars($_GET['order_number']) ?>
                </h4>
                <p class="mb-0">Your order has been placed successfully. We're preparing your fresh meat.</p>
            </div>
        <?php endif; ?>

        <!-- Continue Shopping Button -->
        <div class="mt-4">
            <a href="<?= e(\App\Core\View::url()) ?>" class="btn btn-success btn-lg px-5 py-3">
                <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Order Details -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>What Happens Next?
                    </h4>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item completed">
                            <div class="timeline-marker bg-success">
                                <i class="fas fa-check text-white"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="fw-bold text-success">Order Confirmed</h6>
                                <p class="text-muted mb-0">Your order has been received and confirmed.</p>
                            </div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary">
                                <i class="fas fa-clock text-white"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="fw-bold">Preparing Your Order</h6>
                                <p class="text-muted mb-0">We're carefully preparing your fresh meat order.</p>
                                <small class="text-muted">Estimated time: 2-4 hours</small>
                            </div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-marker bg-warning">
                                <i class="fas fa-truck text-white"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="fw-bold">Out for Delivery</h6>
                                <p class="text-muted mb-0">Your order is on its way to you.</p>
                                <small class="text-muted">Delivery within Kathmandu Valley</small>
                            </div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-marker">
                                <i class="fas fa-box text-muted"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="fw-bold text-muted">Delivered</h6>
                                <p class="text-muted mb-0">Fresh meat delivered to your doorstep.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Email Confirmation -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body text-center py-4">
                    <i class="fas fa-envelope fa-3x text-primary mb-3"></i>
                    <h5 class="fw-bold mb-2">Check Your Email</h5>
                    <p class="text-muted mb-3">
                        We've sent a confirmation email to your email address with order details and tracking information.
                    </p>
                    <small class="text-muted">
                        If you don't see the email, please check your spam/junk folder.
                    </small>
                </div>
            </div>
        </div>

        <!-- Order Summary & Actions -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-tasks me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?= e(\App\Core\View::url()) ?>" class="btn btn-success btn-lg">
                            <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
                        </a>
                        <a href="<?= e(\App\Core\View::url('orders')) ?>" class="btn btn-outline-primary">
                            <i class="fas fa-list me-2"></i>View My Orders
                        </a>
                        <a href="<?= e(\App\Core\View::url('contact')) ?>" class="btn btn-outline-success">
                            <i class="fab fa-whatsapp me-2"></i>Contact Support
                        </a>
                    </div>
                </div>
            </div>

            <!-- Delivery Information -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-truck me-2"></i>Delivery Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <i class="fas fa-clock fa-2x text-info mb-2"></i>
                            <p class="small mb-0">Same Day</p>
                        </div>
                        <div class="col-4">
                            <i class="fas fa-map-marker-alt fa-2x text-success mb-2"></i>
                            <p class="small mb-0">Kathmandu Valley</p>
                        </div>
                        <div class="col-4">
                            <i class="fas fa-shield-alt fa-2x text-warning mb-2"></i>
                            <p class="small mb-0">Cold Chain</p>
                        </div>
                    </div>

                    <hr class="my-3">

                    <div class="small text-muted">
                        <p class="mb-2">
                            <i class="fas fa-phone me-1"></i>
                            <strong>Support:</strong> <a href="tel:+9779811075627" class="text-decoration-none text-muted">+977-9811075627</a>
                        </p>
                        <p class="mb-2">
                            <i class="fas fa-envelope me-1"></i>
                            <strong>Email:</strong> <a href="mailto:meatme9898@gmail.com" class="text-decoration-none text-muted">meatme9898@gmail.com</a>
                        </p>
                        <p class="mb-0">
                            <i class="fas fa-clock me-1"></i>
                            <strong>Hours:</strong> 24/7 Service
                        </p>
                    </div>
                </div>
            </div>

            <!-- Quality Assurance -->
            <div class="alert alert-light border mt-4">
                <div class="d-flex align-items-start">
                    <i class="fas fa-award fa-2x text-success me-3"></i>
                    <div>
                        <h6 class="fw-bold mb-1">Quality Guaranteed</h6>
                        <p class="small text-muted mb-0">
                            All our products are hygienically processed within 24 hours and delivered fresh.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.success-icon {
    animation: checkmark 0.8s ease-in-out;
}

@keyframes checkmark {
    0% {
        transform: scale(0) rotate(-180deg);
        opacity: 0;
    }
    50% {
        transform: scale(1.2) rotate(10deg);
        opacity: 0.9;
    }
    70% {
        transform: scale(0.95) rotate(-5deg);
    }
    100% {
        transform: scale(1) rotate(0deg);
        opacity: 1;
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.success-icon {
    animation: checkmark 1s ease-out;
}

.card {
    animation: fadeInUp 0.6s ease-out;
}

.card:nth-child(2) {
    animation-delay: 0.2s;
    animation-fill-mode: both;
}

.card:nth-child(3) {
    animation-delay: 0.4s;
    animation-fill-mode: both;
}

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-item:last-child {
    margin-bottom: 0;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    border: 3px solid #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    z-index: 1;
}

.timeline-item.completed .timeline-marker {
    background-color: #28a745;
}

.timeline-item.completed::before {
    content: '';
    position: absolute;
    left: 3px;
    top: 35px;
    width: 2px;
    height: calc(100% - 30px);
    background: #28a745;
}

.timeline-content h6 {
    margin-bottom: 5px;
    font-size: 1rem;
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.btn {
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

@media (max-width: 768px) {
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }

    .timeline {
        padding-left: 20px;
    }

    .timeline-marker {
        left: -15px;
        width: 24px;
        height: 24px;
        font-size: 10px;
    }
}
</style>

<!-- Confetti Canvas -->
<canvas id="confetti-canvas"></canvas>

<script>
// Confetti Animation
function createConfetti() {
    const canvas = document.getElementById('confetti-canvas');
    if (!canvas) return;
    
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
    canvas.style.position = 'fixed';
    canvas.style.top = '0';
    canvas.style.left = '0';
    canvas.style.pointerEvents = 'none';
    canvas.style.zIndex = '9999';
    
    const ctx = canvas.getContext('2d');
    const confetti = [];
    const colors = ['#2e7d32', '#4caf50', '#28a745', '#ff6f00', '#ff9800', '#f44336', '#2196f3'];
    
    // Create confetti particles
    for (let i = 0; i < 150; i++) {
        confetti.push({
            x: Math.random() * canvas.width,
            y: Math.random() * canvas.height - canvas.height,
            r: Math.random() * 6 + 4,
            d: Math.random() * confetti.length,
            color: colors[Math.floor(Math.random() * colors.length)],
            tilt: Math.floor(Math.random() * 10) - 10,
            tiltAngleIncrement: Math.random() * 0.07 + 0.05,
            tiltAngle: 0
        });
    }
    
    function drawConfetti() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        confetti.forEach((c, index) => {
            ctx.beginPath();
            ctx.lineWidth = c.r / 2;
            ctx.strokeStyle = c.color;
            ctx.moveTo(c.x + c.tilt + c.r, c.y);
            ctx.lineTo(c.x + c.tilt, c.y + c.tilt + c.r);
            ctx.stroke();
            
            c.tiltAngle += c.tiltAngleIncrement;
            c.y += (Math.cos(c.d) + 3 + c.r / 2) / 2;
            c.tilt = Math.sin(c.tiltAngle - c.r) * c.r * 2;
            
            if (c.y > canvas.height) {
                confetti[index] = {
                    x: Math.random() * canvas.width,
                    y: -20,
                    r: c.r,
                    d: c.d,
                    color: c.color,
                    tilt: Math.floor(Math.random() * 10) - 10,
                    tiltAngleIncrement: c.tiltAngleIncrement,
                    tiltAngle: c.tiltAngle
                };
            }
        });
        
        requestAnimationFrame(drawConfetti);
    }
    
    drawConfetti();
    
    // Stop confetti after 5 seconds
    setTimeout(() => {
        canvas.style.opacity = '0';
        setTimeout(() => {
            canvas.remove();
        }, 1000);
    }, 5000);
}

// Page load animations
document.addEventListener('DOMContentLoaded', function() {
    // Start confetti animation
    createConfetti();

    // Add celebration animation to success icon
    const successIcon = document.querySelector('.success-icon');
    if (successIcon) {
        setTimeout(() => {
            successIcon.style.animation = 'none';
            setTimeout(() => {
                successIcon.style.animation = 'checkmark 0.8s ease-in-out';
            }, 100);
        }, 2000);
    }
    
    // Animate order number alert
    const orderAlert = document.querySelector('.alert-success');
    if (orderAlert) {
        orderAlert.style.opacity = '0';
        orderAlert.style.transform = 'translateY(-20px)';
        setTimeout(() => {
            orderAlert.style.transition = 'all 0.5s ease';
            orderAlert.style.opacity = '1';
            orderAlert.style.transform = 'translateY(0)';
        }, 300);
    }
});
</script>

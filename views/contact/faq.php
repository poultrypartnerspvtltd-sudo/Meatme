<!-- FAQ Page -->
<div class="container py-5">
    
    <!-- Page Header -->
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold text-success mb-3">Frequently Asked Questions</h1>
        <p class="lead text-muted">Find answers to common questions about our products and services.</p>
    </div>
    
    <!-- FAQ Accordion -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="accordion" id="faqAccordion">
                
                <?php foreach ($faqs as $index => $faq): ?>
                    <div class="accordion-item mb-3 border-0 shadow-sm">
                        <h2 class="accordion-header" id="heading<?= e($index) ?>">
                            <button class="accordion-button <?= e($index !== 0 ? 'collapsed' : '') ?> fw-bold" 
                                    type="button" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#collapse<?= e($index) ?>" 
                                    aria-expanded="<?= e($index === 0 ? 'true' : 'false') ?>" 
                                    aria-controls="collapse<?= e($index) ?>">
                                <i class="fas fa-question-circle text-success me-3"></i>
                                <?= htmlspecialchars($faq['question']) ?>
                            </button>
                        </h2>
                        <div id="collapse<?= e($index) ?>" 
                             class="accordion-collapse collapse <?= e($index === 0 ? 'show' : '') ?>" 
                             aria-labelledby="heading<?= e($index) ?>" 
                             data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <p class="mb-0"><?= nl2br(htmlspecialchars($faq['answer'])) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                
            </div>
        </div>
    </div>
    
    <!-- Contact CTA -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card bg-success text-white text-center">
                <div class="card-body py-5">
                    <h3 class="fw-bold mb-3">Still Have Questions?</h3>
                    <p class="lead mb-4">Can't find what you're looking for? We're here to help!</p>
                    <div class="row justify-content-center">
                        <div class="col-auto me-3">
                            <a href="<?= e(\App\Core\View::url('contact')) ?>" class="btn btn-light btn-lg">
                                <i class="fas fa-envelope me-2"></i>Contact Us
                            </a>
                        </div>
                        <div class="col-auto">
                            <a href="tel:+9779821908585" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-phone me-2"></i>Call Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>

/**
 * MeatMe - Main JavaScript Application
 */

// Global App Object
window.MeatMe = {
    config: {
        baseUrl: window.location.origin + '/Meatme',
        csrfToken: document.querySelector('#csrf-token-form input[name="csrf_token"]')?.value || '',
        currency: 'Rs.',
        currencyPosition: 'before'
    },
    
    // Initialize the application
    init: function() {
        this.setupEventListeners();
        this.initializeComponents();
        this.loadSavedPreferences();
    },
    
    // Setup global event listeners
    setupEventListeners: function() {
        // Cart functionality
        document.addEventListener('click', this.handleCartActions.bind(this));
        
        // Search functionality
        const searchInput = document.querySelector('input[name="q"]');
        if (searchInput) {
            searchInput.addEventListener('input', this.debounce(this.handleSearch.bind(this), 300));
        }
        
        // Quantity controls
        document.addEventListener('click', this.handleQuantityControls.bind(this));
        
        // Form submissions
        document.addEventListener('submit', this.handleFormSubmissions.bind(this));
        
        // Image lazy loading
        this.setupLazyLoading();
        
        // Smooth scrolling for anchor links
        this.setupSmoothScrolling();
    },
    
    // Initialize components
    initializeComponents: function() {
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-mdb-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new mdb.Tooltip(tooltipTriggerEl);
        });
        
        // Initialize modals
        const modalList = [].slice.call(document.querySelectorAll('.modal'));
        modalList.map(function (modalEl) {
            return new mdb.Modal(modalEl);
        });
        
        // Initialize carousel
        const carouselList = [].slice.call(document.querySelectorAll('.carousel'));
        carouselList.map(function (carouselEl) {
            return new mdb.Carousel(carouselEl);
        });
    },
    
    // Load saved user preferences
    loadSavedPreferences: function() {
        // Load other preferences (theme is handled in layout)
        const savedCurrency = localStorage.getItem('currency');
        if (savedCurrency) {
            this.config.currency = savedCurrency;
        }
    },
    
    // Handle cart actions (add, remove, update)
    handleCartActions: function(e) {
        const target = e.target.closest('[data-action]');
        if (!target) return;
        
        const action = target.getAttribute('data-action');
        const productId = target.getAttribute('data-product-id');
        
        switch (action) {
            case 'remove-from-cart':
                e.preventDefault();
                this.removeFromCart(productId);
                break;
            case 'update-cart':
                e.preventDefault();
                this.updateCart(productId, target);
                break;
            case 'add-to-wishlist':
                e.preventDefault();
                this.addToWishlist(productId);
                break;
            case 'remove-from-wishlist':
                e.preventDefault();
                this.removeFromWishlist(productId);
                break;
        }
    },
    
    // Add to Cart removed from global handlers
    
    // Remove product from cart
    removeFromCart: function(productId) {
        this.makeRequest('POST', '/cart/remove', {
            product_id: productId
        })
        .then(response => {
            if (response.success) {
                this.updateCartCount(response.cartCount);
                this.showToast('success', response.message);
                
                // Remove cart item from DOM
                const cartItem = document.querySelector(`[data-cart-item="${productId}"]`);
                if (cartItem) {
                    cartItem.remove();
                }
                
                // Update cart totals
                this.updateCartTotals();
            } else {
                throw new Error(response.message);
            }
        })
        .catch(error => {
            this.showToast('error', error.message || 'Failed to remove from cart');
        });
    },
    
    // Update cart item quantity
    updateCart: function(productId, input) {
        const quantity = input.value;
        
        this.makeRequest('POST', '/cart/update', {
            product_id: productId,
            quantity: quantity
        })
        .then(response => {
            if (response.success) {
                this.updateCartCount(response.cartCount);
                this.updateCartTotals();
            } else {
                throw new Error(response.message);
            }
        })
        .catch(error => {
            this.showToast('error', error.message || 'Failed to update cart');
        });
    },
    
    // Add to wishlist
    addToWishlist: function(productId) {
        this.makeRequest('POST', '/wishlist/add', {
            product_id: productId
        })
        .then(response => {
            if (response.success) {
                this.showToast('success', response.message);
                
                // Update wishlist button
                const button = document.querySelector(`[data-action="add-to-wishlist"][data-product-id="${productId}"]`);
                if (button) {
                    button.innerHTML = '<i class="fas fa-heart text-danger"></i>';
                    button.setAttribute('data-action', 'remove-from-wishlist');
                }
            } else {
                throw new Error(response.message);
            }
        })
        .catch(error => {
            this.showToast('error', error.message || 'Failed to add to wishlist');
        });
    },
    
    // Remove from wishlist
    removeFromWishlist: function(productId) {
        this.makeRequest('POST', '/wishlist/remove', {
            product_id: productId
        })
        .then(response => {
            if (response.success) {
                this.showToast('success', response.message);
                
                // Update wishlist button
                const button = document.querySelector(`[data-action="remove-from-wishlist"][data-product-id="${productId}"]`);
                if (button) {
                    button.innerHTML = '<i class="far fa-heart"></i>';
                    button.setAttribute('data-action', 'add-to-wishlist');
                }
            } else {
                throw new Error(response.message);
            }
        })
        .catch(error => {
            this.showToast('error', error.message || 'Failed to remove from wishlist');
        });
    },
    
    // Handle search with suggestions
    handleSearch: function(e) {
        const query = e.target.value.trim();
        
        if (query.length < 2) {
            this.hideSuggestions();
            return;
        }
        
        this.makeRequest('GET', `/api/products/suggestions?q=${encodeURIComponent(query)}`)
        .then(response => {
            if (response.success && response.suggestions.length > 0) {
                this.showSuggestions(response.suggestions, e.target);
            } else {
                this.hideSuggestions();
            }
        })
        .catch(error => {
            console.error('Search error:', error);
            this.hideSuggestions();
        });
    },
    
    // Show search suggestions
    showSuggestions: function(suggestions, input) {
        let suggestionsContainer = document.querySelector('.search-suggestions');
        
        if (!suggestionsContainer) {
            suggestionsContainer = document.createElement('div');
            suggestionsContainer.className = 'search-suggestions';
            input.parentNode.style.position = 'relative';
            input.parentNode.appendChild(suggestionsContainer);
        }
        
        suggestionsContainer.innerHTML = suggestions.map(item => `
            <div class="search-suggestion-item" data-url="${item.url}">
                <div class="d-flex align-items-center">
                    <img src="${item.image}" alt="${item.name}" class="me-2" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                    <div>
                        <div class="fw-bold">${item.name}</div>
                        <small class="text-muted">${this.formatPrice(item.price)}</small>
                    </div>
                </div>
            </div>
        `).join('');
        
        // Add click handlers
        suggestionsContainer.addEventListener('click', (e) => {
            const item = e.target.closest('.search-suggestion-item');
            if (item) {
                window.location.href = item.getAttribute('data-url');
            }
        });
        
        suggestionsContainer.style.display = 'block';
    },
    
    // Hide search suggestions
    hideSuggestions: function() {
        const suggestionsContainer = document.querySelector('.search-suggestions');
        if (suggestionsContainer) {
            suggestionsContainer.style.display = 'none';
        }
    },
    
    // Handle quantity controls (+ and - buttons)
    handleQuantityControls: function(e) {
        const target = e.target.closest('[data-quantity-action]');
        if (!target) return;
        
        e.preventDefault();
        
        const action = target.getAttribute('data-quantity-action');
        const input = target.parentNode.querySelector('input[type="number"]');
        
        if (!input) return;
        
        const currentValue = parseFloat(input.value) || 0;
        const step = parseFloat(input.step) || 1;
        const min = parseFloat(input.min) || 0;
        const max = parseFloat(input.max) || Infinity;
        
        let newValue = currentValue;
        
        if (action === 'increase' && newValue < max) {
            newValue += step;
        } else if (action === 'decrease' && newValue > min) {
            newValue -= step;
        }
        
        input.value = newValue;
        
        // Trigger change event
        input.dispatchEvent(new Event('change'));
    },
    
    // Handle form submissions
    handleFormSubmissions: function(e) {
        const form = e.target;
        
        if (form.hasAttribute('data-ajax')) {
            e.preventDefault();
            this.submitFormAjax(form);
        }
    },
    
    // Submit form via AJAX
    submitFormAjax: function(form) {
        const formData = new FormData(form);
        const method = form.method || 'POST';
        const action = form.action;
        
        // Show loading state
        const submitButton = form.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        }
        
        fetch(action, {
            method: method,
            body: formData,
            headers: {
                'X-CSRF-TOKEN': this.config.csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showToast('success', data.message);
                
                if (data.redirect) {
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1000);
                }
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            this.showToast('error', error.message || 'An error occurred');
        })
        .finally(() => {
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.innerHTML = submitButton.getAttribute('data-original-text') || 'Submit';
            }
        });
    },
    
    // Setup lazy loading for images
    setupLazyLoading: function() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });
            
            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    },
    
    // Setup smooth scrolling
    setupSmoothScrolling: function() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    },
    
    // Update cart count in UI
    updateCartCount: function(count) {
        const cartCountElements = document.querySelectorAll('#cart-count, [data-cart-count]');
        cartCountElements.forEach(element => {
            element.textContent = count;
            element.style.display = count > 0 ? 'inline' : 'none';
        });
    },
    
    // Update cart totals
    updateCartTotals: function() {
        // This would be implemented based on your cart page structure
        // For now, we'll just reload the cart section
        const cartSection = document.querySelector('[data-cart-totals]');
        if (cartSection) {
            // Reload cart totals via AJAX
            this.makeRequest('GET', '/api/cart/totals')
            .then(response => {
                if (response.success) {
                    cartSection.innerHTML = response.html;
                }
            });
        }
    },
    
    // Show toast notification
    showToast: function(type, message, duration = 3000) {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 400px;';
        toast.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-mdb-dismiss="alert"></button>
        `;
        
        document.body.appendChild(toast);
        
        // Auto remove
        setTimeout(() => {
            if (toast.parentNode) {
                toast.classList.remove('show');
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 150);
            }
        }, duration);
        
        // Manual close
        toast.querySelector('.btn-close').addEventListener('click', () => {
            toast.classList.remove('show');
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 150);
        });
    },
    
    // Make AJAX request
    makeRequest: function(method, url, data = null) {
        if (!this.config.csrfToken) {
            const fallbackInput = document.querySelector('#csrf-token-form input[name="csrf_token"]') ||
                document.querySelector('form input[name="csrf_token"]');
            if (fallbackInput) {
                this.config.csrfToken = fallbackInput.value;
            }
        }

        const headers = {
            'Content-Type': 'application/x-www-form-urlencoded'
        };

        if (this.config.csrfToken) {
            headers['X-CSRF-TOKEN'] = this.config.csrfToken;
        }

        const options = {
            method: method,
            headers: headers
        };
        
        if (data && method !== 'GET') {
            if (data instanceof FormData) {
                if (this.config.csrfToken && !data.has('csrf_token')) {
                    data.append('csrf_token', this.config.csrfToken);
                }
                options.body = data;
                delete options.headers['Content-Type'];
            } else {
                const params = new URLSearchParams();
                if (this.config.csrfToken) {
                    params.append('csrf_token', this.config.csrfToken);
                }
                
                Object.keys(data).forEach(key => {
                    params.append(key, data[key]);
                });
                
                options.body = params;
            }
        }
        
        return fetch(this.config.baseUrl + url, options)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const refreshedToken = response.headers.get('X-CSRF-TOKEN');
                if (refreshedToken) {
                    this.config.csrfToken = refreshedToken;
                    window.csrfToken = refreshedToken;
                    const globalInput = document.querySelector('#csrf-token-form input[name="csrf_token"]');
                    if (globalInput) {
                        globalInput.value = refreshedToken;
                    }
                }

                return response.json();
            });
    },
    
    // Format price
    formatPrice: function(amount) {
        const formatted = parseFloat(amount).toFixed(2);
        return this.config.currencyPosition === 'before' 
            ? `${this.config.currency} ${formatted}`
            : `${formatted} ${this.config.currency}`;
    },
    
    // Update theme icon
    updateThemeIcon: function(theme) {
        const icon = document.getElementById('theme-icon');
        if (icon) {
            icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        }
    },
    
    // Throttle function
    throttle: function(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    },
    
    // Debounce function
    debounce: function(func, wait) {
        let timeout;
        return function() {
            const context = this;
            const args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), wait);
        };
    }
};

// Initialize app when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    MeatMe.init();
});

// Handle page visibility changes
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        // Page became visible, refresh cart count
        MeatMe.makeRequest('GET', '/api/cart/count')
        .then(response => {
            if (response.success) {
                MeatMe.updateCartCount(response.count);
            }
        })
        .catch(error => {
            console.error('Failed to refresh cart count:', error);
        });
    }
});

// Handle online/offline status
window.addEventListener('online', function() {
    MeatMe.showToast('success', 'Connection restored');
});

window.addEventListener('offline', function() {
    MeatMe.showToast('warning', 'You are offline. Some features may not work.');
});

// Export for use in other scripts
window.MeatMe = MeatMe;

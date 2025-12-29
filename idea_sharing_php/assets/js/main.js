/**
 * Main JavaScript File for IdeaConnect Platform
 * Handles common functionality across the application
 */

(function() {
    'use strict';

    /**
     * Initialize tooltips
     */
    function initTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    /**
     * Auto-hide alerts after 5 seconds
     */
    function autoHideAlerts() {
        const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });
    }

    /**
     * Confirm delete actions
     */
    function confirmDelete() {
        const deleteLinks = document.querySelectorAll('a[href*="delete"]');
        deleteLinks.forEach(function(link) {
            if (!link.hasAttribute('onclick')) {
                link.addEventListener('click', function(e) {
                    if (!confirm('Are you sure you want to delete this item?')) {
                        e.preventDefault();
                    }
                });
            }
        });
    }

    /**
     * Character counter for textareas
     */
    function initCharacterCounter() {
        const textareas = document.querySelectorAll('textarea[data-max-length]');
        textareas.forEach(function(textarea) {
            const maxLength = parseInt(textarea.getAttribute('data-max-length'));
            const counter = document.createElement('small');
            counter.className = 'text-muted d-block mt-1';
            textarea.parentNode.appendChild(counter);

            function updateCounter() {
                const remaining = maxLength - textarea.value.length;
                counter.textContent = remaining + ' characters remaining';
                
                if (remaining < 0) {
                    counter.classList.add('text-danger');
                    counter.classList.remove('text-muted');
                } else {
                    counter.classList.add('text-muted');
                    counter.classList.remove('text-danger');
                }
            }

            textarea.addEventListener('input', updateCounter);
            updateCounter();
        });
    }

    /**
     * Image preview for file inputs
     */
    function initImagePreview() {
        const fileInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
        fileInputs.forEach(function(input) {
            input.addEventListener('change', function(e) {
                const files = e.target.files;
                if (files.length > 0) {
                    const previewContainer = document.createElement('div');
                    previewContainer.className = 'image-preview-container mt-3 row';
                    
                    // Remove existing preview
                    const existingPreview = input.parentNode.querySelector('.image-preview-container');
                    if (existingPreview) {
                        existingPreview.remove();
                    }

                    for (let i = 0; i < Math.min(files.length, 5); i++) {
                        const file = files[i];
                        const reader = new FileReader();

                        reader.onload = function(event) {
                            const col = document.createElement('div');
                            col.className = 'col-md-3 mb-2';
                            
                            const img = document.createElement('img');
                            img.src = event.target.result;
                            img.className = 'img-thumbnail';
                            img.alt = 'Preview';
                            
                            col.appendChild(img);
                            previewContainer.appendChild(col);
                        };

                        reader.readAsDataURL(file);
                    }

                    input.parentNode.appendChild(previewContainer);
                }
            });
        });
    }

    /**
     * Smooth scroll to anchor links
     */
    function initSmoothScroll() {
        const anchorLinks = document.querySelectorAll('a[href^="#"]');
        anchorLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href !== '#' && href.length > 1) {
                    const target = document.querySelector(href);
                    if (target) {
                        e.preventDefault();
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }
            });
        });
    }

    /**
     * Form validation enhancement
     */
    function enhanceFormValidation() {
        const forms = document.querySelectorAll('form[data-validate]');
        forms.forEach(function(form) {
            form.addEventListener('submit', function(e) {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                form.classList.add('was-validated');
            });
        });
    }

    /**
     * Initialize on DOM ready
     */
    document.addEventListener('DOMContentLoaded', function() {
        initTooltips();
        autoHideAlerts();
        confirmDelete();
        initCharacterCounter();
        initImagePreview();
        initSmoothScroll();
        enhanceFormValidation();
    });

    /**
     * Utility function to show toast notification
     */
    window.showToast = function(message, type = 'info') {
        const toastContainer = document.getElementById('toastContainer') || createToastContainer();
        
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        toastContainer.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        toast.addEventListener('hidden.bs.toast', function() {
            toast.remove();
        });
    };

    /**
     * Create toast container if it doesn't exist
     */
    function createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(container);
        return container;
    }

    /**
     * AJAX helper function
     */
    window.ajaxRequest = function(url, method = 'GET', data = null) {
        return new Promise(function(resolve, reject) {
            const xhr = new XMLHttpRequest();
            xhr.open(method, url, true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            
            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 300) {
                    try {
                        resolve(JSON.parse(xhr.responseText));
                    } catch (e) {
                        resolve(xhr.responseText);
                    }
                } else {
                    reject(new Error(xhr.statusText));
                }
            };
            
            xhr.onerror = function() {
                reject(new Error('Network error'));
            };
            
            xhr.send(data ? JSON.stringify(data) : null);
        });
    };

})();

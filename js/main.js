// Main javascript file

document.addEventListener('DOMContentLoaded', function() {
    console.log('main.js loaded and DOMContentLoaded event fired.');

    // Quick view modal
    // Removed custom modal handling as Bootstrap modal will be used.

    document.querySelectorAll('.quick-view-button').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.id; // Use data-id as set in index.php
            console.log('Quick View button clicked. Product ID:', productId);
            
            // Clear previous content and show loading spinner if desired
            $('#exampleModal .modal-body').html('<div class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>');
            $('#exampleModal').modal('show');

            fetch(`quick_view.php?id=${productId}`)
                .then(response => response.text())
                .then(html => {
                    console.log('Received HTML for product ID', productId, ':', html);
                    $('#exampleModal .modal-body').html(html);
                    
                    // Re-initialize Owl Carousel for the quick view slider
                    if ($.fn.owlCarousel) { // Check if Owl Carousel is loaded
                        $('.quickview-slider-active').owlCarousel({
                            items:1,
                            autoplay:true,
                            autoplayTimeout:5000,
                            smartSpeed: 400,
                            autoplayHoverPause:true,
                            nav:true,
                            loop:true,
                            merge:true,
                            dots:false,
                            navText: ['<i class=" ti-arrow-left"></i>', '<i class=" ti-arrow-right"></i>'],
                        });
                    } else {
                        console.warn('Owl Carousel library not loaded. Quick view slider might not function.');
                    }
                })
                .catch(error => {
                    console.error('Error fetching quick view content:', error);
                    $('#exampleModal .modal-body').html('<p class="text-danger">Failed to load product details.</p>');
                });
        });
    });

    // The close button and window click listeners are handled by Bootstrap's modal functionality.
});

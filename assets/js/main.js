document.addEventListener('DOMContentLoaded', function() {
    var alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            var closeBtn = alert.querySelector('.btn-close');
            if (closeBtn) {
                closeBtn.click();
            }
        }, 5000);
    });

    var quickChips = document.querySelectorAll('.quick-destination-chip');
    quickChips.forEach(function(chip) {
        chip.addEventListener('click', function(e) {
            var dest = this.getAttribute('data-destination');
            var searchInput = document.getElementById('home-search-input');
            if (searchInput && dest) {
                e.preventDefault();
                searchInput.value = dest;
                var form = searchInput.closest('form');
                if (form) {
                    form.submit();
                }
            }
        });
    });

    var imageInput = document.getElementById('parking-image-input');
    var imagePreview = document.getElementById('image-preview');
    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', function() {
            var file = this.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.classList.remove('d-none');
                    var placeholder = document.getElementById('image-preview-placeholder');
                    if (placeholder) {
                        placeholder.classList.add('d-none');
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }
});

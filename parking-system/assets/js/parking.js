document.addEventListener('DOMContentLoaded', function() {
    var paymentFilter = document.getElementById('filter-payment');
    var vehicleFilter = document.getElementById('filter-vehicle');
    var sortFilter = document.getElementById('filter-sort');
    var searchInput = document.getElementById('filter-keyword');
    var countBadge = document.getElementById('results-count-badge');
    var emptyState = document.getElementById('empty-results-state');

    function applyClientFilters() {
        var pVal = paymentFilter ? paymentFilter.value : 'all';
        var vVal = vehicleFilter ? vehicleFilter.value : 'all';
        var sVal = sortFilter ? sortFilter.value : 'nearest';
        var qVal = searchInput ? searchInput.value.toLowerCase().trim() : '';

        var cards = Array.from(document.querySelectorAll('.parking-card-item'));
        var visibleCount = 0;
        var visibleFacilities = [];

        cards.forEach(function(card) {
            var cPayment = card.getAttribute('data-payment');
            var cVehicle = card.getAttribute('data-vehicle');
            var cName = (card.getAttribute('data-name') || '').toLowerCase();
            var cAddress = (card.getAttribute('data-address') || '').toLowerCase();
            var cArea = (card.getAttribute('data-area') || '').toLowerCase();

            var matchesPayment = (pVal === 'all') || (cPayment === pVal);
            var matchesVehicle = (vVal === 'all') || (cVehicle === vVal) || (cVehicle === 'both' && (vVal === 'car' || vVal === 'motorcycle'));
            var matchesSearch = (!qVal) || (cName.indexOf(qVal) !== -1) || (cAddress.indexOf(qVal) !== -1) || (cArea.indexOf(qVal) !== -1);

            if (matchesPayment && matchesVehicle && matchesSearch) {
                card.style.display = '';
                visibleCount++;
                try {
                    var rawData = card.getAttribute('data-json');
                    if (rawData) {
                        visibleFacilities.push(JSON.parse(rawData));
                    }
                } catch(e) {}
            } else {
                card.style.display = 'none';
            }
        });

        if (countBadge) {
            countBadge.textContent = visibleCount;
        }

        if (emptyState) {
            if (visibleCount === 0) {
                emptyState.classList.remove('d-none');
            } else {
                emptyState.classList.add('d-none');
            }
        }

        if (typeof renderParkingMarkers === 'function' && typeof window.appBaseUrl !== 'undefined') {
            renderParkingMarkers(visibleFacilities, window.appBaseUrl);
        }
    }

    if (paymentFilter) paymentFilter.addEventListener('change', applyClientFilters);
    if (vehicleFilter) vehicleFilter.addEventListener('change', applyClientFilters);
    if (searchInput) searchInput.addEventListener('input', applyClientFilters);

    if (sortFilter) {
        sortFilter.addEventListener('change', function() {
            var form = document.getElementById('search-filter-form');
            if (form) {
                form.submit();
            }
        });
    }

    var cardElements = document.querySelectorAll('.parking-card');
    cardElements.forEach(function(card) {
        card.addEventListener('click', function(e) {
            if (e.target.tagName === 'A' || e.target.closest('a')) {
                return;
            }
            var id = this.getAttribute('data-id');
            if (id && typeof focusParkingMarker === 'function') {
                focusParkingMarker(id);
                highlightCard(id);
            }
        });
    });
});

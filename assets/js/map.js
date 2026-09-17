var parkingMapInstance = null;
var parkingMarkersGroup = null;
var destinationMarkerInstance = null;
var markerMapById = {};

function createCustomPin(color, iconClass, label) {
    var html = '<div style="background-color:' + color + ';width:34px;height:34px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);display:flex;align-items:center;justify-content:center;box-shadow:0 3px 6px rgba(0,0,0,0.3);border:2px solid #ffffff;">' +
               '<div style="transform:rotate(45deg);color:#ffffff;font-size:14px;"><i class="bi ' + iconClass + '"></i></div>' +
               '</div>';
    return L.divIcon({
        className: 'custom-leaflet-marker',
        html: html,
        iconSize: [34, 34],
        iconAnchor: [17, 34],
        popupAnchor: [0, -30]
    });
}

function initFinderMap(destData, parkingFacilities, baseUrl) {
    var mapElement = document.getElementById('finder-map');
    if (!mapElement) return;

    var initialLat = destData ? destData.latitude : 27.7172;
    var initialLng = destData ? destData.longitude : 85.3240;
    var initialZoom = destData ? 15 : 13;

    parkingMapInstance = L.map('finder-map', {
        scrollWheelZoom: false
    }).setView([initialLat, initialLng], initialZoom);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(parkingMapInstance);

    parkingMarkersGroup = L.featureGroup().addTo(parkingMapInstance);
    markerMapById = {};

    if (destData && destData.latitude && destData.longitude) {
        var destIcon = createCustomPin('#dc2626', 'bi-geo-alt-fill', 'Destination');
        destinationMarkerInstance = L.marker([destData.latitude, destData.longitude], {
            icon: destIcon,
            zIndexOffset: 1000
        }).addTo(parkingMapInstance);

        destinationMarkerInstance.bindPopup(
            '<div class="text-center p-1">' +
            '<span class="badge bg-danger mb-1">Your Destination</span>' +
            '<div class="fw-bold fs-6">' + destData.name + '</div>' +
            '</div>'
        );
    }

    renderParkingMarkers(parkingFacilities, baseUrl);

    if (destData) {
        var bounds = [];
        bounds.push([destData.latitude, destData.longitude]);
        parkingFacilities.forEach(function(item) {
            bounds.push([parseFloat(item.latitude), parseFloat(item.longitude)]);
        });
        if (bounds.length > 1) {
            parkingMapInstance.fitBounds(bounds, { padding: [40, 40], maxZoom: 16 });
        }
    }
}

function renderParkingMarkers(facilities, baseUrl) {
    if (!parkingMapInstance || !parkingMarkersGroup) return;

    parkingMarkersGroup.clearLayers();
    markerMapById = {};

    facilities.forEach(function(facility) {
        var isFree = facility.payment_type === 'free';
        var pinColor = isFree ? '#059669' : '#2563eb';
        var pinIconClass = facility.vehicle_type === 'motorcycle' ? 'bi-bicycle' : 'bi-p-square-fill';

        var pin = createCustomPin(pinColor, pinIconClass, facility.name);
        var marker = L.marker([parseFloat(facility.latitude), parseFloat(facility.longitude)], {
            icon: pin
        });

        var badgeHtml = isFree ?
            '<span class="badge bg-success-subtle text-success border border-success-subtle me-1">Free Parking</span>' :
            '<span class="badge bg-primary-subtle text-primary border border-primary-subtle me-1">Paid</span>';

        var feeText = facility.parking_fee ? facility.parking_fee : (isFree ? 'Free' : 'Standard rates');

        var popupContent = 
            '<div class="p-1" style="min-width:210px;">' +
            '<div class="d-flex justify-content-between align-items-center mb-1">' +
                badgeHtml +
                (facility.distance_formatted ? '<span class="badge bg-light text-dark border">' + facility.distance_formatted + '</span>' : '') +
            '</div>' +
            '<div class="fw-bold text-dark fs-6 mb-1">' + facility.name + '</div>' +
            '<div class="text-muted small mb-2"><i class="bi bi-geo-alt me-1"></i>' + facility.address + '</div>' +
            '<div class="small fw-semibold text-secondary mb-2"><i class="bi bi-cash me-1"></i>' + feeText + '</div>' +
            '<div class="d-grid gap-1">' +
                '<a href="' + baseUrl + '/parking-details.php?id=' + facility.id + '" class="btn btn-primary btn-sm py-1">View Details</a>' +
            '</div>' +
            '</div>';

        marker.bindPopup(popupContent);

        marker.on('click', function() {
            highlightCard(facility.id);
        });

        parkingMarkersGroup.addLayer(marker);
        markerMapById[facility.id] = marker;
    });
}

function focusParkingMarker(facilityId) {
    if (!parkingMapInstance || !markerMapById[facilityId]) return;
    var marker = markerMapById[facilityId];
    parkingMapInstance.setView(marker.getLatLng(), 16, { animate: true });
    marker.openPopup();
}

function highlightCard(facilityId) {
    var cards = document.querySelectorAll('.parking-card');
    cards.forEach(function(card) {
        card.classList.remove('is-selected');
    });

    var targetCard = document.getElementById('parking-card-' + facilityId);
    if (targetCard) {
        targetCard.classList.add('is-selected');
        targetCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}

function initDetailMap(lat, lng, title, address) {
    var mapEl = document.getElementById('detail-map');
    if (!mapEl) return;

    var map = L.map('detail-map', {
        scrollWheelZoom: false
    }).setView([lat, lng], 16);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var pin = createCustomPin('#2563eb', 'bi-p-square-fill', title);
    var marker = L.marker([lat, lng], { icon: pin }).addTo(map);

    marker.bindPopup(
        '<div class="p-1">' +
        '<div class="fw-bold">' + title + '</div>' +
        '<div class="text-muted small">' + address + '</div>' +
        '</div>'
    ).openPopup();
}

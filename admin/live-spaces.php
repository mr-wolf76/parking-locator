<?php
/**
 * Admin Real-Time Parking Spaces Monitor & Gate Simulator
 * Project: Parking Information and Locator System (Kathmandu)
 * 
 * Allows parking operators, attendants, and evaluators to monitor live occupancy,
 * manually record vehicle entries/exits, and simulate IoT gate sensors.
 */

$adminTitle = "Real-Time Parking Spaces Monitor";
require_once __DIR__ . '/admin_header.php';

$facilities = getAllParkingForAdmin();
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-broadcast text-success me-2"></i>Real-Time Parking Spaces Monitor
        </h4>
        <div class="text-muted small">
            Live gate occupancy tracking across all registered Kathmandu parking locations.
        </div>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <button type="button" class="btn btn-outline-success" id="btn-simulate-once">
            <i class="bi bi-shuffle me-1"></i> Simulate 1 Gate Event
        </button>
        <button type="button" class="btn btn-success" id="btn-simulate-auto">
            <i class="bi bi-play-circle-fill me-1"></i> <span id="auto-sim-text">Start Auto-Traffic Simulation</span>
        </button>
        <a href="<?php echo $baseUrl; ?>/find-parking.php" target="_blank" class="btn btn-outline-primary" title="Open public view in a new tab">
            <i class="bi bi-box-arrow-up-right me-1"></i> View Public Map
        </a>
    </div>
</div>

<!-- Teacher Viva Explanation Notice -->
<div class="alert alert-info alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
    <i class="bi bi-info-circle-fill fs-5"></i>
    <div class="small">
        <strong>Academic Demonstration Feature:</strong> This page acts as the central IoT sensor and gate attendant hub. You can click <strong>"Simulate 1 Gate Event"</strong> or record vehicle entries/exits manually. Notice how space counts and occupancy bars update here and on the public map simultaneously via AJAX polling.
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<!-- City Overview Stat Counters -->
<div class="row g-3 mb-4" id="city-overview-counters">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small mb-1">Total System Capacity</div>
                    <div class="h3 fw-bold text-dark mb-0" id="stat-total-cap">--</div>
                    <div class="text-muted small mt-1">Across 15 facilities</div>
                </div>
                <div class="stat-icon-wrapper bg-primary-subtle text-primary">
                    <i class="bi bi-p-circle-fill"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small mb-1">Available Spaces Now</div>
                    <div class="h3 fw-bold text-success mb-0" id="stat-avail-spaces">--</div>
                    <div class="text-muted small mt-1"><span class="live-pulse-dot me-1"></span>Ready for parking</div>
                </div>
                <div class="stat-icon-wrapper bg-success-subtle text-success">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small mb-1">Occupied Spaces</div>
                    <div class="h3 fw-bold text-danger mb-0" id="stat-occupied-spaces">--</div>
                    <div class="text-muted small mt-1">Currently parked</div>
                </div>
                <div class="stat-icon-wrapper bg-danger-subtle text-danger">
                    <i class="bi bi-car-front-fill"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small mb-1">City-Wide Occupancy</div>
                    <div class="h3 fw-bold text-dark mb-0" id="stat-occupancy-rate">--%</div>
                    <div class="text-muted small mt-1">Average load</div>
                </div>
                <div class="stat-icon-wrapper bg-warning-subtle text-warning">
                    <i class="bi bi-pie-chart-fill"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Facilities Real-Time Grid -->
<div class="row g-3 mb-4" id="realtime-facilities-grid">
    <?php foreach ($facilities as $facility): ?>
        <?php $live = formatRealtimeSpacesData($facility); ?>
        <div class="col-lg-6 col-xl-4" id="attendant-col-<?php echo $facility['id']; ?>">
            <div class="card h-100 border rounded-3 p-3 bg-white attendant-card shadow-xs" id="attendant-card-<?php echo $facility['id']; ?>">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="badge bg-light text-muted border mb-1"><?php echo htmlspecialchars($facility['area']); ?></span>
                        <h6 class="fw-bold text-dark mb-0 text-truncate" style="max-width: 200px;" title="<?php echo htmlspecialchars($facility['name']); ?>">
                            <?php echo htmlspecialchars($facility['name']); ?>
                        </h6>
                    </div>
                    <span class="badge <?php echo $live['badge_class']; ?>" id="admin-badge-<?php echo $facility['id']; ?>">
                        <?php echo $live['status_text']; ?>
                    </span>
                </div>

                <!-- Occupancy Bar -->
                <div class="my-2">
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">Free Spaces</span>
                        <span class="fw-bold text-dark">
                            <span class="<?php echo $live['text_color']; ?>" id="admin-avail-<?php echo $facility['id']; ?>"><?php echo $live['available_spaces']; ?></span> / <?php echo $live['total_capacity']; ?>
                        </span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar <?php echo ($live['status_level'] === 'full' ? 'bg-danger' : ($live['status_level'] === 'limited' ? 'bg-warning' : 'bg-success')); ?>" 
                             id="admin-progress-<?php echo $facility['id']; ?>"
                             role="progressbar" 
                             style="width: <?php echo $live['occupancy_percent']; ?>%;" 
                             aria-valuenow="<?php echo $live['occupancy_percent']; ?>" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                </div>

                <!-- Sub-capacities -->
                <div class="row g-1 text-center small text-muted my-2 p-1 bg-light rounded border">
                    <div class="col-6 border-end">
                        <i class="bi bi-bicycle me-1"></i>Bike: <strong class="text-dark" id="admin-bike-<?php echo $facility['id']; ?>"><?php echo $live['available_bike']; ?></strong>/<?php echo $live['capacity_bike']; ?>
                    </div>
                    <div class="col-6">
                        <i class="bi bi-car-front me-1"></i>Car: <strong class="text-dark" id="admin-car-<?php echo $facility['id']; ?>"><?php echo $live['available_car']; ?></strong>/<?php echo $live['capacity_car']; ?>
                    </div>
                </div>

                <!-- Gate Action Buttons -->
                <div class="pt-2 border-top mt-auto">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted small" style="font-size: 0.72rem;">Gate Actions:</span>
                        <span class="text-muted small" style="font-size: 0.72rem;" id="admin-updated-<?php echo $facility['id']; ?>"><?php echo date('H:i:s', strtotime($live['last_updated'])); ?></span>
                    </div>
                    <div class="d-flex gap-1">
                        <button type="button" class="btn btn-sm btn-outline-success flex-fill py-1 btn-gate-action" 
                                data-id="<?php echo $facility['id']; ?>" data-type="bike" data-direction="exit" title="Bike leaves facility (+1 free spot)">
                            <i class="bi bi-plus-lg"></i> Bike Free
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger flex-fill py-1 btn-gate-action" 
                                data-id="<?php echo $facility['id']; ?>" data-type="bike" data-direction="entry" title="Bike enters facility (-1 free spot)">
                            <i class="bi bi-dash-lg"></i> Bike Park
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary flex-fill py-1 btn-gate-action" 
                                data-id="<?php echo $facility['id']; ?>" data-type="car" data-direction="exit" title="Car leaves facility (+1 free spot)">
                            <i class="bi bi-plus-lg"></i> Car Free
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-warning flex-fill py-1 btn-gate-action" 
                                data-id="<?php echo $facility['id']; ?>" data-type="car" data-direction="entry" title="Car enters facility (-1 free spot)">
                            <i class="bi bi-dash-lg"></i> Car Park
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Live Simulation Event Logs Box -->
<div class="card border rounded-3 bg-white p-3 shadow-xs">
    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
        <div class="fw-bold text-dark d-flex align-items-center gap-2">
            <i class="bi bi-terminal text-primary"></i> Live Gate Sensor Event Logs
        </div>
        <button type="button" class="btn btn-sm btn-light border py-0 px-2" id="btn-clear-logs">
            Clear Logs
        </button>
    </div>
    <div id="simulation-logs-container" class="font-monospace small bg-dark text-light p-3 rounded" style="height: 180px; overflow-y: auto; font-size: 0.8rem;">
        <div class="text-success">[System Ready] IoT Gate Sensors listening across Kathmandu facilities. Polling initialized.</div>
    </div>
</div>

<script>
window.appBaseUrl = <?php echo json_encode($baseUrl); ?>;

document.addEventListener('DOMContentLoaded', function() {
    var logsContainer = document.getElementById('simulation-logs-container');
    var autoSimInterval = null;

    function addLog(message, isSuccess) {
        if (!logsContainer) return;
        var entry = document.createElement('div');
        entry.className = isSuccess ? 'text-info' : 'text-warning';
        entry.textContent = message;
        logsContainer.prepend(entry);
    }

    // Update the city totals and cards based on incoming array
    function updateAdminGrid(facilities) {
        var totalCap = 0;
        var totalAvail = 0;

        facilities.forEach(function(item) {
            totalCap += parseInt(item.total_capacity || 0);
            totalAvail += parseInt(item.available_spaces || 0);

            var id = item.id;
            var badgeEl = document.getElementById('admin-badge-' + id);
            var availEl = document.getElementById('admin-avail-' + id);
            var progEl = document.getElementById('admin-progress-' + id);
            var bikeEl = document.getElementById('admin-bike-' + id);
            var carEl = document.getElementById('admin-car-' + id);
            var updatedEl = document.getElementById('admin-updated-' + id);
            var cardEl = document.getElementById('attendant-card-' + id);

            if (badgeEl) {
                badgeEl.className = 'badge ' + item.badge_class;
                badgeEl.textContent = item.status_text;
            }
            if (availEl) {
                availEl.className = item.text_color;
                availEl.textContent = item.available_spaces;
            }
            if (progEl) {
                progEl.className = 'progress-bar ' + (item.status_level === 'full' ? 'bg-danger' : (item.status_level === 'limited' ? 'bg-warning' : 'bg-success'));
                progEl.style.width = item.occupancy_percent + '%';
            }
            if (bikeEl) bikeEl.textContent = item.available_bike;
            if (carEl) carEl.textContent = item.available_car;
            if (updatedEl && item.last_updated) {
                updatedEl.textContent = item.last_updated.substring(11, 19);
            }
        });

        // Update top counter stats
        var totalOcc = totalCap - totalAvail;
        var occPct = totalCap > 0 ? Math.round((totalOcc / totalCap) * 100) : 0;

        var statCap = document.getElementById('stat-total-cap');
        var statAvail = document.getElementById('stat-avail-spaces');
        var statOcc = document.getElementById('stat-occupied-spaces');
        var statRate = document.getElementById('stat-occupancy-rate');

        if (statCap) statCap.textContent = totalCap;
        if (statAvail) statAvail.textContent = totalAvail;
        if (statOcc) statOcc.textContent = totalOcc;
        if (statRate) statRate.textContent = occPct + '%';
    }

    // Refresh data via live-spaces.php
    function refreshAdminSpaces() {
        fetch(window.appBaseUrl + '/live-spaces.php')
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data && data.success && Array.isArray(data.facilities)) {
                    updateAdminGrid(data.facilities);
                }
            })
            .catch(function(err) {});
    }

    // Initial load
    refreshAdminSpaces();
    // Poll every 5 seconds
    setInterval(refreshAdminSpaces, 5000);

    // Gate action buttons (+ / - entry / exit)
    document.querySelectorAll('.btn-gate-action').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var facId = this.getAttribute('data-id');
            var vType = this.getAttribute('data-type');
            var dir = this.getAttribute('data-direction');

            btn.disabled = true;

            fetch(window.appBaseUrl + '/live-spaces.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'adjust',
                    facility_id: facId,
                    vehicle_type: vType,
                    direction: dir
                })
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                btn.disabled = false;
                if (data.success && data.facility) {
                    addLog(data.log || (data.message + ' at ' + data.facility.name), true);
                    var card = document.getElementById('attendant-card-' + facId);
                    if (card) {
                        card.classList.add('pulse-update');
                        setTimeout(function() { card.classList.remove('pulse-update'); }, 1400);
                    }
                    refreshAdminSpaces();
                } else {
                    addLog('Gate Action Error: ' + (data.error || 'Failed'), false);
                }
            })
            .catch(function(err) {
                btn.disabled = false;
            });
        });
    });

    // Simulate 1 random event
    var btnSimOnce = document.getElementById('btn-simulate-once');
    if (btnSimOnce) {
        btnSimOnce.addEventListener('click', function() {
            btnSimOnce.disabled = true;
            fetch(window.appBaseUrl + '/live-spaces.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'simulate' })
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                btnSimOnce.disabled = false;
                if (data.success) {
                    addLog(data.log, true);
                    if (data.facility && data.facility.id) {
                        var card = document.getElementById('attendant-card-' + data.facility.id);
                        if (card) {
                            card.classList.add('pulse-update');
                            setTimeout(function() { card.classList.remove('pulse-update'); }, 1400);
                        }
                    }
                    refreshAdminSpaces();
                }
            })
            .catch(function(err) {
                btnSimOnce.disabled = false;
            });
        });
    }

    // Auto-simulation toggle
    var btnSimAuto = document.getElementById('btn-simulate-auto');
    var autoText = document.getElementById('auto-sim-text');
    if (btnSimAuto) {
        btnSimAuto.addEventListener('click', function() {
            if (autoSimInterval) {
                clearInterval(autoSimInterval);
                autoSimInterval = null;
                btnSimAuto.className = 'btn btn-success';
                autoText.textContent = 'Start Auto-Traffic Simulation';
                addLog('[Simulation Paused]', false);
            } else {
                btnSimAuto.className = 'btn btn-warning text-dark';
                autoText.textContent = 'Pause Simulation (Running every 3s)';
                addLog('[Auto-Traffic Simulation Started - Random vehicles entering and exiting]', true);
                
                // Fire immediately once
                if (btnSimOnce) btnSimOnce.click();

                autoSimInterval = setInterval(function() {
                    if (btnSimOnce) btnSimOnce.click();
                }, 3500);
            }
        });
    }

    var clearBtn = document.getElementById('btn-clear-logs');
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            if (logsContainer) logsContainer.innerHTML = '';
        });
    }
});
</script>

<?php
require_once __DIR__ . '/admin_footer.php';
?>

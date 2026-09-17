<?php
if (!isset($pageTitle)) {
    $pageTitle = 'Parking Information and Locator System | Kathmandu';
}
$baseUrl = getBaseUrl();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
        (function() {
            try {
                var currentFetch = window.fetch;
                var proto = Object.getPrototypeOf(window);
                var protoDesc = proto ? Object.getOwnPropertyDescriptor(proto, 'fetch') : null;
                if (protoDesc && !protoDesc.set && protoDesc.configurable) {
                    Object.defineProperty(proto, 'fetch', {
                        get: function() { return currentFetch; },
                        set: function(fn) { currentFetch = fn; },
                        configurable: true,
                        enumerable: true
                    });
                }
            } catch (e) {}

            try {
                var _f = window.fetch ? window.fetch.bind(window) : null;
                Object.defineProperty(window, 'fetch', {
                    get: function() { return _f; },
                    set: function(fn) { _f = fn; },
                    configurable: true,
                    enumerable: true
                });
            } catch (e) {}
        })();
    </script>
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="description" content="A Web-Based System for Finding Parking Facilities Near Selected Destinations in Kathmandu">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet">
    <link href="<?php echo $baseUrl; ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>

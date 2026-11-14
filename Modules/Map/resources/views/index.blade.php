<x-app-layout>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

    <style>
        #bikepathMap {
            height: calc(100vh - 65px);
            width: 100%;
            z-index: 1;
        }
    </style>

    <!-- Map Container Only -->
    <div class="relative w-full overflow-hidden">
        <div id="bikepathMap"></div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Inisialisasi Peta Leaflet (OpenStreetMap)
            const map = L.map('bikepathMap', {
                zoomControl: true
            }).setView([-6.2088, 106.8456], 13); // Default pusat: Jakarta

            // Tambahkan Tile Layer OpenStreetMap standar (100% gratis & open-source)
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors'
            }).addTo(map);
        });
    </script>
</x-app-layout>

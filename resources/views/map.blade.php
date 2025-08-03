@extends('layouts.app')

@section('title', 'Maps')

@section('content')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map {
            width: 100%;
            height: 80vh;
        }
        
        /* Leaflet specific styles */
        .leaflet-container {
            background: #f8f9fa;
        }
        
        /* Custom popup style */
        .leaflet-popup-content-wrapper {
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .leaflet-popup-content {
            margin: 8px 12px;
        }
        
        /* Marker cluster styles */
        .marker-cluster {
            background-color: rgba(0, 0, 0, 0.6);
            color: white;
            border-radius: 50%;
            text-align: center;
            font-weight: bold;
        }
        
        .marker-cluster-small {
            width: 30px !important;
            height: 30px !important;
            line-height: 30px !important;
        }
        
        .marker-cluster-medium {
            width: 40px !important;
            height: 40px !important;
            line-height: 40px !important;
        }
        
        .marker-cluster-large {
            width: 50px !important;
            height: 50px !important;
            line-height: 50px !important;
        }
    </style>

    <div class="container-fluid px-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="#">
                                <i class="fas fa-home"></i>
                            </a>
                        </li>
                        <li class="breadcrumb-item"><a href="#">AGS</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Map</li>
                    </ol>
                </nav>
                <h1 class="h4 mb-1">Customer Distribution Map</h1>
                <p class="text-muted mb-0">Interactive map showing customer counts by country</p>
            </div>
            <div>
                <a href="https://leafletjs.com/" class="btn btn-outline-gray-600">
                    <i class="fas fa-question-circle me-1"></i> Documentation
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div id="map"></div>
            </div>
        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- Marker Cluster Plugin -->
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
    
    <script>
        // Initialize map
        const map = L.map('map').setView([20, 0], 2);
        
        // Add monochrome tile layer (using Stadia Maps Alidade Smooth)
        L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth/{z}/{x}/{y}{r}.png', {
            maxZoom: 20,
            attribution: '&copy; <a href="https://stadiamaps.com/">Stadia Maps</a>, &copy; <a href="https://openmaptiles.org/">OpenMapTiles</a> &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors',
            className: 'map-tiles' // For grayscale filter
        }).addTo(map);
        
        // Apply grayscale to tiles
        document.querySelector('.map-tiles').style.filter = 'grayscale(100%) contrast(110%)';
        
        // Sample customer data by country (replace with your actual data)
        const customerData = {
            "Indonesia": { lat: -0.7893, lng: 113.9213, count: 28 },
            "USA": { lat: 37.8, lng: -96.9, count: 42 },
            "Japan": { lat: 36.2048, lng: 138.2529, count: 15 },
            "Germany": { lat: 51.1657, lng: 10.4515, count: 23 },
            "Brazil": { lat: -14.2350, lng: -51.9253, count: 18 }
        };
        
        // Create marker cluster group
        const markers = L.markerClusterGroup();
        
        // Add markers for each country
        Object.entries(customerData).forEach(([country, data]) => {
            const marker = L.circleMarker([data.lat, data.lng], {
                radius: Math.sqrt(data.count) * 0.8,
                fillColor: "#333",
                color: "#000",
                weight: 1,
                opacity: 1,
                fillOpacity: 0.8
            });
            
            marker.bindPopup(`
                <div class="p-2">
                    <h6 class="mb-1">${country}</h6>
                    <p class="mb-0"><strong>Customers:</strong> ${data.count}</p>
                </div>
            `);
            
            // Show tooltip on hover
            marker.bindTooltip(`${country}: ${data.count} customers`, {
                permanent: false,
                direction: 'top'
            });
            
            markers.addLayer(marker);
        });
        
        // Add markers to map
        map.addLayer(markers);
        
        // Add zoom control
        L.control.zoom({
            position: 'topright'
        }).addTo(map);
        
        // Add scale control
        L.control.scale({
            position: 'bottomleft',
            metric: true,
            imperial: false
        }).addTo(map);
    </script>
@endsection
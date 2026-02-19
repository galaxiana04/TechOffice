<!DOCTYPE html>
<html>
<head>
    <title>Visualisasi GPS</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        #map { height: 500px; margin-top: 20px; }
    </style>
</head>
<body>
    <h2>Upload File GPS (CSV)</h2>
    <form method="POST" action="{{ route('gps.upload') }}" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" accept=".csv" required>
        <button type="submit">Upload</button>
    </form>

    @if(isset($data))
        <h3>Visualisasi Jalur</h3>
        <div id="map"></div>

        <script>
    var map = L.map('map').setView([{{ $data[0]['latitude'] ?? 0 }}, {{ $data[0]['longitude'] ?? 0 }}], 18);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 20,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // Data dari PHP
    var data = {!! json_encode($data) !!};

    // Fungsi untuk menentukan warna berdasarkan speed
    function getColor(speed) {
        return speed > 20 ? '#d73027' :
               speed > 18 ? '#fc8d59' :
               speed > 16 ? '#fee08b' :
               speed > 14 ? '#d9ef8b' :
               speed > 12 ? '#91cf60' :
                            '#1a9850';
    }

    // Gambar garis antar titik dengan warna
    for (let i = 0; i < data.length - 1; i++) {
        let pointA = [data[i].latitude, data[i].longitude];
        let pointB = [data[i + 1].latitude, data[i + 1].longitude];
        let speed = data[i].speed_kmh;
        L.polyline([pointA, pointB], {
            color: getColor(speed),
            weight: 5,
            opacity: 0.8
        }).addTo(map);
    }

    // Titik awal dan akhir
    L.circleMarker([data[0].latitude, data[0].longitude], {color: 'green'}).bindPopup("Start").addTo(map);
    L.circleMarker([data[data.length - 1].latitude, data[data.length - 1].longitude], {color: 'red'}).bindPopup("End").addTo(map);

    // Legend
    var legend = L.control({position: 'bottomright'});

    legend.onAdd = function (map) {
        var div = L.DomUtil.create('div', 'info legend'),
            grades = [0, 12, 14, 16, 18, 20],
            labels = [];

        div.innerHTML += '<b>Speed (km/h)</b><br>';
        for (var i = 0; i < grades.length; i++) {
            div.innerHTML +=
                '<i style="background:' + getColor(grades[i] + 0.1) + '"></i> ' +
                grades[i] + (grades[i + 1] ? '&ndash;' + grades[i + 1] + '<br>' : '+');
        }
        return div;
    };

    legend.addTo(map);
</script>

    @endif
    <style>
    .info.legend {
        background: white;
        line-height: 1.5em;
        padding: 6px 8px;
        font: 14px/16px Arial, Helvetica, sans-serif;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        border-radius: 5px;
    }

    .info.legend i {
        width: 18px;
        height: 18px;
        float: left;
        margin-right: 8px;
        opacity: 0.8;
        display: inline-block;
    }
</style>

</body>
</html>

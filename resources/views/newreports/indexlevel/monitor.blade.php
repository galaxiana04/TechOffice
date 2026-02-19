<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INKA - Monitoring Dokumen Per Jenis</title>

    <!-- Font & Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #e3f2fd, #bbdefb, #90caf9);
            /* Cerah, biru langit untuk tema INKA (kereta api) */
            color: #333;
            /* Teks gelap untuk kontras */
            min-height: 100vh;
            overflow: hidden;
        }

        .header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 2px solid #1976d2;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            padding: 20px 30px;
            display: flex;
            align-items: center;
            gap: 20px;
            /* Jarak antar elemen */
        }

        .logo img {
            height: 70px;
        }

        .title {
            font-size: 2.5rem;
            /* Lebih kecil dari sebelumnya */
            font-weight: 700;
            background: linear-gradient(90deg, #d32f2f, #1976d2, #ff9800);
            /* Merah, biru, orange - warna INKA */
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 0 20px rgba(25, 118, 210, 0.3);
        }

        .subtitle {
            font-size: 1.4rem;
            color: #555;
        }

        .project-name {
            font-size: 2.5rem;
            /* Lebih kecil tapi masih besar */
            font-weight: 700;
            color: #1976d2;
            /* Biru INKA */
        }

        .container {
            margin-top: 120px;
            /* Adjust berdasarkan tinggi header */
            padding: 30px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(600px, 1fr));
            /* Card lebih lebar */
            gap: 30px;
            max-width: 1920px;
            margin-left: auto;
            margin-right: auto;
        }

        .card {
            background: rgba(255, 255, 255, 0.95);
            /* Putih cerah */
            border-radius: 60px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            /* Shadow lembut */
            transition: all 0.4s ease;
            animation: slideUp 0.8s ease-out;
            border: 2px solid #e3f2fd;
            /* Border biru muda */
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background: linear-gradient(45deg, #1976d2, #42a5f5);
            /* Biru INKA */
            color: white;
            padding: 25px;
            text-align: center;
            font-size: 2rem;
            /* Lebih besar */
            font-weight: 600;
        }

        .card-body {
            padding: 30px;
        }

        .status-summary {
            text-align: center;
            margin: 25px 0;
            padding: 20px;
            background: rgba(25, 118, 210, 0.1);
            /* Biru muda */
            border-radius: 15px;
        }

        .total-docs {
            font-size: 4rem;
            /* Lebih kecil dari sebelumnya */
            font-weight: 800;
            color: #1976d2;
            /* Biru */
            margin-bottom: 10px;
        }

        .percentage {
            font-size: 6rem;
            /* Lebih besar untuk menonjol */
            font-weight: 900;
            color: #4caf50;
            /* Hijau untuk persentase */
            margin: 10px 0;
        }

        .released-unreleased {
            font-size: 1.8rem;
            margin-top: 15px;
        }

        .released {
            color: #4caf50;
            /* Hijau cerah */
            font-weight: 700;
        }

        .unreleased {
            color: #f44336;
            /* Merah cerah */
            font-weight: 700;
        }

        .dates-summary {
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background: rgba(255, 193, 7, 0.1);
            /* Kuning muda untuk dates */
            border-radius: 15px;
            font-size: 1.2rem;
            color: #856404;
            /* Kuning gelap */
        }

        .kinds-container {
            max-height: 450px;
            /* Diperpanjang dari 400px ke 600px agar card lebih panjang ke bawah */
            overflow-y: auto;
            /* Scroll vertikal hanya untuk jenis dokumen */
            margin-top: 20px;
        }

        .kind-item {
            background: rgba(25, 118, 210, 0.05);
            /* Biru sangat muda */
            padding: 18px 22px;
            border-radius: 12px;
            margin: 12px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s;
            border-left: 6px solid #1976d2;
            /* Biru */
        }

        .kind-item:hover {
            background: rgba(25, 118, 210, 0.1);
            transform: translateX(10px);
        }

        .kind-name {
            font-weight: 600;
            font-size: 1.3rem;
            /* Lebih besar */
        }

        .kind-badge {
            background: #1976d2;
            /* Biru */
            color: white;
            padding: 10px 18px;
            border-radius: 50px;
            font-weight: bold;
            min-width: 70px;
            text-align: center;
            font-size: 1.1rem;
        }

        .status-detail {
            font-size: 1rem;
            margin-top: 8px;
            color: #666;
        }

        .status-detail .text-success {
            color: #4caf50;
            /* Hijau */
        }

        .status-detail .text-danger {
            color: #f44336;
            /* Merah */
        }

        .date-display {
            position: fixed;
            bottom: 30px;
            left: 40px;
            background: rgba(255, 255, 255, 0.9);
            padding: 15px 25px;
            border-radius: 50px;
            font-size: 1.1rem;
            color: #333;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 999;
        }

        .auto-rotate {
            position: fixed;
            bottom: 30px;
            right: 40px;
            background: rgba(255, 255, 255, 0.9);
            padding: 15px 25px;
            border-radius: 50px;
            font-size: 1.1rem;
            color: #333;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 999;
        }


        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsivitas untuk monitor besar */
        @media (min-width: 1920px) {
            .container {
                grid-template-columns: repeat(3, 1fr);
                /* Maksimal 3 kolom di layar sangat besar */
            }
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="logo">
            <img src="{{ asset('images/logo-inka.png') }}" alt="INKA">
        </div>
        <div class="title">MONITORING DOKUMEN</div>
        <div class="subtitle">-</div>
        <div class="project-name" id="projectName">Memuat...</div>
    </div>

    <div class="container" id="levelCardsContainer">
        <!-- Cards akan diisi oleh JS -->
    </div>

    <div class="date-display" id="currentDate"></div>

    <div class="auto-rotate">
        <i class="fas fa-sync-alt"></i> Ganti project otomatis setiap <span id="countdown">30</span> detik
    </div>

    <script>
        const API_BASE = "{{ url('newreports/level-data') }}"; // Ganti kalau beda domain
        let projects = @json($projects->pluck('title', 'id'));
        let projectIds = Object.keys(projects);
        let currentIndex = 0;
        let countdown = 30;

        // Update tanggal & jam real-time
        function updateDateTime() {
            const now = new Date();
            document.getElementById('currentDate').textContent =
                now.toLocaleDateString('id-ID', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                }) +
                ' — ' + now.toTimeString().substr(0, 8);
        }
        setInterval(updateDateTime, 1000);
        updateDateTime();

        // Countdown
        function startCountdown() {
            countdown = 30;
            const el = document.getElementById('countdown');
            const timer = setInterval(() => {
                countdown--;
                el.textContent = countdown;
                if (countdown <= 0) {
                    clearInterval(timer);
                    nextProject();
                }
            }, 1000);
        }

        // Ganti project otomatis
        function nextProject() {
            currentIndex = (currentIndex + 1) % projectIds.length;
            loadProject(projectIds[currentIndex]);
        }

        function loadProject(projectId) {
            document.getElementById('projectName').textContent = projects[projectId] || 'Unknown Project';

            fetch(API_BASE + '/' + projectId)
                .then(r => r.json())
                .then(res => {
                    document.getElementById('levelCardsContainer').innerHTML = '';

                    res.levels.forEach(level => {
                        let kindsHtml = '';
                        const sorted = Object.keys(level.kinds).sort((a, b) => level.kinds[b] - level.kinds[a]);

                        sorted.forEach(kindId => {
                            if (level.kinds[kindId] <= 0) return;

                            const name = kindId == 0 ? 'Belum Diketik Jenis' : (res.document_kinds[
                                kindId] || 'Unknown');
                            const totalInKind = level.kinds[kindId];
                            // JADI PAKAI DATA ASLI:
                            const kindDetail = level.kinds_detail?.[kindId] || {};
                            const releasedInKind = kindDetail.released || 0;
                            const unreleasedInKind = kindDetail.unreleased || 0;
                            const emoji = kindDetail.emoji || '😐';
                            const emojiClass = kindDetail.emoji_class || 'text-secondary';

                            const latestDeadline = kindDetail.latest_deadline ?
                                new Date(kindDetail.latest_deadline) // langsung valid!
                                :
                                null;

                            kindsHtml += `
<div class="kind-item">
    <div>
        <div class="kind-name">
            ${name}
        </div>
        ${latestDeadline ? `<div class="text-muted small"><i class="fas fa-calendar-alt"></i> Deadline: ${latestDeadline.toLocaleDateString('id-ID', {day:'numeric', month:'short', year:'numeric'})}</div>` : ''}
        <div class="status-detail">
            <span class="text-success">RELEASED ${releasedInKind}</span>
            <span class="mx-2">|</span>
            <span class="text-danger">UNRELEASED ${unreleasedInKind}</span>
            <div class="kind-badge">Total Doc: ${totalInKind}</div>
        </div>
    </div>
    <div class="d-flex align-items-center gap-4">
        
        <span class="${emojiClass}" style="font-size:2.8rem;">${emoji}</span>
    
    </div>
</div>`;
                        });

                        if (!kindsHtml) {
                            kindsHtml =
                                `<div class="text-center py-5 text-muted"><i class="fas fa-folder-open fa-4x"></i><br><br>Belum ada dokumen</div>`;
                        }

                        // Tambahkan dates summary
                        let datesHtml = '';
                        if (level.start_date || level.deadline_date) {
                            datesHtml = `<div class="dates-summary">
                                ${level.start_date ? `<i class="fas fa-calendar-alt"></i> Start: ${level.start_date}` : ''}
                                ${level.start_date && level.deadline_date ? ' | ' : ''}
                                ${level.deadline_date ? `<i class="fas fa-clock"></i> Deadline: ${level.deadline_date}` : ''}
                            </div>`;
                        }

                        // Hitung persentase
                        const percentage = level.total > 0 ? Math.round((level.released / level.total) * 100) :
                            0;

                        const card = document.createElement('div');
                        card.className = 'card';
                        card.innerHTML = `
                            <div class="card-header">
                                <i class="fas fa-layer-group"></i> ${level.level_title}
                            </div>
                            <div class="card-body">
                                <div class="status-summary">
                                    <div class="total-docs">${level.total} Total Dokumen</div>
                                    <div class="percentage">${percentage}%</div>
                                    <div class="released-unreleased">
                                        <span class="released">${level.released} RELEASED</span>
                                        <span class="mx-4">|</span>
                                        <span class="unreleased">${level.unreleased} UNRELEASED</span>
                                    </div>
                                </div>
                                ${datesHtml}
                                <div class="kinds-container">
                                    ${kindsHtml}
                                </div>
                            </div>
                        `;
                        document.getElementById('levelCardsContainer').appendChild(card);
                    });

                    startCountdown();
                })
                .catch(err => {
                    console.error(err);
                    document.getElementById('levelCardsContainer').innerHTML =
                        `<div class="text-center text-danger" style="grid-column: 1/-1; padding: 50px;">
                            <i class="fas fa-exclamation-triangle fa-3x"></i><br><br>
                            Gagal memuat data project
                         </div>`;
                    startCountdown();
                });
        }

        // Start dengan project pertama
        if (projectIds.length > 0) {
            loadProject(projectIds[0]);
        }

        // Keyboard: spasi = next, F11 = fullscreen
        document.addEventListener('keydown', e => {
            if (e.key === ' ') {
                e.preventDefault();
                nextProject();
            }
        });
    </script>
</body>

</html>

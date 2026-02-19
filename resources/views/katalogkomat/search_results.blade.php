<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencarian Katalog Kode Material</title>
    <style>
        body {
            margin: 0;
            font-family: "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(135deg, #fdfbfb, #ebedee);
            color: #333;
        }

        .container {
            max-width: 900px;
            margin: 2rem auto;
            padding: 1rem;
        }

        h1 {
            text-align: center;
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 2rem;
            color: #2c3e50;
        }

        .breadcrumb {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 2rem;
        }

        .breadcrumb a {
            color: #3498db;
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .search-box {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }

        .search-box input {
            padding: 0.8rem 1rem;
            width: 70%;
            border: 2px solid #a5d8ff;
            border-radius: 25px 0 0 25px;
            outline: none;
            font-size: 1rem;
            background: #fff;
            color: #333;
        }

        .search-box button {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 0 25px 25px 0;
            background: linear-gradient(45deg, #6dd5fa, #81ecec);
            color: #fff;
            font-size: 1rem;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .search-box button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 150, 255, 0.4);
        }

        /* Hasil list */
        .results {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .card {
            background: #ffffff;
            border-radius: 10px;
            padding: 1rem 1.5rem;
            border: 1px solid #e0e0e0;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(0, 180, 255, 0.2);
        }

        .card h5 {
            font-size: 1.1rem;
            margin-bottom: 0.8rem;
            color: #3498db;
        }

        .card p {
            margin: 0.3rem 0;
            font-size: 0.95rem;
            color: #555;
        }

        .card strong {
            color: #000;
        }

        ul {
            margin: 0.5rem 0 0 1.2rem;
            padding: 0;
        }

        .alert {
            background: #fff8e1;
            border-left: 5px solid #ffca28;
            padding: 1rem;
            border-radius: 10px;
            margin-top: 1.5rem;
            color: #795548;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="{{ route('katalogkomat.searchKomat') }}">Search Results</a>
        </div>

        <!-- Title -->
        <h1>Pencarian Katalog Kode Material</h1>

        <!-- Form Pencarian -->
        <form action="{{ route('katalogkomat.searchKomat') }}" method="GET" class="search-box">
            <input type="text" name="query" value="{{ $query }}"
                placeholder="Cari kode material, deskripsi, atau spesifikasi" required>
            <button type="submit">Cari</button>
        </form>

        <!-- Jika ada hasil -->
        @if ($results->count() > 0)
            <p><strong>Query:</strong> {{ $query }}</p>
            <div class="results">
                @foreach ($results as $result)
                    <div class="card">
                        <h5>Kode Material: {{ $result->kodematerial }}</h5>
                        <p><strong>Deskripsi:</strong> {{ $result->deskripsi }}</p>
                        <p><strong>Spesifikasi:</strong> {{ $result->spesifikasi }}</p>
                        <p><strong>UoM:</strong> {{ $result->UoM }}</p>
                        <p><strong>Stok UU di Ekspedisi:</strong> {{ $result->stokUUekpedisi }}</p>
                        <p><strong>Stok UU di Gudang:</strong> {{ $result->stokUUgudang }}</p>
                        <p><strong>Stok Project di Ekspedisi:</strong> {{ $result->stokprojectekpedisi }}</p>
                        <p><strong>Stok Project di Gudang:</strong> {{ $result->stokprojectgudang }}</p>

                        @if (isset($proyekData[$result->kodematerial]) && count($proyekData[$result->kodematerial]) > 0)
                            <p><strong>Terikat dengan Proyek:</strong></p>
                            <ul>
                                @foreach ($proyekData[$result->kodematerial] as $proyek)
                                    <li>{{ $proyek }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p><strong>Terikat dengan Proyek:</strong> Tidak ada</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert">
                ⚠️ Tidak ada katalog kode material yang ditemukan untuk pencarian: <strong>{{ $query }}</strong>
            </div>
        @endif
    </div>
</body>

</html>

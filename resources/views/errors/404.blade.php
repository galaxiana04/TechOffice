<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        integrity="sha512-y7LjQ82KQUkK04bXk5qfKn9P0nIfxXY3xAe1Eum6Cy+0x4vEfwNLmztYh2+x8ck9vmyLfAG0swCzv5cZpI9lWg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            text-align: center;
            animation: fadeIn 1.2s ease-in-out;
        }

        .icon {
            font-size: 6rem;
            color: #e74c3c;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        p {
            font-size: 1.2rem;
            margin-bottom: 20px;
        }

        a {
            text-decoration: none;
            background-color: #3498db;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        a:hover {
            background-color: #2980b9;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h1>404 - Halaman Tidak Ditemukan</h1>
        <p>Sepertinya halaman yang Anda cari sudah dipindahkan atau tidak tersedia.</p>
        <a href="{{ url('/') }}">Kembali ke Beranda</a>
    </div>
</body>

</html>

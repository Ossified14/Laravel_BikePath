<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'My App')</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }

        /* ── Navbar ── */
        nav {
            background-color: #1a1a2e;
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 60px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }

        nav .brand {
            color: #e94560;
            font-size: 1.3rem;
            font-weight: 700;
            text-decoration: none;
            letter-spacing: 1px;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 1.5rem;
        }

        nav ul li a {
            color: #ccc;
            text-decoration: none;
            font-size: 0.95rem;
            padding: 0.4rem 0.8rem;
            border-radius: 4px;
            transition: background 0.2s, color 0.2s;
        }

        nav ul li a:hover,
        nav ul li a.active {
            background-color: #e94560;
            color: #fff;
        }

        /* ── Content ── */
        .container {
            max-width: 900px;
            margin: 2.5rem auto;
            padding: 0 1.5rem;
        }

        /* ── Card ── */
        .card {
            background: #fff;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }

        .card h2 {
            margin-bottom: 1.2rem;
            color: #1a1a2e;
            font-size: 1.5rem;
            border-bottom: 2px solid #e94560;
            padding-bottom: 0.5rem;
        }

        /* ── Profile table ── */
        .profile-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0.5rem;
        }

        .profile-table th,
        .profile-table td {
            padding: 0.75rem 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .profile-table th {
            color: #666;
            font-weight: 600;
            width: 180px;
        }

        /* ── Badge ── */
        .badge {
            display: inline-block;
            background-color: #e94560;
            color: #fff;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.82rem;
            margin: 0.2rem 0.2rem 0.2rem 0;
        }

        /* ── Card header ── */
        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.2rem;
            border-bottom: 2px solid #e94560;
            padding-bottom: 0.5rem;
        }

        .card-header h2 {
            border: none;
            margin: 0;
            padding: 0;
        }

        .btn-edit {
            padding: 0.35rem 1rem;
            background: #1a1a2e;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.88rem;
            transition: background 0.2s;
        }

        .btn-edit:hover { background: #e94560; }

        .btn-batal {
            padding: 0.35rem 1rem;
            background: #aaa;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.88rem;
            transition: background 0.2s;
        }

        .btn-batal:hover { background: #888; }

        .input-field {
            padding: 0.4rem 0.7rem;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .input-field:focus { border-color: #e94560; }

        .text-muted { color: #aaa; font-style: italic; }

        /* ── Form dropdown ── */
        .select-input {
            padding: 0.45rem 0.75rem;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 0.95rem;
            cursor: pointer;
            outline: none;
            transition: border-color 0.2s;
        }

        .select-input:focus {
            border-color: #e94560;
        }

        .btn-submit {
            margin-left: 0.5rem;
            padding: 0.45rem 1rem;
            background-color: #e94560;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-submit:hover {
            background-color: #c73652;
        }

        .hasil-pilihan {
            margin-top: 0.75rem;
            font-size: 0.95rem;
            color: #444;
        }

        .error-msg {
            margin-top: 0.5rem;
            color: #e94560;
            font-size: 0.88rem;
        }

        /* ── Home empty state ── */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #999;
        }

        .empty-state p {
            font-size: 1.1rem;
        }
    </style>
</head>
<body>

    {{-- Navbar sebagai komponen Blade --}}
    <x-navbar />

    {{-- Konten utama setiap halaman --}}
    <div class="container">
        @yield('content')
    </div>

</body>
</html>



<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'BikeProfile')</title>
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
            align-items: center;
            gap: 1rem;
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

        .nav-user {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            border-left: 1px solid #333;
            padding-left: 1rem;
            margin-left: 0.5rem;
        }

        .nav-greeting {
            color: #aaa;
            font-size: 0.88rem;
        }

        .btn-logout {
            background: transparent;
            border: 1px solid #e94560;
            color: #e94560;
            padding: 0.3rem 0.7rem;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.85rem;
            transition: background 0.2s, color 0.2s;
        }

        .btn-logout:hover {
            background: #e94560;
            color: #fff;
        }

        /* ── Content ── */
        .container {
            max-width: 960px;
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

        /* ── User table ── */
        .user-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0.5rem;
            font-size: 0.95rem;
        }

        .user-table thead tr {
            background-color: #1a1a2e;
            color: #ccc;
        }

        .user-table thead th {
            padding: 0.75rem 1rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.88rem;
            letter-spacing: 0.03em;
        }

        .user-table tbody tr {
            border-bottom: 1px solid #eee;
            transition: background 0.15s;
        }

        .user-table tbody tr:hover {
            background-color: #fafafa;
        }

        .user-table tbody tr.row-me {
            background-color: #fff5f7;
        }

        .user-table tbody td {
            padding: 0.75rem 1rem;
        }

        .td-num  { color: #aaa; width: 40px; }
        .td-email { color: #555; font-size: 0.88rem; }
        .td-date  { color: #888; font-size: 0.85rem; }

        .badge-me {
            display: inline-block;
            background-color: #1a1a2e;
            color: #e94560;
            padding: 0.15rem 0.5rem;
            border-radius: 20px;
            font-size: 0.75rem;
            margin-left: 0.4rem;
            font-weight: 600;
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

        /* ── Buttons ── */
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

        .btn-submit {
            padding: 0.45rem 1.2rem;
            background-color: #e94560;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-submit:hover { background-color: #c73652; }

        .btn-full { width: 100%; padding: 0.65rem; font-size: 1rem; }

        /* ── Form inputs ── */
        .input-field {
            padding: 0.4rem 0.7rem;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.2s;
            width: 100%;
        }

        .input-field:focus { border-color: #e94560; }
        .input-error { border-color: #e94560 !important; }

        .select-input {
            padding: 0.45rem 0.75rem;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 0.95rem;
            cursor: pointer;
            outline: none;
            transition: border-color 0.2s;
        }

        .select-input:focus { border-color: #e94560; }

        /* ── Auth card ── */
        .auth-card {
            background: #fff;
            border-radius: 10px;
            padding: 2.5rem;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            max-width: 460px;
            margin: 0 auto;
        }

        .auth-title {
            text-align: center;
            color: #1a1a2e;
            font-size: 1.5rem;
            margin-bottom: 1.8rem;
        }

        .auth-form { display: flex; flex-direction: column; gap: 1rem; }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .form-group label {
            font-size: 0.9rem;
            color: #555;
            font-weight: 600;
        }

        .form-check {
            flex-direction: row;
            align-items: center;
            gap: 0.5rem;
        }

        .check-label { font-weight: 400; color: #666; cursor: pointer; }

        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: #888;
            font-size: 0.9rem;
        }

        .auth-link {
            color: #e94560;
            text-decoration: none;
            font-weight: 600;
        }

        .auth-link:hover { text-decoration: underline; }

        /* ── Alert ── */
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 6px;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .text-muted { color: #aaa; font-style: italic; }

        .error-msg {
            color: #e94560;
            font-size: 0.85rem;
        }

        /* ── Empty state ── */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #999;
        }

        .empty-state p { font-size: 1.1rem; }
    </style>
</head>
<body>

    <x-navbar />

    <div class="container">
        @yield('content')
    </div>

</body>
</html>
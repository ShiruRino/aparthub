<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Aparthub</title>
    <style>
        :root {
            --bg: #f6f7f4;
            --surface: #ffffff;
            --text: #18201b;
            --muted: #647067;
            --line: #dde3dc;
            --accent: #1f7a55;
            --accent-strong: #155e42;
            --danger: #b42318;
        }

        * {
            box-sizing: border-box;
        }

        body {
            display: grid;
            min-height: 100vh;
            margin: 0;
            place-items: center;
            background: var(--bg);
            color: var(--text);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            font-size: 14px;
        }

        .login {
            width: min(420px, calc(100vw - 32px));
            padding: 30px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--surface);
            box-shadow: 0 18px 42px rgba(24, 32, 27, 0.1);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 26px;
            font-size: 22px;
            font-weight: 800;
        }

        .brand-mark {
            display: grid;
            width: 36px;
            height: 36px;
            place-items: center;
            border-radius: 8px;
            background: #d8f36f;
            color: #17231d;
            font-size: 16px;
        }

        h1 {
            margin: 0 0 6px;
            font-size: 24px;
            line-height: 1.2;
        }

        p {
            margin: 0 0 22px;
            color: var(--muted);
        }

        form {
            display: grid;
            gap: 16px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
        }

        input {
            width: 100%;
            min-height: 42px;
            border: 1px solid #cfd8d1;
            border-radius: 8px;
            padding: 10px 12px;
            color: var(--text);
            font: inherit;
            outline: none;
        }

        input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(31, 122, 85, 0.14);
        }

        .row {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
        }

        .row input {
            width: 18px;
            height: 18px;
            min-height: 18px;
            accent-color: var(--accent);
        }

        button {
            min-height: 42px;
            border: 0;
            border-radius: 8px;
            background: var(--accent);
            color: #ffffff;
            cursor: pointer;
            font: inherit;
            font-weight: 800;
        }

        button:hover {
            background: var(--accent-strong);
        }

        .alert {
            margin-bottom: 16px;
            padding: 12px 14px;
            border: 1px solid #f4b8b2;
            border-radius: 8px;
            background: #fff1f0;
            color: var(--danger);
            font-weight: 700;
        }
    </style>
</head>
<body>
    <main class="login">
        <div class="brand">
            <span class="brand-mark">A</span>
            <span>Aparthub</span>
        </div>

        <h1>Login Admin</h1>
        <p>Masuk dengan username untuk mengelola akses sistem.</p>

        @if ($errors->any())
            <div class="alert">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}">
            @csrf

            <div>
                <label for="username">Username</label>
                <input id="username" name="username" value="{{ old('username') }}" autocomplete="username" autofocus required>
            </div>

            <div>
                <label for="password">Password</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required>
            </div>

            <label class="row">
                <input type="checkbox" name="remember" value="1">
                <span>Ingat saya</span>
            </label>

            <button type="submit">Login</button>
        </form>
    </main>
</body>
</html>

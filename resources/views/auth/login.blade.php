<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
<div class="auth">
    <aside class="auth-side">
        <div class="brand"><span class="brand-mark">U</span> UserHub</div>
        <div>
            <h2>Manage your users in one place.</h2>
            <p>Sign in to view, edit and remove accounts from a single, simple dashboard.</p>
        </div>
        <small>&copy; {{ date('Y') }} UserHub</small>
    </aside>

    <main class="auth-main">
        <div class="auth-card">
            <h1>Welcome back</h1>
            <p class="sub">Sign in to your account to continue.</p>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-error">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="field">
                    <label for="email">Email</label>
                    <input class="input" id="email" type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" autocomplete="email" required autofocus>
                </div>
                <div class="field">
                    <label for="password">Password</label>
                    <input class="input" id="password" type="password" name="password" placeholder="Enter your password" autocomplete="current-password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>

            <p class="auth-foot">Don't have an account? <a href="{{ route('register') }}">Create one</a></p>
        </div>
    </main>
</div>
</body>
</html>
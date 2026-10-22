<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create account</title>
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
            <h2>Create your account in a minute.</h2>
            <p>Register with your name, email and a password to get access to the dashboard.</p>
        </div>
        <small>&copy; {{ date('Y') }} UserHub</small>
    </aside>

    <main class="auth-main">
        <div class="auth-card">
            <h1>Create account</h1>
            <p class="sub">Fill in your details to get started.</p>

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="field">
                    <label for="name">Full name</label>
                    <input class="input @error('name') has-error @enderror" id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Jane Doe" autocomplete="name" required autofocus>
                    @error('name')<div class="error-text">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label for="email">Email</label>
                    <input class="input @error('email') has-error @enderror" id="email" type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" autocomplete="email" required>
                    @error('email')<div class="error-text">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label for="password">Password</label>
                    <input class="input @error('password') has-error @enderror" id="password" type="password" name="password" placeholder="Create a password" autocomplete="new-password" required>
                    @error('password')<div class="error-text">{{ $message }}</div>@enderror
                </div>
                <button type="submit" class="btn btn-primary btn-block">Create account</button>
            </form>

            <p class="auth-foot">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
        </div>
    </main>
</div>
</body>
</html>
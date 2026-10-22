<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

<nav class="nav">
    <div class="nav-inner">
        <div class="brand"><span class="brand-mark">U</span> UserHub</div>

        <div class="nav-links">
            <a href="#" class="active">Users</a>
        </div>

        <div class="nav-user">
            <div class="avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
            <div class="who">
                <strong>{{ Auth::user()->name }}</strong>
                <span>{{ Auth::user()->email }}</span>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-ghost btn-sm">Log out</button>
            </form>
        </div>
    </div>
</nav>

<main class="page">
    <div class="page-head">
        <div>
            <h1>Welcome, {{ Auth::user()->name }}</h1>
            <p>View, edit and remove user accounts.</p>
        </div>
        <div class="count"><strong>{{ $users->count() }}</strong> {{ Str::plural('user', $users->count()) }}</div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <section class="panel">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th class="right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($users as $user)
                    <tr>
                        <td class="id">{{ $user->id }}</td>
                        <td><input class="input" type="text" name="name" value="{{ $user->name }}" form="update-{{ $user->id }}" aria-label="Name"></td>
                        <td><input class="input" type="email" name="email" value="{{ $user->email }}" form="update-{{ $user->id }}" aria-label="Email"></td>
                        <td>
                            <div class="actions">
                                <form id="update-{{ $user->id }}" action="{{ route('user.update', $user->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-primary btn-sm">Save changes</button>
                                </form>
                                <form action="{{ route('user.delete', $user->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete {{ addslashes($user->name) }}? This cannot be undone.')">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty">No users yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
</main>

</body>
</html>
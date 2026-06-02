<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Admin Login — TechBits</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body style="background:var(--surface);display:flex;align-items:center;justify-content:center;min-height:100vh;">
<div class="auth-card">
    <h1 style="text-align:center;">TechBits Admin</h1>
    @if($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif
    <form method="POST" action="{{ route('admin.login.submit') }}">
        @csrf
        <div class="form-group">
            <label>Email</label>
            <input class="form-control" type="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input class="form-control" type="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;">Log in</button>
    </form>
</div>
</body>
</html>

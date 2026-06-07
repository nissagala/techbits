<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Admin Verification — TechBits</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body style="background:var(--surface);display:flex;align-items:center;justify-content:center;min-height:100vh;">
<div class="auth-card">
    <h1 style="text-align:center;">TechBits Admin</h1>
    <h2 style="text-align:center;font-size:1.1rem;font-weight:600;margin-bottom:.5rem;">Verify Login</h2>
    <p style="color:var(--text-muted);margin-bottom:.5rem;text-align:center;">We've sent a verification code to your registered email address.</p>
    <p style="font-size:.8rem;color:var(--text-muted);margin-bottom:1.25rem;text-align:center;">If you close this page your login attempt will be cancelled.</p>

    @if($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.login.otp.submit') }}">
        @csrf
        <div class="form-group">
            <label>Verification Code</label>
            <input class="form-control otp-input" type="text" name="otp" inputmode="numeric" maxlength="6" pattern="\d{6}" placeholder="000000" autofocus required>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;margin-bottom:.75rem;">Verify</button>
    </form>
    <div style="text-align:center;">
        <form method="POST" action="{{ route('admin.login.otp.resend') }}" style="display:inline;">
            @csrf
            <button type="submit" class="btn btn-secondary btn-sm">Resend code</button>
        </form>
        <p style="font-size:.78rem;color:var(--text-muted);margin-top:.5rem;">Code expires in 10 minutes.</p>
    </div>
</div>
</body>
</html>

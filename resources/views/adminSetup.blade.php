<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Up Administrator Account</title>
    <style>
        *{box-sizing:border-box}body{margin:0;min-height:100vh;display:grid;place-items:center;padding:20px;background:#080808;color:#fff;font-family:Arial,sans-serif}.card{width:100%;max-width:420px;padding:30px;border:1px solid #cdaa5d;border-radius:18px;background:#141414}.card h1{margin:0 0 8px;font-size:25px}.email{margin:0 0 24px;color:#d8bd7c}.field{margin-bottom:16px}.field label{display:block;margin-bottom:7px;font-size:14px}.field input{width:100%;height:48px;padding:0 14px;border:1px solid #6f613e;border-radius:9px;background:#0d0d0d;color:#fff;font-size:16px}.button{width:100%;height:50px;border:0;border-radius:999px;background:#cdaa5d;color:#080808;font-size:16px;font-weight:700;cursor:pointer}.errors{margin-bottom:16px;padding:12px;border-radius:8px;background:#481b1b;color:#ffd2d2}
    </style>
</head>
<body>
<main class="card">
    <h1>Create your password</h1>
    <p class="email">{{ $email }}</p>
    @if($errors->any())<div class="errors">{{ $errors->first() }}</div>@endif
    <form method="POST" action="{{ request()->fullUrl() }}">
        @csrf
        <div class="field"><label for="password">New password</label><input id="password" name="password" type="password" minlength="8" required autocomplete="new-password"></div>
        <div class="field"><label for="password_confirmation">Confirm password</label><input id="password_confirmation" name="password_confirmation" type="password" minlength="8" required autocomplete="new-password"></div>
        <button class="button" type="submit">Set Password and Continue</button>
    </form>
</main>
</body>
</html>

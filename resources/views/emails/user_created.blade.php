<!DOCTYPE html>
<html>
<head>
    <title>Account Created</title>
</head>
<body>
<h1>Hello, {{ $name }}!</h1>
<p>Your account has been created successfully.</p>
<p>Your login details are:</p>
<ul>
    <li><strong>URL:</strong> {{ $url }}</li>
    <li><strong>Email:</strong> {{ $email }}</li>
    <li><strong>Password:</strong> {{ $password }}</li>
</ul>
<p>Please change your password after logging in.</p>
</body>
</html>
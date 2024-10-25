<!DOCTYPE html>
<html>
<head>
    <title>Account Created</title>
</head>
<body>
<h1>Welcome to OpenGRC!</h1>
<p>Hello, {{ $name }}!</p>
<p>An OpenGRC account has been created for you. You may your account using the credentials provided below.</p>
<p>Your login details are:</p>
<ul>
    <li><strong>URL:</strong> {{ $url }}</li>
    <li><strong>Email:</strong> {{ $email }}</li>
    <li><strong>Password:</strong> {{ $password }}</li>
</ul>
<p>After logging in you will be prompted to change your password. You will then be asked to re-login with your new secret password before continuing.</p>
</body>
</html>
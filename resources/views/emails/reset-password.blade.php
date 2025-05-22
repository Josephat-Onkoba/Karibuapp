<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #041E42;
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            color: white;
            margin: 0;
        }
        .content {
            padding: 20px;
            background-color: #f9f9f9;
        }
        .button {
            display: inline-block;
            background-color: #041E42;
            color: white !important;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Karibu Check-in System</h1>
    </div>
    
    <div class="content">
        <h2>Reset Your Password</h2>
        <p>Hello,</p>
        <p>You are receiving this email because we received a password reset request for your account.</p>
        
        <p>Click the button below to reset your password:</p>
        
        <a href="{{ $resetLink }}" class="button">Reset Password</a>
        
        <p>If you did not request a password reset, no further action is required.</p>
        
        <p>This password reset link will expire in 60 minutes.</p>
        
        <p>If you're having trouble clicking the button, copy and paste the URL below into your web browser:</p>
        
        <p style="word-break: break-all;">{{ $resetLink }}</p>
    </div>
    
    <div class="footer">
        <p>&copy; {{ date('Y') }} Karibu Check-in System. All rights reserved.</p>
    </div>
</body>
</html> 
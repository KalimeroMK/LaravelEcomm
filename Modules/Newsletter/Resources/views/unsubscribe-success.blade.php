<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unsubscribed Successfully</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .success-icon {
            font-size: 48px;
            color: #27ae60;
            margin-bottom: 20px;
        }
        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
        }
        p {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .email {
            font-weight: bold;
            color: #3498db;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">✅</div>
        <h1>Successfully Unsubscribed</h1>
        <p>You have been successfully unsubscribed from our newsletter.</p>
        @if($email)
            <p>Email: <span class="email">{{ $email }}</span></p>
        @endif
        <p>You will no longer receive promotional emails from us.</p>
        <p>If you change your mind, you can always subscribe again from our website.</p>
        
        <div class="footer">
            <p>Thank you for being part of our community!</p>
            <p>{{ config('app.name') }}</p>
        </div>
    </div>
</body>
</html>

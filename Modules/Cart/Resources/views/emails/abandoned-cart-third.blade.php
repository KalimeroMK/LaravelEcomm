<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Last chance! Your cart expires soon</title>
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
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
        }
        .urgent-badge {
            background: #e74c3c;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            display: inline-block;
            font-weight: bold;
            margin: 20px 0;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        .product-item {
            border: 1px solid #eee;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            display: flex;
            align-items: center;
        }
        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
            margin-right: 15px;
        }
        .product-details {
            flex: 1;
        }
        .product-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .product-price {
            color: #e74c3c;
            font-weight: bold;
        }
        .final-discount {
            background: #8e44ad;
            color: white;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
            margin: 20px 0;
            border: 2px dashed #fff;
        }
        .cta-button {
            display: inline-block;
            background: #e74c3c;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
        .cta-button:hover {
            background: #c0392b;
        }
        .countdown {
            background: #34495e;
            color: white;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
        .unsubscribe {
            margin-top: 20px;
        }
        .unsubscribe a {
            color: #666;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">{{ config('app.name') }}</div>
            <div class="urgent-badge">⏰ LAST CHANCE</div>
            <h1>Your cart expires soon!</h1>
        </div>

        <p>Hi {{ $userName }},</p>

        <p>This is your final reminder! Your cart will expire soon and these items may no longer be available at this price.</p>

        <div class="final-discount">
            🎉 FINAL OFFER: Use code <strong>{{ $discountCode }}</strong> for {{ $discountPercent }}% OFF!
        </div>

        <div class="countdown">
            ⏰ This offer expires in 24 hours
        </div>

        <h2>Your Cart Items:</h2>
        @foreach($cartItems as $item)
            @if($item['product'])
            <div class="product-item">
                @if($item['product']->imageUrl)
                    <img src="{{ $item['product']->imageUrl }}" alt="{{ $item['product']->title }}" class="product-image">
                @endif
                <div class="product-details">
                    <div class="product-title">{{ $item['product']->title }}</div>
                    <div>Quantity: {{ $item['quantity'] }}</div>
                    <div class="product-price">${{ number_format($item['amount'], 2) }}</div>
                </div>
            </div>
            @endif
        @endforeach

        <div style="text-align: center; margin: 30px 0;">
            <strong>Total: ${{ number_format($abandonedCart->total_amount, 2) }}</strong><br>
            <span style="color: #27ae60; font-size: 18px;">With discount: ${{ number_format($abandonedCart->total_amount * (1 - $discountPercent/100), 2) }}</span><br>
            <span style="color: #e74c3c;">You save: ${{ number_format($abandonedCart->total_amount * ($discountPercent/100), 2) }}!</span>
        </div>

        <div style="text-align: center;">
            <a href="{{ $cartUrl }}" class="cta-button">Complete Purchase Now</a>
        </div>

        <p><strong>Don't miss out!</strong> This is your last chance to get these items at this special price. After this email, your cart will be cleared.</p>

        <div class="footer">
            <p>Thank you for choosing {{ config('app.name') }}!</p>
            <div class="unsubscribe">
                <a href="{{ $unsubscribeUrl }}">Unsubscribe from these emails</a>
            </div>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>You left something in your cart!</title>
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
        .cart-icon {
            font-size: 48px;
            margin: 20px 0;
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
        .cta-button {
            display: inline-block;
            background: #3498db;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
        .cta-button:hover {
            background: #2980b9;
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
            <div class="cart-icon">🛒</div>
            <h1>You left something in your cart!</h1>
        </div>

        <p>Hi {{ $userName }},</p>

        <p>We noticed you added some great items to your cart but didn't complete your purchase. Don't worry - your items are still waiting for you!</p>

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
            <strong>Total: ${{ number_format($abandonedCart->total_amount, 2) }}</strong>
        </div>

        <div style="text-align: center;">
            <a href="{{ $cartUrl }}" class="cta-button">Complete Your Purchase</a>
        </div>

        <p>Complete your order now and get your items delivered to your doorstep. We offer fast and secure shipping!</p>

        <div class="footer">
            <p>Thank you for choosing {{ config('app.name') }}!</p>
            <div class="unsubscribe">
                <a href="{{ $unsubscribeUrl }}">Unsubscribe from these emails</a>
            </div>
        </div>
    </div>
</body>
</html>

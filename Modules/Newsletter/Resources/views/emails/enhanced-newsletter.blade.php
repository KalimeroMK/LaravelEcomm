<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} Newsletter</title>
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
            border-bottom: 2px solid #3498db;
            padding-bottom: 20px;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .newsletter-title {
            color: #3498db;
            font-size: 24px;
            margin: 0;
        }
        .post-item {
            border: 1px solid #eee;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            background: #fafafa;
        }
        .post-image {
            width: 100%;
            max-width: 300px;
            height: 200px;
            object-fit: cover;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .post-title {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .post-excerpt {
            color: #666;
            margin-bottom: 15px;
        }
        .read-more-btn {
            display: inline-block;
            background: #3498db;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .read-more-btn:hover {
            background: #2980b9;
        }
        .product-item {
            border: 1px solid #eee;
            padding: 15px;
            margin: 15px 0;
            border-radius: 8px;
            display: flex;
            align-items: center;
            background: #f9f9f9;
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
            color: #2c3e50;
        }
        .product-price {
            color: #e74c3c;
            font-weight: bold;
            font-size: 18px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .social-links {
            margin: 20px 0;
        }
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #3498db;
            text-decoration: none;
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
    <!-- Tracking pixel -->
    @if(isset($analyticsId))
        <img src="{{ route('email.track.open', ['id' => $analyticsId]) }}" width="1" height="1" style="display:none;" />
    @endif

    <div class="container">
        <div class="header">
            <div class="logo">{{ config('app.name') }}</div>
            <h1 class="newsletter-title">📧 Newsletter</h1>
            <p>Stay updated with our latest news and products!</p>
        </div>

        @if(!empty($posts))
            <h2>📰 Latest Blog Posts</h2>
            @foreach($posts as $post)
                <div class="post-item">
                    @if($post->imageUrl)
                        <img src="{{ $post->imageUrl }}" alt="{{ $post->title }}" class="post-image">
                    @endif
                    <h3 class="post-title">{{ $post->title }}</h3>
                    <p class="post-excerpt">{{ Str::limit(strip_tags($post->description), 200) }}</p>
                    <a href="{{ route('email.track.click', ['id' => $analyticsId ?? '', 'url' => route('front.blog-detail', $post->slug)]) }}" class="read-more-btn">
                        Read More →
                    </a>
                </div>
            @endforeach
        @endif

        @if(!empty($products))
            <h2>🛍️ Featured Products</h2>
            @foreach($products as $product)
                <div class="product-item">
                    @if($product->imageUrl)
                        <img src="{{ $product->imageUrl }}" alt="{{ $product->title }}" class="product-image">
                    @endif
                    <div class="product-details">
                        <div class="product-title">{{ $product->title }}</div>
                        <div class="product-price">${{ number_format($product->price, 2) }}</div>
                        <a href="{{ route('email.track.click', ['id' => $analyticsId ?? '', 'url' => route('front.product-detail', $product->slug)]) }}" class="read-more-btn">
                            View Product →
                        </a>
                    </div>
                </div>
            @endforeach
        @endif

        <div class="footer">
            <div class="social-links">
                <a href="{{ route('email.track.click', ['id' => $analyticsId ?? '', 'url' => 'https://facebook.com']) }}">Facebook</a>
                <a href="{{ route('email.track.click', ['id' => $analyticsId ?? '', 'url' => 'https://twitter.com']) }}">Twitter</a>
                <a href="{{ route('email.track.click', ['id' => $analyticsId ?? '', 'url' => 'https://instagram.com']) }}">Instagram</a>
            </div>
            
            <p>Thank you for subscribing to our newsletter!</p>
            <p>{{ config('app.name') }} - Your trusted e-commerce partner</p>
            
            <div class="unsubscribe">
                <a href="{{ route('email.unsubscribe', ['email' => $recipientEmail ?? '', 'id' => $analyticsId ?? '']) }}">
                    Unsubscribe from this newsletter
                </a>
            </div>
        </div>
    </div>
</body>
</html>

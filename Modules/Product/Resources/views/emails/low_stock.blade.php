<!DOCTYPE html>
<html>
<head>
    <title>Low Stock Alert</title>
</head>
<body>
<h1>Low Stock Alert</h1>
<p>The following products have stock less than 10:</p>
<ul>
    @foreach($products as $product)
        <li>{{ $product->title }}: {{ $product->stock }}</li>
    @endforeach
</ul>
</body>
</html>

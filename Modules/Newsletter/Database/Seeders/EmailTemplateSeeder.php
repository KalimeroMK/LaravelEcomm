<?php

declare(strict_types=1);

namespace Modules\Newsletter\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Newsletter\Models\EmailTemplate;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Welcome Email Template',
                'subject' => 'Welcome to {{company}}!',
                'template_type' => 'welcome',
                'is_active' => true,
                'is_default' => true,
                'html_content' => $this->getWelcomeHtmlTemplate(),
                'text_content' => $this->getWelcomeTextTemplate(),
                'settings' => [
                    'show_unsubscribe' => true,
                    'show_company_info' => true,
                    'footer_text' => 'Thank you for joining us!'
                ],
                'preview_data' => [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'company' => config('app.name', 'Our Company')
                ]
            ],
            [
                'name' => 'Newsletter Template',
                'subject' => '{{company}} Newsletter - {{date}}',
                'template_type' => 'newsletter',
                'is_active' => true,
                'is_default' => true,
                'html_content' => $this->getNewsletterHtmlTemplate(),
                'text_content' => $this->getNewsletterTextTemplate(),
                'settings' => [
                    'show_unsubscribe' => true,
                    'show_company_info' => true,
                    'max_products' => 6,
                    'max_posts' => 3
                ],
                'preview_data' => [
                    'name' => 'Subscriber',
                    'email' => 'subscriber@example.com',
                    'company' => config('app.name', 'Our Company'),
                    'date' => now()->format('F Y')
                ]
            ],
            [
                'name' => 'Abandoned Cart Template',
                'subject' => 'Don\'t forget your items at {{company}}!',
                'template_type' => 'abandoned_cart',
                'is_active' => true,
                'is_default' => true,
                'html_content' => $this->getAbandonedCartHtmlTemplate(),
                'text_content' => $this->getAbandonedCartTextTemplate(),
                'settings' => [
                    'show_unsubscribe' => true,
                    'show_company_info' => true,
                    'cart_expiry_hours' => 24
                ],
                'preview_data' => [
                    'name' => 'Customer',
                    'email' => 'customer@example.com',
                    'company' => config('app.name', 'Our Company'),
                    'cart_total' => '$99.99',
                    'cart_items' => 3
                ]
            ],
            [
                'name' => 'Order Confirmation Template',
                'subject' => 'Order Confirmation - {{order_number}}',
                'template_type' => 'order_confirmation',
                'is_active' => true,
                'is_default' => true,
                'html_content' => $this->getOrderConfirmationHtmlTemplate(),
                'text_content' => $this->getOrderConfirmationTextTemplate(),
                'settings' => [
                    'show_unsubscribe' => false,
                    'show_company_info' => true,
                    'show_tracking_info' => true
                ],
                'preview_data' => [
                    'name' => 'Customer',
                    'email' => 'customer@example.com',
                    'company' => config('app.name', 'Our Company'),
                    'order_number' => 'ORD-12345',
                    'order_total' => '$149.99'
                ]
            ],
            [
                'name' => 'Promotional Template',
                'subject' => 'Special Offer: {{promotion_title}}',
                'template_type' => 'promotional',
                'is_active' => true,
                'is_default' => true,
                'html_content' => $this->getPromotionalHtmlTemplate(),
                'text_content' => $this->getPromotionalTextTemplate(),
                'settings' => [
                    'show_unsubscribe' => true,
                    'show_company_info' => true,
                    'promotion_expiry' => 7
                ],
                'preview_data' => [
                    'name' => 'Customer',
                    'email' => 'customer@example.com',
                    'company' => config('app.name', 'Our Company'),
                    'promotion_title' => '50% Off Everything!',
                    'promotion_code' => 'SAVE50'
                ]
            ]
        ];

        foreach ($templates as $templateData) {
            EmailTemplate::updateOrCreate(
                [
                    'name' => $templateData['name'],
                    'template_type' => $templateData['template_type']
                ],
                $templateData
            );
        }
    }

    private function getWelcomeHtmlTemplate(): string
    {
        return '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{company}}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #007bff; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f8f9fa; }
        .footer { background: #6c757d; color: white; padding: 15px; text-align: center; font-size: 12px; }
        .button { display: inline-block; background: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to {{company}}!</h1>
        </div>
        <div class="content">
            <h2>Hello {{name}}!</h2>
            <p>Thank you for joining {{company}}. We\'re excited to have you as part of our community!</p>
            <p>Here\'s what you can expect from us:</p>
            <ul>
                <li>Exclusive offers and promotions</li>
                <li>Latest product updates</li>
                <li>Helpful tips and insights</li>
                <li>Priority customer support</li>
            </ul>
            <p>If you have any questions, feel free to reach out to our support team.</p>
            <a href="#" class="button">Get Started</a>
        </div>
        <div class="footer">
            <p>{{footer_text}}</p>
            <p>© {{company}} - All rights reserved</p>
            <p><a href="#" style="color: #fff;">Unsubscribe</a></p>
        </div>
    </div>
</body>
</html>';
    }

    private function getWelcomeTextTemplate(): string
    {
        return '
Welcome to {{company}}!

Hello {{name}}!

Thank you for joining {{company}}. We\'re excited to have you as part of our community!

Here\'s what you can expect from us:
- Exclusive offers and promotions
- Latest product updates
- Helpful tips and insights
- Priority customer support

If you have any questions, feel free to reach out to our support team.

{{footer_text}}

© {{company}} - All rights reserved
Unsubscribe: [unsubscribe_link]';
    }

    private function getNewsletterHtmlTemplate(): string
    {
        return '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{company}} Newsletter</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #007bff; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f8f9fa; }
        .footer { background: #6c757d; color: white; padding: 15px; text-align: center; font-size: 12px; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin: 20px 0; }
        .product-item { border: 1px solid #ddd; padding: 10px; text-align: center; }
        .button { display: inline-block; background: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{company}} Newsletter</h1>
            <p>{{date}}</p>
        </div>
        <div class="content">
            <h2>Hello {{name}}!</h2>
            <p>Here\'s what\'s new this month:</p>
            
            <h3>Featured Products</h3>
            <div class="product-grid">
                <div class="product-item">
                    <h4>Product 1</h4>
                    <p>Great product description</p>
                    <a href="#" class="button">View Product</a>
                </div>
                <div class="product-item">
                    <h4>Product 2</h4>
                    <p>Another great product</p>
                    <a href="#" class="button">View Product</a>
                </div>
            </div>
            
            <h3>Latest News</h3>
            <p>Stay updated with our latest news and updates...</p>
            
            <a href="#" class="button">Visit Our Store</a>
        </div>
        <div class="footer">
            <p>© {{company}} - All rights reserved</p>
            <p><a href="#" style="color: #fff;">Unsubscribe</a></p>
        </div>
    </div>
</body>
</html>';
    }

    private function getNewsletterTextTemplate(): string
    {
        return '
{{company}} Newsletter - {{date}}

Hello {{name}}!

Here\'s what\'s new this month:

FEATURED PRODUCTS:
- Product 1: Great product description
- Product 2: Another great product

LATEST NEWS:
Stay updated with our latest news and updates...

Visit our store: [store_link]

© {{company}} - All rights reserved
Unsubscribe: [unsubscribe_link]';
    }

    private function getAbandonedCartHtmlTemplate(): string
    {
        return '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Don\'t forget your items!</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #dc3545; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f8f9fa; }
        .footer { background: #6c757d; color: white; padding: 15px; text-align: center; font-size: 12px; }
        .button { display: inline-block; background: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin: 10px 0; }
        .cart-summary { background: white; padding: 15px; border: 1px solid #ddd; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Don\'t forget your items!</h1>
        </div>
        <div class="content">
            <h2>Hello {{name}}!</h2>
            <p>We noticed you left some items in your cart at {{company}}.</p>
            
            <div class="cart-summary">
                <h3>Your Cart Summary</h3>
                <p>Items: {{cart_items}}</p>
                <p>Total: {{cart_total}}</p>
            </div>
            
            <p>Complete your purchase now before these items are no longer available!</p>
            <a href="#" class="button">Complete Purchase</a>
            
            <p>If you have any questions, feel free to contact our support team.</p>
        </div>
        <div class="footer">
            <p>© {{company}} - All rights reserved</p>
            <p><a href="#" style="color: #fff;">Unsubscribe</a></p>
        </div>
    </div>
</body>
</html>';
    }

    private function getAbandonedCartTextTemplate(): string
    {
        return '
Don\'t forget your items at {{company}}!

Hello {{name}}!

We noticed you left some items in your cart.

Your Cart Summary:
- Items: {{cart_items}}
- Total: {{cart_total}}

Complete your purchase now before these items are no longer available!

Complete Purchase: [cart_link]

If you have any questions, feel free to contact our support team.

© {{company}} - All rights reserved
Unsubscribe: [unsubscribe_link]';
    }

    private function getOrderConfirmationHtmlTemplate(): string
    {
        return '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #28a745; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f8f9fa; }
        .footer { background: #6c757d; color: white; padding: 15px; text-align: center; font-size: 12px; }
        .order-details { background: white; padding: 15px; border: 1px solid #ddd; margin: 15px 0; }
        .button { display: inline-block; background: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Order Confirmation</h1>
            <p>Thank you for your order!</p>
        </div>
        <div class="content">
            <h2>Hello {{name}}!</h2>
            <p>Your order has been confirmed and is being processed.</p>
            
            <div class="order-details">
                <h3>Order Details</h3>
                <p><strong>Order Number:</strong> {{order_number}}</p>
                <p><strong>Total Amount:</strong> {{order_total}}</p>
                <p><strong>Order Date:</strong> {{order_date}}</p>
            </div>
            
            <p>You will receive a shipping confirmation email once your order is dispatched.</p>
            <a href="#" class="button">Track Your Order</a>
            
            <p>If you have any questions about your order, please contact our support team.</p>
        </div>
        <div class="footer">
            <p>© {{company}} - All rights reserved</p>
        </div>
    </div>
</body>
</html>';
    }

    private function getOrderConfirmationTextTemplate(): string
    {
        return '
Order Confirmation - {{order_number}}

Hello {{name}}!

Your order has been confirmed and is being processed.

Order Details:
- Order Number: {{order_number}}
- Total Amount: {{order_total}}
- Order Date: {{order_date}}

You will receive a shipping confirmation email once your order is dispatched.

Track Your Order: [tracking_link]

If you have any questions about your order, please contact our support team.

© {{company}} - All rights reserved';
    }

    private function getPromotionalHtmlTemplate(): string
    {
        return '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Special Offer</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #ffc107; color: #000; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f8f9fa; }
        .footer { background: #6c757d; color: white; padding: 15px; text-align: center; font-size: 12px; }
        .promo-code { background: #dc3545; color: white; padding: 10px; text-align: center; font-size: 18px; font-weight: bold; margin: 15px 0; }
        .button { display: inline-block; background: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Special Offer! 🎉</h1>
            <h2>{{promotion_title}}</h2>
        </div>
        <div class="content">
            <h2>Hello {{name}}!</h2>
            <p>We have an exclusive offer just for you!</p>
            
            <div class="promo-code">
                Use Code: {{promotion_code}}
            </div>
            
            <p>This offer is valid for a limited time only. Don\'t miss out!</p>
            <a href="#" class="button">Shop Now</a>
            
            <p>Terms and conditions apply. Offer expires in {{promotion_expiry}} days.</p>
        </div>
        <div class="footer">
            <p>© {{company}} - All rights reserved</p>
            <p><a href="#" style="color: #fff;">Unsubscribe</a></p>
        </div>
    </div>
</body>
</html>';
    }

    private function getPromotionalTextTemplate(): string
    {
        return '
🎉 Special Offer! 🎉

{{promotion_title}}

Hello {{name}}!

We have an exclusive offer just for you!

Use Code: {{promotion_code}}

This offer is valid for a limited time only. Don\'t miss out!

Shop Now: [shop_link]

Terms and conditions apply. Offer expires in {{promotion_expiry}} days.

© {{company}} - All rights reserved
Unsubscribe: [unsubscribe_link]';
    }
}

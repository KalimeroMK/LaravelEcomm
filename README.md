# E-commerce website in  Laravel 10

## Features :

====== FRONT-END =======

- Responsive Layout
- Shopping Cart, Wishlist, Product Reviews
- Coupons & Discounts
- Product attributes: cost price, promotion price, stock, size...
- Blog: category, tag, content, web page
- Module/Extension: Shipping, payment, discount, ...
- Upload manager: banner, images,..
- Bundles module
- SEO support: customer URL
- Newsletter management
- Contact forms with the real-time notification (Laravel Pusher)
- Related Products, Recommendations for you in our categories
- A Product search form
- Laravel Socialite implement(Facebook, Google & twitter) & Customer login
- Product Share and follow from different social platform...
- Payment integration(Paypal, Stripe, Casys)
- Multi-level comment system many more......

======= ADMIN =======

- Admin roles, permission
- Product manager
- Media manager using unisharp laravel file manager
- Banner manager
- Bundles manager
- Order management
- Category management
- Brand management
- Shipping Management
- Review Management
- Blog, Category & Tag manager
- User Management
- Role
- Permission
- Coupon Management
- System config: email setting, info shop, maintain status,...
- Line Chart & Pie chart ...
- Generate order in pdf form...
- Real time message & notification
- Translation manager
- Impersonate
- Activity log
- IP blocker
- Profile Settings Many more....

======= USER DASHBOARD =======

- Order management
- Review Management
- Comment Management
- Profile Settings

======= SECURITY =======

- Google 2FA

======= Caching =======

- Redis

## Screenshots :

![screencapture-e-shop-loc-admin-2020-08-15-15_47_37](https://user-images.githubusercontent.com/29488275/90719413-13b82200-e2d4-11ea-8ca0-f0e5551c4c9d.png)

![screencapture-e-shop-loc-admin-category-2020-08-14-19_45_55](https://user-images.githubusercontent.com/29488275/90719470-3813fe80-e2d4-11ea-8f63-e6001855a945.png)

![screencapture-e-shop-loc-admin-product-2020-08-14-19_44_49](https://user-images.githubusercontent.com/29488275/90719534-61348f00-e2d4-11ea-8a81-409daee0ad94.png)

![screencapture-e-shop-loc-user-order-show-1-2020-08-14-18_57_06](https://user-images.githubusercontent.com/29488275/90719557-71e50500-e2d4-11ea-97cf-befb1d525643.png)

![screencapture-e-shop-loc-user-profile-2020-08-14-18_58_06](https://user-images.githubusercontent.com/29488275/90719563-7a3d4000-e2d4-11ea-9e6a-56caac13b146.png)

![screencapture-e-shop-loc-admin-post-2020-08-14-16_00_07](https://user-images.githubusercontent.com/29488275/90719572-81644e00-e2d4-11ea-9fe5-3325ab427f88.png)

![screencapture-e-shop-loc-2020-08-14-18_19_46](https://user-images.githubusercontent.com/29488275/90719631-a1940d00-e2d4-11ea-89a3-eb36960d687d.png)

![screencapture-e-shop-loc-blog-2020-08-14-18_36_21](https://user-images.githubusercontent.com/29488275/90719648-a8228480-e2d4-11ea-9c57-5ed7aef50e26.png)

![screencapture-e-shop-loc-blog-detail-where-can-i-get-some-2020-08-14-18_43_01](https://user-images.githubusercontent.com/29488275/90719658-ace73880-e2d4-11ea-9cb2-13f2b3b0c4d2.png)

![screencapture-e-shop-loc-product-track-2020-08-14-18_51_07](https://user-images.githubusercontent.com/29488275/90719682-bbcdeb00-e2d4-11ea-8e4e-7d6bfab1c421.png)

## Set up :

1. Clone the repo and cd into it
2. composer install
3. Rename or copy .env.example file to .env
4. php artisan key:generate
5. Set your database credentials in your .env file
6. Set your Braintree credentials in your .env file if you want to use PayPal
7. Run php artisan migrate:fresh --seed
8. npm install
9. npm run watch
10. run command[laravel file manager]:- php artisan storage:link
11. Edit .env file :- remove APP_URL
10. php artisan serve or use virtual host
11. Visit localhost:8000 in your browser
12. Visit /admin if you want to access the admin panel. Admin Email/Password: superadmin@mail.com/password. User
    Email/Password:
    client@mail.com/password

### Requirements installation and configuration for docker

* **Docker**
* **In project root run**: docker-compose up -d.
* **Install laravel packages**: composer install
* **ENV**: rename DB_HOST=127.0.0.1 to DB_HOST=mysql
* **Container ssh**: docker-compose exec app sh
* **Run migrations**: php artisan:migrate:fresh --seed.

### Demo page:https://e-comm.mk

<p style="text-align:center">Thank You so much for your time !!!</p>


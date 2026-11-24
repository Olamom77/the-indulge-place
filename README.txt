THE INDULGE PLACE - SETUP (PHP 8+, MySQL/MariaDB, HTTPS recommended)

REQUIRED CONFIGURATION (nothing below is pre-filled; the site cannot take real orders until it is)
1. Database: import schema.sql, then set DB_HOST / DB_NAME / DB_USER / DB_PASS in config.php.
2. Admin password: run  php -r "echo password_hash('YOUR_PASSWORD',PASSWORD_DEFAULT);"
   and paste the result into ADMIN_HASH in config.php. Admin login is disabled while it is empty. Admin: /admin.php
3. WhatsApp ordering: $S['whatsapp'] in config.php is currently set to 2347075203145 (the first phone number on the menu) - change it if orders should go to a different WhatsApp number.
   Orders are saved to the database, then WhatsApp opens with the order summary. The customer must press Send.
4. Business details: $S phone, address, email, hours, currency symbol, Google Maps embed URL ($S['map']).
5. Images/video (all supplied by the restaurant, already optimised in assets/img/):
   hero-1..4.jpg = homepage slideshow, gallery-1..12.jpg = gallery, about.jpg, team.jpg,
   reel-1..3.mp4 + reel-1..3.jpg = "From our kitchen" videos (muted loops, only downloaded when tapped).
   Menu item photos: set a path (e.g. assets/img/gallery-2.jpg) or https URL per item in /admin.php.
   The full menu (210 items, both menus, in Naira) is loaded by schema.sql; edit it in /admin.php.

NOT INCLUDED: online payment (no gateway is configured; payment is arranged with the restaurant), email notifications,
image upload in admin, CAPTCHA/rate limiting on the public order and contact forms.

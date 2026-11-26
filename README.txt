THE INDULGE PLACE - SETUP (PHP 8+, MySQL/MariaDB, HTTPS recommended)

RAILWAY DEPLOYMENT
1. In Railway, create or open a project and choose Deploy from GitHub Repo.
2. Select Olamom77/the-indulge-place. Railway will build the included Dockerfile.
3. Add a MySQL service to the same Railway project.
4. In the PHP web service, add references to the MySQL service variables:
   MYSQLHOST, MYSQLPORT, MYSQLDATABASE, MYSQLUSER, MYSQLPASSWORD.
   config.php reads these automatically. Do not paste database passwords into GitHub.
5. Open the Railway MySQL service's query console and run the contents of schema.sql against the database named by MYSQLDATABASE. The schema creates the required tables and inserts the 210 menu records; it no longer tries to create or switch databases.
6. Generate a public domain for the PHP web service. Use that domain as the live website URL.
7. Test https://YOUR-DOMAIN/api.php?a=menu . It should return the menu as JSON.

The GitHub Pages address is not suitable for the complete site because GitHub Pages does not run PHP or connect to MySQL. Use the Railway public domain for the live PHP site.

REQUIRED CONFIGURATION
- Database: import schema.sql, then set the Railway MySQL variables above (or DB_HOST / DB_PORT / DB_NAME / DB_USER / DB_PASS on another host).
- Admin password: run php -r "echo password_hash('YOUR_PASSWORD',PASSWORD_DEFAULT);" and set the resulting value as ADMIN_HASH in the web service variables. Set ADMIN_USER too if desired. Admin: /admin.php. After login, the dashboard shows menu counts, orders and messages. Admin login is disabled while ADMIN_HASH is empty; configure ADMIN_USER and ADMIN_HASH in Railway service Variables before using it.
- WhatsApp ordering: $S['whatsapp'] in config.php is currently set to 2347075203145. Change it if orders should go to a different WhatsApp number.
- Business details: $S phone, address, email, hours, currency symbol and Google Maps embed URL ($S['map']).
- Images/video are in assets/img/. The full menu (210 items, both menus, in Naira) is loaded by schema.sql; edit it in /admin.php. If MySQL is temporarily unavailable or the menu table is empty, the public website displays a built-in menu preview, but placing orders still requires a working database.

NOT INCLUDED: online payment (no gateway is configured; payment is arranged with the restaurant), email notifications, image upload in admin, CAPTCHA/rate limiting on the public order and contact forms.

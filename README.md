Markei Security and Protection
==============================

Install the plugin via Composer

    composer require markei/wordpress-security-protection

Activate the plugin in WordPress admin

Add the next two lines to `wordpress/wp-config.php` above `require_once(ABSPATH . 'wp-settings.php');`

    define('MARKEI_SECURITY_PROTECTION_HIDEBACKEND_URL', '/my-secret-cms');
    define('MARKEI_SECURITY_PROTECTION_UPDATEINFO_SECRET', 'super-secret-key');
    define('MARKEI_SECURITY_PROTECTION_BLOCKXMLRPC', true);
    
Replace `/my-secret-cms` with an URL you like. Do **not** use `my-secret-cms`, `wp-admin` or `wp-login`! Mention the slash in the beginning. Replace `super-secret-key` with a [random long value](http://password.markei.nl/randomsave.txt).

Log out and use your new URL to relogin.

The API for update status report is located at `http://www.my-wordpress.tld/.markei/security-and-protection/update-state?secret=super-secret-key`
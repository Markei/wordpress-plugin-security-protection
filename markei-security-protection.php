<?php
/*
Plugin Name:  Markei.nl WordPress Security & Protection
Plugin URI:   https://github.com/markei/wordpress-plugin-security-protection/
Description:  Security & protection improvements: login event history, prevent login brute force, hide backend, update status API
Version:      1.0.0
Author:       Markei.nl
Author URI:   https://www.markei.nl
License:      MIT
License URI:  https://opensource.org/licenses/MIT
Text Domain:  markei-security-protection
Domain Path:  /languages
*/

defined('ABSPATH') or die('Initialize WordPress-core first');

require_once __DIR__ . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrate.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'loginhistory' . DIRECTORY_SEPARATOR . 'track.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'autoban' . DIRECTORY_SEPARATOR . 'evaluate_ip.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'autoban' . DIRECTORY_SEPARATOR . 'is_blocked.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'notify' . DIRECTORY_SEPARATOR . 'mailadmins.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'hidebackend' . DIRECTORY_SEPARATOR . 'check_token.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'hidebackend' . DIRECTORY_SEPARATOR . 'generate_token.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'updatestate' . DIRECTORY_SEPARATOR . 'report.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'blockxmlrpc' . DIRECTORY_SEPARATOR . 'xmlrpcdie.php';

// database
register_activation_hook(__FILE__, function () {
    markei_security_protection_database_migrate();
});
add_action('plugins_loaded', function () {
    markei_security_protection_database_migrate();
});

// login history
add_action('wp_login', function ($username) {
    markei_security_protection_loginhistory_track($username, true);
}, 10, 2);
add_action('wp_login_failed', function ($username) {
    markei_security_protection_loginhistory_track($username, false);
});

// auto ban
add_action('wp_login_failed', function () {
    // if ip is not blocked yet
    if (markei_security_protection_autoban_is_blocked($_SERVER['REMOTE_ADDR']) === null) {
        markei_security_protection_autoban_evaluate_ip($_SERVER['REMOTE_ADDR']);
    }
});
add_action('login_init', function () {
    $block = markei_security_protection_autoban_is_blocked($_SERVER['REMOTE_ADDR']);
    if ($block !== null) {
        wp_die('Your IP address is blocked');
    }
});

// hide backend
add_action('login_init', function () {
    if (markei_security_protection_hidebackend_check_token() === false) {
        wp_die('Direct access to the login page is prohibited');
    }
});
add_action('init', function () {
    if (defined('MARKEI_SECURITY_PROTECTION_HIDEBACKEND_URL') === true) {
        if (rtrim($_SERVER['REQUEST_URI'], '/') === rtrim(MARKEI_SECURITY_PROTECTION_HIDEBACKEND_URL, '/')) {
            markei_security_protection_hidebackend_generate_token();
            wp_redirect('/wp-admin/', 302);
            wp_die('Redirecting to <a href="/wp-admin/">/wp-admin/</a>', 'Redirecting', 302);
        }
        if (substr($_SERVER['REQUEST_URI'], 0, 9) === '/wp-admin' || substr($_SERVER['REQUEST_URI'], 0, 9) === '/wp-login') {
            if (markei_security_protection_hidebackend_check_token() === false) {
                wp_die('Direct access to the backend is prohibited. Please contact support for the right login URL.', 'Admin', 403);
            }
        }
    }
}, 1000);

// update state
add_action('init', function () {
    if (defined('MARKEI_SECURITY_PROTECTION_UPDATEINFO_SECRET') === true) {
        if (substr($_SERVER['REQUEST_URI'], 0, 45) === '/.markei/security-and-protection/update-state') {
            if (isset($_GET['secret']) === false || $_GET['secret'] !== MARKEI_SECURITY_PROTECTION_UPDATEINFO_SECRET) {
                wp_die('Secret invalid', 'Security and protection', 403);
            }
            $report = markei_security_protection_updatestate_report();
            header('Content-type: text/plain');
            define('DOING_AJAX', true);
            wp_die(json_encode($report, JSON_PRETTY_PRINT));
        }
    }
});

// add logo on login page
add_action('login_enqueue_scripts', function () {
    $baseUrl = plugin_dir_url(__FILE__);
    echo PHP_EOL . '<style type="text/css"> body { background: #f1f1f1 url(' . $baseUrl . 'assets/login-background.png) no-repeat bottom left scroll !important; } </style>' . PHP_EOL;
});

// block xmlrpc
add_filter('wp_die_xmlrpc_handler', function () {
    return 'markei_security_protection_blockxmlrpc_xmlrpcdie';
});
add_action('init', function () {
    if (defined('MARKEI_SECURITY_PROTECTION_BLOCKXMLRPC') === true || MARKEI_SECURITY_PROTECTION_BLOCKXMLRPC === true) {
        if (substr($_SERVER['REQUEST_URI'], 0, 11) === '/xmlrpc.php') {
            wp_die('XML-RPC access disabled', '', 404);
        }
    }
});

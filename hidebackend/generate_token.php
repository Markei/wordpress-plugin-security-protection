<?php

defined('ABSPATH') or die('Initialize WordPress-core first');

function markei_security_protection_hidebackend_generate_token()
{
    $expiry = time() + (2 * 24 * 60 * 60);
    $secret = sha1(\LOGGED_IN_SALT . $expiry . \LOGGED_IN_SALT);
    
    $secure = isset($_SERVER["HTTPS"]) && (strtolower($_SERVER["HTTPS"]) === 'on') ? true : false;
    
    setcookie('wordpress_hidebackend_token', $expiry . '|' . $secret, 0, '/', null, $secure, true);
}
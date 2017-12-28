<?php

defined('ABSPATH') or die('Initialize WordPress-core first');

function markei_security_protection_hidebackend_check_token()
{
    if (isset($_COOKIE['wordpress_hidebackend_token']) === false) {
        return false;
    }
 
    $parts = explode('|', $_COOKIE['wordpress_hidebackend_token']);
    if (count($parts) !== 2) {
        return false;
    }
    
    $expiry = $parts[0];
    $secret = $parts[1];
    
    $expectedSecret = sha1(\LOGGED_IN_SALT . $expiry . \LOGGED_IN_SALT);
    
    if ($expectedSecret !== $secret) {
        return false;
    }
    
    if (time() > $expiry) {
        return false;
    }
    
    return true;
}
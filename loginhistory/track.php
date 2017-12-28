<?php

defined('ABSPATH') or die('Initialize WordPress-core first');

function markei_security_protection_loginhistory_track($username, $success)
{
    global $wpdb;
    
    $wpdb->insert(
        $wpdb->prefix . 'markei_loginhistory',
        [
            'datetime' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER['REMOTE_ADDR'],
            'user' => substr($username, 0, 255),
            'useragent' => substr((isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : ''), 0, 255),
            'success' => $success ? 1 : 0
        ],
        [
            '%s',
            '%s',
            '%s',
            '%s',
            '%d',
        ]
    );
}
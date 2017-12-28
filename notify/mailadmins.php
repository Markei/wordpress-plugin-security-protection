<?php

defined('ABSPATH') or die('Initialize WordPress-core first');

function markei_security_protection_notify_mailadmins($subject, $message)
{
    global $wpdb;
    
    $addresses = [strtolower(get_bloginfo('admin_email')) => get_bloginfo('wpurl')];
    
    $users = get_users(['role__in' => ['administrator']]);
    foreach ($users as $user) {
        $addresses[strtolower($user->user_email)] = $user->display_name;
    }
    
    foreach ($addresses as $address => $name) {
        wp_mail($address, $subject, str_replace('{{ name }}', $name, $message));
    }
}
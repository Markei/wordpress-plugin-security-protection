<?php

defined('ABSPATH') or die('Initialize WordPress-core first');

function markei_security_protection_autoban_is_blocked($ip)
{
    global $wpdb;
    
    $sql = 'SELECT * FROM ' . $wpdb->prefix . 'markei_ipban' . ' WHERE ip = %s AND NOW() > start AND (end IS NULL OR end > NOW());';
    $block = $wpdb->get_row($wpdb->prepare($sql, $ip), ARRAY_A);
    
    return $block; // return NULL if there is no block, or if blocked the information for the ip ban
}
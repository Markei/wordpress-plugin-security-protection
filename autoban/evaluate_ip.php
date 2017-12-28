<?php

defined('ABSPATH') or die('Initialize WordPress-core first');

function markei_security_protection_autoban_evaluate_ip($ip)
{
    global $wpdb;
    
    $sql = 'SELECT COUNT(id) FROM ' . $wpdb->prefix . 'markei_loginhistory' . ' WHERE ip = %s AND success = 0 AND datetime BETWEEN (NOW() - INTERVAL 24 HOUR) AND NOW();';
    $loginFailuresLast24h = $wpdb->get_var($wpdb->prepare($sql, $ip));
    
    if ($loginFailuresLast24h > 10) {
        $wpdb->insert(
            $wpdb->prefix . 'markei_ipban',
            [
                'ip' => $ip,
                'start' => date('Y-m-d H:i:s'),
                'end' => date('Y-m-d H:i:s', time() + (60 * 60 * 24)),
                'remarks' => 'Automaticly blocked for 24 hours due to too many login failures'
            ],
            [
                '%s',
                '%s',
                '%s',
                '%s',
            ]
        );
        
        $sql = 'SELECT * FROM ' . $wpdb->prefix . 'markei_loginhistory' . ' WHERE ip = %s AND success = 0 AND datetime BETWEEN (NOW() - INTERVAL 24 HOUR) AND NOW();';
        $loginFailures = $wpdb->get_results($wpdb->prepare($sql, [$ip]), ARRAY_A);
        
        $subject = '[' . get_bloginfo('name') . '] IP address ' . $ip . ' automaticly blocked';
        $message  = 'Hi {{ name }},' . PHP_EOL . PHP_EOL;
        $message .= 'After ' . $loginFailuresLast24h . ' login failures from ' . $ip . ' the address is automaticly blocked by us for new logins for the next 24 hours.' . PHP_EOL;
        $message .= 'If you think this is a mistake please contact your technical support for removing the ip address' . PHP_EOL . PHP_EOL;
        $message .= 'Failed login last 24 hours from ' . $ip . PHP_EOL . PHP_EOL;
        $message .= 'Date/Time | Username' . PHP_EOL;
        foreach ($loginFailures as $failure) {
            $message .= $failure['datetime'] . ' | ' . $failure['user'] . PHP_EOL;
        }
        $message .= PHP_EOL . PHP_EOL;
        $message .= 'Sincerely, Markei Security and Protection' . PHP_EOL;
        $message .= get_bloginfo('wpurl');
        markei_security_protection_notify_mailadmins($subject, $message);
    }
}
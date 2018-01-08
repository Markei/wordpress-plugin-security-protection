<?php

defined('ABSPATH') or die('Initialize WordPress-core first');

function markei_security_protection_blockxmlrpc_xmlrpcdie($message, $title = '', $args = array()) {
    global $wp_xmlrpc_server;
    $defaults = array( 'response' => 500 );

    $r = wp_parse_args($args, $defaults);

    if ( $wp_xmlrpc_server ) {
        $error = new IXR_Error( $r['response'] , $message);
        $wp_xmlrpc_server->output( $error->getXml() );
        die();
    } else {
        $function = apply_filters( 'wp_die_handler', '_default_wp_die_handler' );
        call_user_func( $function, $message, $title, $args );
    }
}
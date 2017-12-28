<?php

defined('ABSPATH') or die('Initialize WordPress-core first');

function markei_security_protection_database_migrate() {
    global $wpdb;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    
    $currentVersion = get_option('markei-security_protection-db_version', 0);
    
    if ($currentVersion < 1) {
        $sql = "CREATE TABLE " . $wpdb->prefix . 'markei_loginhistory' . "
            (
                `id` BIGINT NOT NULL AUTO_INCREMENT,
                `datetime` DATETIME NOT NULL,
                `ip` VARCHAR(50) NOT NULL,
                `user` VARCHAR(255) NULL,
                `useragent` VARCHAR(255) NULL,
                `success` TINYINT NOT NULL DEFAULT '0',
                `remarks` VARCHAR(255) NULL,
                PRIMARY KEY (`id`),
                INDEX `ip` (`ip`)
            )
            COLLATE='utf8_general_ci'
        ;";
        dbDelta($sql);
        add_option('markei_security_protection_db_version', 1);
    }
    
    if ($currentVersion < 2) {
        $sql = "CREATE TABLE " . $wpdb->prefix . 'markei_ipban' . "
            (
                `id` BIGINT NOT NULL AUTO_INCREMENT,
                `ip` VARCHAR(50) NOT NULL,
                `start` DATETIME NOT NULL,
                `end` DATETIME NULL,
                `remarks` VARCHAR(255) NULL,
                PRIMARY KEY (`id`),
                INDEX `ip` (`ip`)
            )
            COLLATE='utf8_general_ci'
        ;";
        dbDelta($sql);
        update_option('markei_security_protection_db_version', 2);
    }
}
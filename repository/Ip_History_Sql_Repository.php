<?php
declare(strict_types = 1);

class Ip_History_Sql_Repository
{
    public static function find_from_last_week(int $per_page, int $page_number): array
    {
        global $wpdb;

        $per_page = min($per_page, 200);

        $sql = "SELECT * FROM {$wpdb->prefix}markei_loginhistory WHERE datetime >= CURRENT_DATE - 6 AND DATETIME <= NOW() LIMIT " . esc_sql($per_page) . " OFFSET " . esc_sql($page_number * $per_page);

        return $wpdb->get_results($sql, 'ARRAY_A');
    }

    public static function count_records(): int
    {
        global $wpdb;

        $sql = "SELECT count(*) FROM {$wpdb->prefix}markei_loginhistory WHERE datetime >= CURRENT_DATE - 6 AND DATETIME <= NOW()";

        return (int) $wpdb->get_var($sql);
    }
}
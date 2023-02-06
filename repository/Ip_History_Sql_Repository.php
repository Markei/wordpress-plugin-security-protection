<?php
declare(strict_types = 1);

class Ip_History_Sql_Repository
{
    public static function find_from_last_week(int $per_page, int $page_number): array
    {
        global $wpdb;

        $offset = $page_number * $per_page;

        $sql = "SELECT * FROM {$wpdb->prefix}markei_loginhistory WHERE DATE(datetime) >= CURDATE() - INTERVAL 7 DAY LIMIT {$per_page} OFFSET {$offset}";

        return $wpdb->get_results($sql, 'ARRAY_A');
    }

    public static function count_records(): int
    {
        global $wpdb;

        $sql = "SELECT count(*) FROM {$wpdb->prefix}markei_loginhistory WHERE DATE(datetime) >= CURDATE() - INTERVAL 7 DAY";

        return absint($wpdb->get_var($sql));    // Waarom hier absint en niet (int)
    }
}
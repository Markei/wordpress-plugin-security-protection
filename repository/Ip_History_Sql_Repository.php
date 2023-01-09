<?php
declare(strict_types = 1);

// navragen of static methods hier wel handig zijn
class Ip_History_Sql_Repository
{
    public static function FindFromLastWeek(int $per_page, int $page_number): array
    {
        global $wpdb;

        $offset = ($page_number - 1) * $per_page;

        $sql = "SELECT * FROM {$wpdb->prefix}markei_loginhistory WHERE DATE(datetime) >= CURDATE() - INTERVAL 7 DAY LIMIT {$per_page} OFFSET {$offset}";

        $result = $wpdb->get_results($sql, 'ARRAY_A');

        return $result;
    }

    public static function count_records(): string
    {
        global $wpdb;
        
        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}markei_loginhistory";
        
        return $wpdb->get_var($sql);
    }
}
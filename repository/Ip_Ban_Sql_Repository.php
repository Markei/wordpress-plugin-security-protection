<?php
declare(strict_types = 1);

// navragen of static methods hier wel handig zijn
// Vragen hoe ik $wpdb niet meerdere keren hoe in te laden
class Ip_Ban_Sql_Repository
{
    public static function findAll(int $per_page, int $page_number): array
    {
        global $wpdb;

        $offset = ($page_number - 1) * $per_page;
        
        $sql = "SELECT * FROM {$wpdb->prefix}markei_ipban LIMIT {$per_page} OFFSET {$offset}";
        
        $result = $wpdb->get_results($sql, 'ARRAY_A');

        return $result;
    }

    public static function delete(int $id): void
    {
        global $wpdb;

        $wpdb->delete("{$wpdb->prefix}markei_ipban", ['id' => $id], ['%d']);
    }

    public static function count_records(): string
    {
        global $wpdb;
        
        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}markei_ipban";
        
        return $wpdb->get_var($sql);
    }
}
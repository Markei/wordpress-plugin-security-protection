<?php
declare(strict_types = 1);

class Ip_Ban_Sql_Repository
{
    public static function find_all(int $per_page, int $page_number): array
    {
        global $wpdb;

        $per_page = min($per_page, 200);

        $sql = "SELECT * FROM {$wpdb->prefix}markei_ipban LIMIT " . esc_sql($per_page) . " OFFSET " . esc_sql($page_number * $per_page);

        $result = $wpdb->get_results($sql, 'ARRAY_A');

        return $result;
    }

    public static function delete(int $id): void
    {
        global $wpdb;

        $wpdb->delete("{$wpdb->prefix}markei_ipban", ['id' => esc_sql($id)], ['%d']);
    }

    public static function count_records(): int
    {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}markei_ipban";

        return (int) ($wpdb->get_var($sql));
    }
}
<?php
declare(strict_types = 1);

require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'repository' . DIRECTORY_SEPARATOR . 'Ip_Ban_Sql_Repository.php';

class Ip_Ban_List_Table extends WP_List_Table
{
    const PER_PAGE = 5;

    public function get_columns(): array
    {
        $columns = [
            'id' => 'Id',
            'cb' => '<input type="checkbox"/>',
            'ip' => 'IP',
            'start' => 'Start',
            'end' => 'Eind',
            'action'=> 'Acties'
        ];
        
        return $columns;
    }

    public function get_hidden_columns(): array
    {
        $hidden_columns = [
            'id'
        ];
        
        return $hidden_columns;
    }

    public function column_cb($item): string
    {
        return sprintf('<input type="checkbox" name="bulk-delete[]" value="%s"/>', esc_attr($item['id']));
    }

    public function column_default($item, $column_name): string
    {
        $delete_nonce = wp_create_nonce('wp_delete_ip_address');

        switch ($column_name) {
            case 'id':
            case 'ip':
            case 'start':
            case 'end':
                return esc($item[$column_name]);
            case 'action':
                return sprintf('<a href="?page=%s&action=%s&id=%s&_wpnonce=%s">Delete</a>', esc_attr($_REQUEST['page']), 'delete', esc_attr($item['id']), esc_attr($delete_nonce));
            default:
                return 'Waarde Onbekend';
        }
    }

    public function get_bulk_actions(): array
    {
        $actions = [
            'bulk-delete' => 'Delete'
        ];

        return $actions;
    }

    public function process_action(): void
    {
        if (isset($_REQUEST['_wpnonce'])) {
            $nonce  = filter_input(INPUT_POST, '_wpnonce', FILTER_UNSAFE_RAW);

            if ('delete' === $this->current_action()) {
                if (!wp_verify_nonce($nonce, 'wp_delete_ip_address')) {
                    die('Invalid security token!');
                }

                Ip_Ban_Sql_Repository::delete((int) $_GET['id']);
            }
        }
    }

    public function process_bulk_action(): void
    {
        if (isset($_POST['_wpnonce'])) {
            $nonce  = filter_input(INPUT_POST, '_wpnonce', FILTER_UNSAFE_RAW);
            $action = 'bulk-' . $this->_args['plural'];

            if (!wp_verify_nonce($nonce, $action)) {
                die('Invalid security token!');
            }

            if ((isset($_POST['action']) && $_POST['action'] === 'bulk-delete')) {
                $delete_ip_address_ids = $_POST['bulk-delete'];
    
                foreach ($delete_ip_address_ids as $ip_address_id) {
                    Ip_Ban_Sql_Repository::delete((int) $ip_address_id);
                }
            }
        }
    }

    public function prepare_items(): void
    {
        $visible_columns = $this->get_columns();
        $hidden_columns = $this->get_hidden_columns();

        $this->_column_headers = [$visible_columns, $hidden_columns];

        $total_items = Ip_Ban_Sql_Repository::count_records();

        $this->set_pagination_args([
            'total_items' => $total_items,
            'total_pages' => (int) ceil($total_items / self::PER_PAGE),
            'per_page' => self::PER_PAGE
        ]);

        $this->process_action();
        $this->process_bulk_action();

        $page_number = $this->get_pagenum() - 1;

        $this->items = Ip_Ban_Sql_Repository::find_all(self::PER_PAGE, $page_number);
    }
}
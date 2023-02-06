<?php
declare(strict_types = 1);

require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'repository' . DIRECTORY_SEPARATOR . 'Ip_History_Sql_Repository.php';

class Ip_History_List_Table extends WP_List_Table
{
    const PER_PAGE = 5;

    public function get_columns(): array
    {
        $columns = array(
            'ip' => 'IP',
            'request_uri' => 'Request URI',
            'user' => 'Gebruiker',
            'useragent' => 'Gebruikersagent',
            'datetime' => 'Datum',
            'success'=> 'Status'
        );
        
        return $columns;
    }

    public function column_default($item, $column_name): string
    {
        switch ($column_name) {
            case 'ip':
            case 'request_uri':
            case 'user':
            case 'useragent':
            case 'datetime':
                return esc($item[$column_name]);
            case 'success':
                return $item[$column_name] == 1 ? 'Succesvol' : 'Mislukt';
            default:
                return 'Waarde onbekend';
        }
    }

    public function prepare_items(): void
    {
        $this->_column_headers = [$this->get_columns()];

        $total_items = Ip_History_Sql_Repository::count_records(self::PER_PAGE, $this->get_pagenum());

        $this->set_pagination_args([
            'total_items' => $total_items,
            'total_pages' => (int) ceil($total_items / self::PER_PAGE),
            'per_page' => self::PER_PAGE
        ]);

        $this->process_action();
        $this->process_bulk_action();

        $page_number = $this->get_pagenum() - 1;

        $this->items = Ip_History_Sql_Repository::find_from_last_week(self::PER_PAGE, $page_number);
    }
}
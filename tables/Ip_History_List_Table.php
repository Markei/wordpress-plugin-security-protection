<?php
declare(strict_types = 1);

require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'repository' . DIRECTORY_SEPARATOR . 'Ip_History_Sql_Repository.php';

class Ip_History_List_Table extends WP_List_Table
{
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
                return $item[$column_name];
            case 'success':
                return $item[$column_name] == 1 ? 'Succesvol' : 'Mislukt';
            default:
                return 'Waarde onbekend';
        }
    }

    public function prepare_items(): void
    {
        $this->_column_headers = [$this->get_columns()];

        $total_items = Ip_History_Sql_Repository::count_records(5, $this->get_pagenum());

        $this->set_pagination_args([
            'total_items' => $total_items,
            'total_pages' => intval(ceil($total_items / 5)),
            'per_page' => 5
        ]);

        $this->process_action();
        $this->process_bulk_action();

        $this->items = Ip_History_Sql_Repository::FindFromLastWeek(5, $this->get_pagenum());
    }
}
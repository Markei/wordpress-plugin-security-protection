<?php
declare(strict_types=1);

require_once(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'tables' . DIRECTORY_SEPARATOR . 'Ip_History_List_Table.php');

$ip_history_list_table = new Ip_History_List_Table();
$ip_history_list_table->prepare_items();

echo '<form method="post">
        <h3>Login geschiedenis</h3>';
        $ip_history_list_table->display();
echo '</form>';
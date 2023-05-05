<?php
declare(strict_types=1);

require_once(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'tables' . DIRECTORY_SEPARATOR . 'Ip_Ban_List_Table.php');

$ip_ban_list_table = new Ip_Ban_List_Table();
$ip_ban_list_table->prepare_items();

echo '<form method="post">
        <h3>Geblokkeerde ip-adressen</h3>';
        $ip_ban_list_table->display();
echo '</form>';
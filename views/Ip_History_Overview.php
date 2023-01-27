<?php

require_once(WP_PLUGIN_DIR . '/wordpress-security-protection/tables/Ip_History_List_Table.php');

$ip_history_list_table = new Ip_History_List_Table();

echo '<form id="wpse-list-table-form" method="post">';
    $ip_history_list_table->prepare_items();

    echo "<h3>Login geschiedenis</h3>";

    $ip_history_list_table->display();
echo '</form>';
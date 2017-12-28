<?php

defined('ABSPATH') or die('Initialize WordPress-core first');

function markei_security_protection_updatestate_report()
{
    $report = [];
    $report['wordpress'] = [];
    $report['wordpress']['installed'] = get_bloginfo('version');
    $updates = get_core_updates(['available' => true, 'dismissed' => true]);
    $newestAvailable = $report['wordpress']['installed'];
    foreach ($updates as $update) {
        if (version_compare($update->version, $newestAvailable, '>')) {
            $newestAvailable = $update->version;
        }
    }
    $report['wordpress']['newest'] = $newestAvailable;
    $report['wordpress']['_hasUpdate'] = $report['wordpress']['installed'] !== $report['wordpress']['newest'];
    
    $pluginsWithUpdates = get_plugin_updates();
    $report['wordpress-plugins'] = [];
    $hasPluginUpdate = false;
    foreach (get_plugins() as $key => $plugin) {
        $report['wordpress-plugins'][$key] = [
            'name' => $plugin['Name'],
            'installed' => $plugin['Version'],
            'newest' => isset($pluginsWithUpdates[$key]) && isset($pluginsWithUpdates[$key]->update) ? $pluginsWithUpdates[$key]->update->new_version : $plugin['Version']
        ];
        $report['wordpress-plugins'][$key]['_hasUpdate'] = $report['wordpress-plugins'][$key]['installed'] !== $report['wordpress-plugins'][$key]['newest'];
        $hasPluginUpdate = ($report['wordpress-plugins'][$key]['_hasUpdate'] === true || $hasPluginUpdate === true) ? true : false;
    }
    
    $report['summary'] = [
        'wordpress' => $report['wordpress']['_hasUpdate'],
        'wordpress-plugins' => $hasPluginUpdate
    ];
    
    return $report;
}
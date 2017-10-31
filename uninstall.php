<?php
// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

$options = array("message_1", "message_II", "message_III", "message_IV", "low_score", "mid_score", "high_score"); 

for ($i = 0; $i < $options.count(); $i++) {
	delete_site_option($options[$i]);
}
 
// drop a custom database table
global $wpdb;
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mytable");

?>

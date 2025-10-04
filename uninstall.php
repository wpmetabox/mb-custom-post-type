<?php
defined( 'WP_UNINSTALL_PLUGIN' ) || die;

global $wpdb;
$result = $wpdb->query( "DESCRIBE $wpdb->terms `term_order`" );
if ( $result ) {
	$query  = "ALTER TABLE $wpdb->terms DROP `term_order`";
	$wpdb->query( $query );
}
delete_option( 'add_term_order_column' );

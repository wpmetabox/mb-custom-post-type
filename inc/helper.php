<?php
function mb_cpt_get_prop( $object, $prop_1, $prop_2 = null, $default = '' ) {
	return property_exists( $object, $prop_1 ) ? property_exists( $object->$prop_1, $prop_2 ) ? $object->$prop_1->$prop_2 : $object->$prop_1 : $default;
}

function mb_cpt_get_post_types() {
	$post_types = get_post_types( '', 'objects' );
	$post_types = array_diff_key( $post_types, array_flip( [
		'custom_css',
		'customize_changeset',
		'oembed_cache',
		'nav_menu_item',
		'revision',
		'user_request',
		'wp_block',

		'mb-post-type',
		'mb-taxonomy',
		'mb-views',
		'meta-box',
	] ) );

	return $post_types;
}
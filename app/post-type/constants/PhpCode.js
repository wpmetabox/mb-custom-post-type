import { SupportSettings, TaxonomySettings } from './DefaultSettings';

const labelSettings = settings => {
	return`'label'  => esc_html__( '${settings.name}', 'text-domain' ),
		'labels' => [
			'menu_name'          => esc_html__( '${settings.menu_name || settings.name}', '${settings.text_domain}' ),
			'name_admin_bar'     => esc_html__( '${settings.name_admin_bar || settings.singular_name}', '${settings.text_domain}' ),
			'add_new'            => esc_html__( '${settings.add_new || 'Add new'}', '${settings.text_domain}' ),
			'add_new_item'       => esc_html__( '${settings.add_new_item + '\'' || `${'Add new ' + settings.singular_name + '\''}`}, '${settings.text_domain}' ),
			'new_item'           => esc_html__( '${settings.new_item + '\'' || `${'New ' + settings.singular_name + '\''}`}, '${settings.text_domain}' ),
			'edit_item'          => esc_html__( '${settings.edit_item + '\'' || `${'Edit ' + settings.singular_name + '\''}`}, '${settings.text_domain}' ),
			'view_item'          => esc_html__( '${settings.view_item + '\'' || `${'View ' + settings.singular_name + '\''}`}, '${settings.text_domain}' ),
			'update_item'        => esc_html__( '${settings.update_item + '\'' || `${'Update ' + settings.singular_name + '\''}`}, '${settings.text_domain}' ),
			'all_items'          => esc_html__( '${settings.all_items + '\'' || `${'All ' + settings.name + '\''}`}, '${settings.text_domain}' ),
			'search_items'       => esc_html__( '${settings.search_items + '\'' || `${'Search ' + settings.name + '\''}`}, '${settings.text_domain}' ),
			'parent_item_colon'  => esc_html__( '${settings.parent_item_colon + '\'' || `${'Parent ' + settings.singular_name + '\''}`}, '${settings.text_domain}' ),
			'not_found'          => esc_html__( '${settings.not_found + '\'' || `${'No ' + settings.name + ' found\''}`}, '${settings.text_domain}' ),
			'not_found_in_trash' => esc_html__( '${settings.not_found_in_trash + '\'' || `${'No ' + settings.name + ' found in Trash\''}`}, '${settings.text_domain}' ),
			'name'               => esc_html__( '${settings.name}', '${settings.text_domain}' ),
			'singular_name'      => esc_html__( '${settings.singular_name}', '${settings.text_domain}' ),
		],`;
}

const supportSettings = settings => {
	let temp = '';

	for ( let key in SupportSettings ) {
		temp += settings[key] === true ? `\n\t\t\t'${key}',` : '';
	}

	return '' === temp ? '' : `'supports' => [${temp}\n\t\t],`;
}

const taxonomySettings = settings => {
	let temp = '';

	for ( let key in TaxonomySettings ) {
		temp += settings[key] === true ? `\n\t\t\t'${key}',` : '';
	}

	return '' === temp ? '' : `'taxonomies' => [${temp}\n\t\t],`;
}

const menuIcon = settings => settings.menu_icon ? `\n\t\t'menu_icon'           => '${settings.menu_icon}',` : '';
const restBase = settings => settings.rest_base ? `\n\t\t'rest_base'           => '${settings.rest_base}',` : '';
const menuPostion = settings => settings.menu_position ? `\n\t\t'menu_position'       => ${settings.menu_position},` : '';

const reWrite = settings => {
	let result = `'rewrite' => `;

	const rewrite_slug = undefined === settings.rewrite_slug ? '' : `'slug' => '${settings.rewrite_slug}'`;
	const rewrite_no_front = undefined === settings.rewrite_no_front || false === settings.rewrite_no_front ? '' : ` 'with_front' => false`;

	if ( '' === rewrite_slug && '' === rewrite_no_front ) {
		return result + 'true';
	}

	return result + `[ ${rewrite_slug},${rewrite_no_front} ]`;
}

const advanceSettings = settings => {
	let showInMenu = false;
	if ( settings.show_in_menu && 'false' !== settings.show_in_menu ) {
		showInMenu = 'true' === settings.show_in_menu ? true : `'${settings.show_in_menu}'`;
	}
	return `'public'              => ${settings.public},
		'exclude_from_search' => ${settings.exclude_from_search},
		'publicly_queryable'  => ${settings.publicly_queryable},
		'show_ui'             => ${settings.show_ui},
		'show_in_nav_menus'   => ${settings.show_in_nav_menus},
		'show_in_admin_bar'   => ${settings.show_in_admin_bar},
		'show_in_rest'        => ${settings.show_in_rest},
		'capability_type'     => '${settings.capability_type}',
		'hierarchical'        => ${settings.hierarchical},
		'has_archive'         => ${settings.archive_slug ? `'${settings.archive_slug}'` : true},
		'query_var'           => ${settings.query_var},
		'can_export'          => ${settings.can_export},
		'rewrite_no_front'    => ${settings.rewrite_no_front},
		'show_in_menu'        => ${showInMenu},`;
}

const PhpCode = settings => {
	return (
`<?php
add_action( 'init', '${settings.function_name}' );
function ${settings.function_name}() {
	$args = [
		${labelSettings( settings )}
		${advanceSettings( settings )}${menuPostion( settings )}${restBase( settings )}${menuIcon( settings )}
		${supportSettings( settings )}
		${taxonomySettings( settings )}
		${reWrite( settings )}
	];

	register_post_type( '${settings.slug}', $args );
}`
	);
}

export default PhpCode;
import { PostTypeSettings } from './DefaultSettings';

const labelSettings = settings => {
	return `'label'  => esc_html__( '${settings.name}', 'text-domain' ),
		'labels' => [
			'menu_name'                  => esc_html__( '${settings.menu_name || settings.name}', '${settings.text_domain}' ),
			'all_items'                  => esc_html__( '${settings.all_items || settings.singular_name}', '${settings.text_domain}' ),
			'edit_item'                  => esc_html__( '${settings.edit_item + '\'' || `${'Edit ' + settings.singular_name + '\''}`}, '${settings.text_domain}' ),
			'view_item'                  => esc_html__( '${settings.view_item + '\'' || `${'View ' + settings.singular_name + '\''}`}, '${settings.text_domain}' ),
			'update_item'                => esc_html__( '${settings.update_item + '\'' || `${'Update ' + settings.singular_name + '\''}`}, '${settings.text_domain}' ),
			'add_new_item'               => esc_html__( '${settings.add_new_item + '\'' || `${'Add new ' + settings.singular_name + '\''}`}, '${settings.text_domain}' ),
			'new_item'                   => esc_html__( '${settings.new_item + '\'' || `${'New ' + settings.singular_name + '\''}`}, '${settings.text_domain}' ),
			'parent_item'                => esc_html__( '${settings.parent_item + '\'' || `${'Parent ' + settings.singular_name + '\''}`}, '${settings.text_domain}' ),
			'parent_item_colon'          => esc_html__( '${settings.parent_item_colon + '\'' || `${'Parent ' + settings.singular_name + '\''}`}, '${settings.text_domain}' ),
			'search_items'               => esc_html__( '${settings.search_items + '\'' || `${'Search ' + settings.name + '\''}`}, '${settings.text_domain}' ),
			'popular_items'              => esc_html__( '${settings.popular_items + '\'' || `${'Search ' + settings.name + '\''}`}, '${settings.text_domain}' ),
			'separate_items_with_commas' => esc_html__( '${settings.separate_items_with_commas + '\'' || `${'Search ' + settings.name + '\''}`}, '${settings.text_domain}' ),
			'add_or_remove_items'        => esc_html__( '${settings.add_or_remove_items + '\'' || `${'Search ' + settings.name + '\''}`}, '${settings.text_domain}' ),
			'choose_from_most_used'      => esc_html__( '${settings.choose_from_most_used + '\'' || `${'Search ' + settings.name + '\''}`}, '${settings.text_domain}' ),
			'not_found'                  => esc_html__( '${settings.not_found + '\'' || `${'No ' + settings.name + ' found\''}`}, '${settings.text_domain}' ),
			'name'                       => esc_html__( '${settings.name}', '${settings.text_domain}' ),
			'singular_name'              => esc_html__( '${settings.singular_name}', '${settings.text_domain}' ),
		],`;
};

const postTypeSettings = settings => {
	let temp = '';
	let i = 0;

	for ( let key in PostTypeSettings ) {
		i += 1;
		if ( !settings[ key ] ) {
			continue;
		}
		if ( i > 1 ) {
			temp += `, `;
		}
		temp += `'${key}'`;
	}

	return '' === temp ? '' : `[ ${temp} ]`;
};

const reWrite = settings => {
	let result = `'rewrite' => `;

	const rewrite_slug = undefined === settings.rewrite_slug ? '' : `'slug' => '${settings.rewrite_slug}'`;

	if ( '' === rewrite_slug ) {
		return result + 'true';
	}

	return result + `[ ${rewrite_slug} ]`;
};

const advanceSettings = settings => {
	return `'public'               => ${settings.public},
		'show_ui'              => ${settings.show_ui},
		'show_in_menu'         => ${settings.show_in_menu},
		'show_in_nav_menus'    => ${settings.show_in_nav_menus},
		'show_tagcloud'        => ${settings.show_tagcloud},
		'show_in_quick_edit'   => ${settings.show_in_quick_edit},
		'show_admin_column'    => ${settings.show_admin_column},
		'show_in_rest'         => ${settings.show_in_rest},
		'hierarchical'         => ${settings.hierarchical},
		'query_var'            => ${settings.query_var},
		'sort'                 => ${settings.sort},
		'rewrite_no_front'     => ${settings.rewrite_no_front},
		'rewrite_hierarchical' => ${settings.rewrite_hierarchical},`;
};

const PhpCode = settings => {
	return (
		`<?php
add_action( 'init', '${settings.function_name}' );
function ${settings.function_name}() {
	$args = [
		${labelSettings( settings )}
		${advanceSettings( settings )}
		${reWrite( settings )}
	];
	register_taxonomy( '${settings.slug}', ${postTypeSettings( settings )}, $args );
}`
	);
};

export default PhpCode;
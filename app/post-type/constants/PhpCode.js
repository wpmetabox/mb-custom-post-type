const labelSettings = settings => {
	const { label, labels, text_domain } = settings;

	let keys = Object.keys( labels );
	const maxLengh = Math.max.apply( null, keys.map( key => key.length ) );
	keys = keys.map( key => `'${ key }'${ ' '.repeat( maxLengh - key.length ) } => esc_html__( '${ labels[ key ] }', '${ text_domain }' ),` );

	let output = `'label'  => esc_html__( '${ label }', '${ text_domain }' ),
		'labels' => [
			${ keys.join( "\n\t\t\t" ) }
		]`;

	return output;
};

const supportSettings = settings => settings.supports.length ? `'supports'            => ['${settings.supports.join( "', '" )}'],` : '';
const taxonomySettings = settings => settings.taxonomies.length ? `'taxonomies'          => ['${settings.taxonomies.join( "', '" )}'],` : '';

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
};

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
};

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
};

export default PhpCode;
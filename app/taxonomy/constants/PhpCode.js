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

const postTypeSettings = settings => settings.post_types.length ? `['${settings.post_types.join( "', '" )}']` : null;

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
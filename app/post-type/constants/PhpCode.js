import { checkboxList, general, labels, spaces, text, translatableText } from '../../code';

const advanced = settings => {
	const ignore = [ 'slug', 'function_name', 'text_domain', 'label', 'labels', 'description', 'rest_base', 'show_in_menu', 'menu_icon', 'menu_position', 'capability_type', 'has_archive', 'archive_slug', 'rewrite', 'supports', 'taxonomies' ];

	let keys = Object.keys( settings ).filter( key => !ignore.includes( key ) );
	return keys.map( key => general( settings, key ) ).join( ",\n\t\t" );
};

const showInMenu = settings => {
	let value = settings.show_in_menu;
	value = [ true, false ].includes( value ) ? value : `'${ value }'`;
	let code = `'show_in_menu'${ spaces( settings, 'show_in_menu' ) } => ${ value },`;
	if ( value === true ) {
		code += `
		${ text( settings, 'menu_icon' ) },
		${ general( settings, 'menu_position' ) },`;
	}
	return code;
};

const archive = settings => {
	let value = settings.archive_slug ? `'${ settings.archive_slug }'` : settings.has_archive;
	return `'has_archive'${ spaces( settings, 'has_archive' ) } => ${ value }`;
};

const rewrite = settings => {
	let value = [];
	if ( settings.rewrite.slug ) {
		value.push( text( settings.rewrite, 'slug' ) );
	}
	value.push( general( settings.rewrite, 'with_front' ) );

	return `'rewrite'${ spaces( settings, 'rewrite' ) } => [
			${ value.join( ",\n\t\t\t" ) },
		]`;
};

const PhpCode = settings => {
	return `<?php
add_action( 'init', '${ settings.function_name }' );
function ${ settings.function_name }() {
	$labels = [
		${ labels( settings ) },
	];
	$args = [
		${ translatableText( settings, 'label' ) },
		'labels'${ spaces( settings, 'labels' ) } => $labels,
		${ text( settings, 'description' ) },
		${ advanced( settings ) },
		${ archive( settings ) },
		${ text( settings, 'rest_base' ) },
		${ showInMenu( settings ) }
		${ text( settings, 'capability_type' ) },
		${ checkboxList( settings, 'supports', false ) },
		${ checkboxList( settings, 'taxonomies', '[]' ) },
		${ rewrite( settings ) },
	];

	register_post_type( '${ settings.slug }', $args );
}`;
};

export default PhpCode;

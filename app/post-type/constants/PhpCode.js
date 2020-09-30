const maxKeyLengh = object => Math.max.apply( null, Object.keys( object ).map( key => key.length ) );
const spaces = ( settings, key ) => ' '.repeat( maxKeyLengh( settings ) - key.length );

const text = ( settings, key ) => `'${ key }'${ spaces( settings, key ) } => '${ settings[ key ] }'`;
const translatableText = ( settings, key ) => `'${ key }'${ spaces( settings, key ) } => esc_html__( '${ settings[ key ] }', '${ settings.text_domain }' )`;
const checkboxList = ( settings, key ) => `'${ key }'${ spaces( settings, key ) } => ${ settings[ key ].length ? `['${ settings[ key ].join( "', '" ) }']` : '[]' }`;
const general = ( settings, key ) => `'${ key }'${ spaces( settings, key ) } => ${ settings[ key ] }`;

const labels = settings => {
	const { labels } = settings;

	let keys = Object.keys( labels );
	labels.text_domain = settings.text_domain; // Add text domain to run the `text` function above.

	return keys.map( key => translatableText( labels, key ) ).join( ",\n\t\t" );
};

const advanced = settings => {
	const ignore = [ 'slug', 'function_name', 'text_domain', 'label', 'labels', 'description', 'rest_base', 'menu_icon', 'capability_type', 'has_archive', 'archive_slug', 'rewrite', 'supports', 'taxonomies' ];

	let keys = Object.keys( settings ).filter( key => !ignore.includes( key ) );
	return keys.map( key => general( settings, key ) ).join( ",\n\t\t" );
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
		${ text( settings, 'label' ) },
		'labels'${ spaces( settings, 'labels' ) } => $labels,
		${ text( settings, 'description' ) },
		${ advanced( settings ) },
		${ archive( settings ) },
		${ text( settings, 'rest_base' ) },
		${ text( settings, 'menu_icon' ) },
		${ text( settings, 'capability_type' ) },
		${ checkboxList( settings, 'supports' ) },
		${ checkboxList( settings, 'taxonomies' ) },
		${ rewrite( settings ) },
	];

	register_post_type( '${ settings.slug }', $args );
}`;
};

export default PhpCode;
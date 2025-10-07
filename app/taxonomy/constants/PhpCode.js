import dotProp from 'dot-prop';
import { general, labels, spaces, text, translatableText } from '../../code';

const types = settings => {
	let values = dotProp.get( settings, 'types', [] );
	if ( !Array.isArray( values ) ) {
		values = [ values ];
	}
	return `${ values.length ? `['${ values.join( "', '" ) }']` : '[]' }`;
};

const advanced = settings => {
	const ignore = [ 'slug', 'types', 'function_name', 'text_domain', 'label', 'labels', 'description', 'rest_base', 'rewrite', 'meta_box_cb', 'meta_box_sanitize_cb' ];

	let keys = Object.keys( settings ).filter( key => !ignore.includes( key ) );
	return keys.map( key => general( settings, key ) ).join( ",\n\t\t" );
};

const rewrite = settings => {
	let value = [];
	if ( settings.rewrite.slug ) {
		value.push( text( settings.rewrite, 'slug' ) );
	}
	value.push( general( settings.rewrite, 'with_front' ) );
	value.push( general( settings.rewrite, 'hierarchical' ) );

	return `'rewrite'${ spaces( settings, 'rewrite' ) } => [
			${ value.join( ",\n\t\t\t" ) },
		]`;
};

const meta_box_cb = settings => {
	return settings.meta_box_cb ? '' : `\n\t\t'meta_box_cb'${ spaces( settings, 'meta_box_cb' ) } => false,`;
};

const meta_box_sanitize_cb = settings => {
	if ( ! settings.meta_box_cb || ! settings.meta_box_sanitize_cb ) {
		return '';
	}

	return `\n\t\t'meta_box_sanitize_cb'${ spaces( settings, 'meta_box_sanitize_cb' ) } => '${ settings.meta_box_sanitize_cb }',`;
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
		${ advanced( settings ) },${ meta_box_cb( settings ) }${ meta_box_sanitize_cb( settings ) }
		${ text( settings, 'rest_base' ) },
		${ rewrite( settings ) },
	];
	register_taxonomy( '${ settings.slug.replace(/\\/g, '\\\\').replace(/\'/g, '\\\'') }', ${ types( settings, 'types' ) }, $args );
}`;
};

export default PhpCode;
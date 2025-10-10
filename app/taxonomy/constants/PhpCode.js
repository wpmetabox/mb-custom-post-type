import dotProp from 'dot-prop';
import { general, labels, outputSettingObject, spaces, text, translatableText } from '../../code';
import DefaultSettings from './DefaultSettings';

const types = settings => {
	let values = dotProp.get( settings, 'types', [] );
	if ( !Array.isArray( values ) ) {
		values = [ values ];
	}
	return `${ values.length ? `['${ values.join( "', '" ) }']` : '[]' }`;
};

const advanced = settings => {
	const ignore = [ 'slug', 'types', 'function_name', 'text_domain', 'label', 'labels', 'description', 'rest_base', 'rewrite', 'default_term', 'capabilities', 'meta_box', 'meta_box_cb', 'meta_box_sanitize_cb', 'rest_namespace', 'rest_controller_class', 'taxonomy' ];

	let keys = Object.keys( settings ).filter( key => !ignore.includes( key ) );
	return keys.map( key => general( settings, key ) ).join( ",\n\t\t" );
};

const special = settings => {
	return outputSettingObject( settings, 'capabilities', 3 );
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
	// If true, leave setting logic for wp
	if ( settings.meta_box_cb && typeof settings.meta_box_cb !== 'string' ) {
		return '';
	}

	// If default callbacks, leave setting logic for wp
	if ( [ 'post_tags_meta_box', 'post_categories_meta_box' ].includes( settings.meta_box_cb ) ) {
		return '';
	}

	const value = settings.meta_box_cb ? `'${ settings.meta_box_cb }'` : false;
	return `\r\t\t'meta_box_cb'${ spaces( settings, 'meta_box_cb' ) } => ${ value },`;
};

const meta_box_sanitize_cb = settings => {
	// If meta_box is inactive don't show it
	if ( !settings.meta_box_cb || !settings.meta_box_sanitize_cb ) {
		return '';
	}

	return `\n\t\t'meta_box_sanitize_cb'${ spaces( settings, 'meta_box_sanitize_cb' ) } => '${ settings.meta_box_sanitize_cb }',`;
};

const PhpCode = settings => {
	return `<?php
add_action( 'init', '${ settings.function_name || DefaultSettings.function_name }' );
function ${ settings.function_name || DefaultSettings.function_name }() {
	$labels = [
		${ labels( settings ) },
	];
	$args = [
		${ translatableText( settings, 'label' ) },
		'labels'${ spaces( settings, 'labels' ) } => $labels,
		${ text( settings, 'description' ) },
		${ advanced( settings ) },
		${ special( settings ) }${ meta_box_cb( settings ) }${ meta_box_sanitize_cb( settings ) }
		${ text( settings, 'rest_base' ) },
		${ rewrite( settings ) },
	];
	register_taxonomy( '${ settings.slug.replace( /\\/g, '\\\\' ).replace( /\'/g, '\\\'' ) }', ${ types( settings, 'types' ) }, $args );
}`;
};

export default PhpCode;
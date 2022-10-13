import dotProp from 'dot-prop';
import { spaces, text, translatableText, general, labels } from '../../code';

const types = settings => {
	let values = dotProp.get( settings, 'types', [] );
	if ( !Array.isArray( values ) ) {
		values = [ values ];
	}
	return `${ values.length ? `['${ values.join( "', '" ) }']` : '[]' }`;
};

const advanced = settings => {
	const ignore = [ 'slug', 'types', 'function_name', 'text_domain', 'label', 'labels', 'description', 'rest_base', 'rewrite', 'meta_box_cb' ];

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
	let value = settings.meta_box_cb ? `'${ settings.meta_box_cb }'` : settings.meta_box_cb;

	if ( value ) {
		value = settings.hierarchical ? `'post_categories_meta_box'` : `'post_tags_meta_box'`; ;
	}


	return `'meta_box_cb'${ spaces( settings, 'meta_box_cb' ) } => ${ value }`;
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
		${ meta_box_cb( settings ) },
		${ text( settings, 'rest_base' ) },
		${ rewrite( settings ) },
	];
	register_taxonomy( '${ settings.slug.replace(/\\/g, '\\\\').replace(/\'/g, '\\\'') }', ${ types( settings, 'types' ) }, $args );
}`;
};

export default PhpCode;
import dotProp from 'dot-prop';

const maxKeyLength = object => Math.max.apply( null, Object.keys( object ).map( key => key.length ) );
const spaces = ( settings, key ) => ' '.repeat( maxKeyLength( settings ) - key.length );
const checkText = ( settings, key ) => {
	let value = dotProp.get( settings, key, '' ).replace( /\\/g, '\\\\' );
	value = value.replace( /\'/g, '\\\'' );
	return value;
};
const text = ( settings, key ) => `'${ key }'${ spaces( settings, key ) } => '${ checkText( settings, key ) }'`;
const translatableText = ( settings, key ) => `'${ key }'${ spaces( settings, key ) } => esc_html__( '${ checkText( settings, key ) }', '${ settings.text_domain || 'your-textdomain' }' )`;
const checkboxList = ( settings, key, defaultValue ) => `'${ key }'${ spaces( settings, key ) } => ${ dotProp.get( settings, key, [] ).length ? `['${ dotProp.get( settings, key, [] ).join( "', '" ) }']` : defaultValue }`;
const general = ( settings, key ) => {
	let value = dotProp.get( settings, key );
	if ( [ '', undefined ].includes( value ) ) {
		value = "''";
	}
	return `'${ key }'${ spaces( settings, key ) } => ${ value }`;
};

const outputSettingObject = ( settings, key, indent = 1 ) => {
	const setting = dotProp.get( settings, key );
	if ( !isPlainObjectWithKeys( setting ) ) {
		return '';
	}

	return `'${ key }'${ spaces( settings, key ) } => ${ outputObject( setting, indent ) }`;
};

const outputObject = ( obj, indent = 1 ) => {
	const indentString = "\t".repeat( indent );
	const bracketIndentString = "\t".repeat( indent - 1 );
	const entries = Object.entries( obj ).map( ( [ k, v ] ) => `${ indentString }'${ k }'${ spaces( obj, k ) } => '${ v }',` );

	return `[\n${ entries.join( "\n" ) }\n${ bracketIndentString }]`;
};

const isPlainObjectWithKeys = obj => Object.prototype.toString.call( obj ) === '[object Object]' && Object.keys( obj ).length > 0;

const labels = settings => {
	const { labels } = settings;

	let keys = Object.keys( labels );
	// Create a temporary labels object with text_domain for translation purposes
	const tempLabels = { ...labels };
	tempLabels.text_domain = dotProp.get( settings, 'text_domain', 'your-textdomain' );

	return keys.map( key => translatableText( tempLabels, key ) ).join( ",\n\t\t" );
};

export { checkboxList, general, labels, outputSettingObject, spaces, text, translatableText };

import dotProp from 'dot-prop';

const maxKeyLength = object => Math.max.apply( null, Object.keys( object ).map( key => key.length ) );
const spaces = ( settings, key ) => ' '.repeat( maxKeyLength( settings ) - key.length );
const checktext = ( settings, key ) => {
    let checktext = dotProp.get( settings, key, '' ).replace(/\'/g, '\\\'');
    return checktext;
};
const text = ( settings, key ) => `'${ key }'${ spaces( settings, key ) } => '${ checktext( settings, key ) }'`;
const translatableText = ( settings, key ) => `'${ key }'${ spaces( settings, key ) } => esc_html__( '${ checktext( settings, key ) }', '${ settings.text_domain }' )`;
const checkboxList = ( settings, key, defaultValue ) => `'${ key }'${ spaces( settings, key ) } => ${ dotProp.get( settings, key, [] ).length ? `['${ dotProp.get( settings, key, [] ).join( "', '" ) }']` : defaultValue }`;
const general = ( settings, key ) => {
    let value = dotProp.get( settings, key );
    if ( [ '', undefined ].includes( value ) ) {
        value = "''";
    }
    return `'${ key }'${ spaces( settings, key ) } => ${ value }`;
};

const labels = settings => {
    const { labels } = settings;

    let keys = Object.keys( labels );
    labels.text_domain = dotProp.get( settings, 'text_domain', 'your-textdomain' ); // Add text domain to run the `text` function above.

    return keys.map( key => translatableText( labels, key ) ).join( ",\n\t\t" );
};

export { spaces, text, translatableText, checkboxList, general, labels };

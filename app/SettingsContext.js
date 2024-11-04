import { createContext, useState } from '@wordpress/element';

export const SettingsContext = createContext();

const removeHtml = text => text.replace( /<.*?>/g, '' ).replace( /(&lt;|&gt;)/g, '' );

export const SettingsProvider = ( { children, value } ) => {
	const [ settings, setSettings ] = useState( value );
	const updateSettings = data => {
		const labels = data.labels;

		// Fix labels is [] when empty.
		if ( typeof labels !== 'object' || Array.isArray( labels ) || labels === null ) {
			labels = {};
		}

		Object.keys( labels ).forEach( key => labels[ key ] = removeHtml( labels[ key ] ) );
		data.labels = labels;

		setSettings( prev => ( { ...prev, ...data } ) );
	};

	return <SettingsContext.Provider value={ { settings, updateSettings } }>{ children }</SettingsContext.Provider>;
};
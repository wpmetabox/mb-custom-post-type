const { createContext, useState } = wp.element;

export const SettingsContext = createContext();

export const SettingsProvider = ( { children, value } ) => {
	const [ settings, setSettings ] = useState( value );
	const updateSettings = data => setSettings( prev => ( { ...prev, data } ) );

	return <SettingsContext.Provider value={ { settings, updateSettings } }>{ children }</SettingsContext.Provider>;
};
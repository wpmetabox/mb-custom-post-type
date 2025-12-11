import { useContext } from '@wordpress/element';
import { getProperty, setProperty } from 'dot-prop';
import slugify from 'slugify';
import { SettingsContext } from '../SettingsContext';
import Checkbox from './Checkbox';
import Fontawesome from './Fontawesome';
import Icon from './Icon';
import Input from './Input';
import Select from './Select';
import Slug from './Slug';
import Textarea from './Textarea';
import Toggle from './Toggle';

const ucfirst = str => str.length ? str[ 0 ].toUpperCase() + str.slice( 1 ) : str;
const normalizeBool = value => {
	if ( 'true' === value ) {
		value = true;
	} else if ( 'false' === value ) {
		value = false;
	}
	return value;
};

const Control = ( { field, autoFills = [] } ) => {
	const { settings, updateSettings } = useContext( SettingsContext );

	const isDisplay = field => {
		const { dependency } = field;
		if ( !dependency ) {
			return true;
		}

		const conditions = dependency.split( '&&' ).map( c => c.trim() );

		for ( let condition of conditions ) {
			const match = condition.match( /([^:]+):([^:\s]+)/ );
			if ( !match ) {
				continue;
			}
			const depName = match[ 1 ];
			const depValue = normalizeBool( match[ 2 ] );
			const currentValue = getProperty( settings, depName );

			if ( currentValue !== depValue ) {
				return false;
			}
		}

		return true;
	};

	const autofill = ( newSettings, name, value ) => {
		const placeholder = name.replace( 'labels.', '' );
		autoFills.forEach( f => {
			let newValue;

			if ( 'slug' === f.name ) {
				// Only generate slug when it's not manually changed.
				if ( newSettings._slug_changed ) {
					return;
				}
				newValue = slugify( value, { lower: true } );
			} else {
				newValue = ucfirst( f.default
					.replace( `%${ placeholder }%`, value )
					.replace( `%${ placeholder }_lowercase%`, value.toLowerCase() )
				);
			}

			setProperty( newSettings, f.name, newValue );
		} );

		return newSettings;
	};

	const update = e => {
		const name = e.target.name;
		let value = 'checkbox' === e.target.type ? getProperty( e.target, 'checked', false ) : e.target.value;
		value = normalizeBool( value );
		value = name === 'menu_position' ? parseFloat( value ) || '' : value;

		let newSettings = { ...settings };
		setProperty( newSettings, name, value );
		autofill( newSettings, name, value );

		updateSettings( newSettings );
	};

	const updateCheckbox = ( name, value ) => {
		let newSettings = { ...settings };
		setProperty( newSettings, name, value );

		updateSettings( newSettings );
	};

	const _value = getProperty( settings, field.name, field.default || '' );
	if ( !isDisplay( field ) ) {
		return '';
	}
	switch ( field.type ) {
		case 'text':
			return <Input { ...field } value={ _value } update={ update } />;
		case 'textarea':
			return <Textarea { ...field } value={ _value } update={ update } />;
		case 'toggle':
			return <Toggle { ...field } checked={ _value } update={ updateCheckbox } />;
		case 'checkbox':
			return <Checkbox { ...field } checked={ _value } update={ updateCheckbox } />;
		case 'icon':
			return <Icon { ...field } value={ _value } update={ update } />;
		case 'fontawesome':
			return <Fontawesome { ...field } value={ _value } update={ update } />;
		case 'select':
			return <Select { ...field } value={ _value } update={ update } />;
		case 'slug':
			return <Slug { ...field } value={ _value } update={ update } settings={ settings } updateSettings={ updateSettings } />;
	}
};

export default Control;

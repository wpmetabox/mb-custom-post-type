import dotProp from 'dot-prop';
import slugify from 'slugify';
import { SettingsContext } from '../SettingsContext';
import Checkbox from './Checkbox';
import Icon from './Icon';
import Input from './Input';
import Radio from './Radio';
import Select from './Select';
import Textarea from './Textarea';
const { useContext } = wp.element;

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
		const dep = dependency.match( /([^:]+):([^:\s]+)/ );
		const depName = dep[ 1 ];
		const depValue = normalizeBool( dep[ 2 ] );
		const currentDepValue = dotProp.get( settings, depName );

		return depValue === currentDepValue;
	};

	const autofill = ( newSettings, name, value ) => {
		const placeholder = name.replace( 'labels.', '' );
		autoFills.forEach( f => {
			let newValue;

			if ( 'slug' === f.name ) {
				newValue = slugify( value, { lower: true } );
			} else {
				newValue = ucfirst( f.default.replace( `%${ placeholder }%`, f.default.split( ' ' ).length > 2 ? value.toLowerCase() : value ) );
			}

			dotProp.set( newSettings, f.name, newValue );
		} );

		return newSettings;
	};

	const update = e => {
		const name = e.target.name;
		let value = 'checkbox' === e.target.type ? dotProp.get( e.target, 'checked', false ) : e.target.value;
		value = normalizeBool( value );
		value = name === 'menu_position' ? parseInt( value ) : value;

		let newSettings = { ...settings };
		dotProp.set( newSettings, name, value );
		autofill( newSettings, name, value );

		updateSettings( newSettings );
	};

	const _value = dotProp.get( settings, field.name, field.default || '' );
	if ( ! isDisplay( field ) ) {
		return '';
	}
	switch ( field.type ) {
		case 'text':
			return <Input { ...field } value={ _value } update={ update } />;
		case 'textarea':
			return <Textarea { ...field } value={ _value } update={ update } />;
		case 'checkbox':
			return <Checkbox { ...field } checked={ _value } update={ update } />;
		case 'radio':
			return <Radio { ...field } value={ _value } update={ update } />;
		case 'icon':
			return <Icon { ...field } value={ _value } update={ update } />;
		case 'select':
			return <Select { ...field } value={ _value } update={ update } />;
	}
};

export default Control;

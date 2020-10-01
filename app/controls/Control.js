import dotProp from 'dot-prop';
import slugify from 'slugify';
import PhpSettings from '../PhpSettings';
import Input from './Input';
import Textarea from './Textarea';
import Checkbox from './Checkbox';
import Radio from './Radio';
import Select from './Select';
const { useContext } = wp.element;

const ucfirst = str => str.length ? str[ 0 ].toUpperCase() + str.slice( 1 ) : str;
const normalizeBool = value => {
	if ( 'true' === value ) {
		value = true;
	} else if ( 'false' === value ) {
		value = false;
	}
	return value;
}
const normalizeNumber = value => isNaN( parseInt( value ) ) ? value : parseInt( value );

const Control = ( { field, autoFills } ) => {
	const [ state, setState ] = useContext( PhpSettings );

	const update = e => {
		const name = e.target.dataset.name;
		let value = 'checkbox' === e.target.type ? e.target.checked : e.target.value;
		value = normalizeBool( value );
		value = normalizeNumber( value );

		setState( state => {
			let newState = JSON.parse( JSON.stringify( state ) );

			dotProp.set( newState, name, value );

			if ( !autoFills ) {
				return newState;
			}

			const placeholder = name.replace( 'labels.', '' );
			autoFills.forEach( f => {
				let newValue;

				if ( 'slug' !== f.name ) {
					newValue = ucfirst( f.default.replace( `%${placeholder}%`, f.default.split( ' ' ).length > 2 ? value.toLowerCase() : value ) );
				} else {
					newValue = slugify( value, { lower: true } );
				}

				dotProp.set( newState, f.name, newValue );
			} );

			return newState;
		} );
	};

	const _value = dotProp.get( state, field.name ) || field.default || '';
	switch ( field.type ) {
		case 'text':
			return <Input label={ field.label } name={ field.name } value={ _value } description={ field.description } required={ field.required } update={ update } />;
		case 'textarea':
			return <Textarea label={ field.label } name={ field.name } placeholder={ field.placeholder } value={ _value } description={ field.description } update={ update } />;
		case 'checkbox':
			return <Checkbox label={ field.label } name={ field.name } description={ field.description } checked={ _value } update={ update } />;
		case 'radio':
			return <Radio label={ field.label } name={ field.name } options={ field.options } value={ _value } update={ update } />;
		case 'select':
			return <Select label={ field.label } name={ field.name } description={ field.description } options={ field.options } value={ _value } update={ update } />;
	}
};

export default Control;
import PhpSettings from '../PhpSettings';
import Input from './Input';
import Textarea from './Textarea';
import Checkbox from './Checkbox';
import Radio from './Radio';
import Select from './Select';
import CheckboxList from './CheckboxList';
const { useContext } = wp.element;

const stringToSlug = str => {
	// Trim the string
	str = str.replace( /^\s+|\s+$/g, '' );
	str = str.toLowerCase();

	// Remove accents
	var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;",
		to = "aaaaeeeeiiiioooouuuunc------",
		i, l;

	for ( i = 0, l = from.length; i < l; i ++ ) {
		str = str.replace( new RegExp( from.charAt( i ), 'g' ), to.charAt( i ) );
	}

	str = str.replace( /[^a-z0-9 -]/g, '' ) // remove invalid chars
		.replace( /\s+/g, '-' ) // collapse whitespace and replace by -
		.replace( /-+/g, '-' ); // collapse dashes
	return str;
}

const Control = ( {props, values, autoFills} ) => {
	const [state, setState] = useContext( PhpSettings );

	const autoFill = ( name, autoFills, value ) => {
		if ( ! autoFills ) {
			return;
		}

		autoFills.map( e => {
			if ( name !== e.updateFrom ) {
				return '';
			}

			let str;
			if ( 'slug' === e.name ) {
				str = stringToSlug( value );
				setState( state => ( { ...state, labels: { ...state.labels }, slug: str } ) );
			} else {
				str = e.defaultValue;
				setState( state => ( {...state, labels: {...state.labels, [e.name]: str.replace( '%name%', value ).replace( '%singular_name%', value )}} ) );
			}

			return '';
		} );
	}

	const handleUpdate = e => {
		const name = e.target.name;
		let value;

		switch ( e.target.type ) {
			case 'checkbox':
				value = e.target.checked;
				break;
			case 'text':
				value = e.target.value;
				autoFill( name, autoFills, value );
				break;
			default:
				value = e.target.value;
				break;
		}

		if ( state.labels[props.name] || 'singular_name' === name ) { // fix singular_name state
			setState( state => ( {...state, labels: {...state.labels, [name]: value}} ) );
		} else {
			setState( state => ( {...state, [name]: value} ) );
		}
	}

	let _value = state.labels[props.name] ? state.labels[props.name] : state[props.name] ? state[props.name] : props.defaultValue;
	switch (props.type) {
		case 'text':
			return <Input label={props.label} name={props.name} placeholder={props.placeholder} defaultValue={_value} description={props.description} required={props.required} update={handleUpdate} />
		case 'textarea':
			return <Textarea label={props.label} name={props.name} placeholder={props.placeholder} defaultValue={_value} description={props.description} update={handleUpdate} />
		case 'checkbox':
			return <Checkbox label={props.label} name={props.name} description={props.description} checked={_value} update={handleUpdate} />
		case 'radio':
			return <Radio label={props.label} name={props.name} values={props.values} defaultValue={_value} update={handleUpdate} />
		case 'select':
			return <Select label={props.label} name={props.name} description={props.description} values={props.values} defaultValue={_value} update={handleUpdate} />
		case undefined:
			return <CheckboxList label={props.label} name={props.name} values={values} update={handleUpdate} />
		default:
			break;
	}
}

export default Control;
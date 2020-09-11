import React, { useContext } from 'react';
import PhpSettings from '../PhpSettings';
import Input from './Input';
import Textarea from './Textarea';
import Checkbox from './Checkbox';
import Radio from './Radio';
import Select from './Select';
import CheckboxList from './CheckboxList';

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
			if ( 'args_post_type' === e.name || 'args_taxonomy' === e.name ) {
				str = stringToSlug( value );
				setState( state => ( {...state, [e.name]: str} ) );
			} else {
				str = e.defaultValue;
				setState( state => ( {...state, [e.name]: str.replace( '%name%', value ).replace( '%singular_name%', value )} ) );
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

		setState( state => ( {...state, [name]: value} ) );
	}

	switch (props.type) {
		case 'text':
			return <Input label={props.label} name={props.name} placeholder={props.placeholder} defaultValue={state[props.name]} description={props.description} required={props.required} update={handleUpdate} />
		case 'textarea':
			return <Textarea label={props.label} name={props.name} placeholder={props.placeholder} description={props.description} update={handleUpdate} />
		case 'checkbox':
			return <Checkbox label={props.label} name={props.name} description={props.description} checked={props.checked} update={handleUpdate} />
		case 'radio':
			return <Radio label={props.label} name={props.name} values={props.values} defaultValue={props.defaultValue} update={handleUpdate} />
		case 'select':
			return <Select label={props.label} name={props.name} description={props.description} values={props.values} defaultValue={props.defaultValue} update={handleUpdate} />
		case undefined:
			return <CheckboxList label={props.label} name={props.name} values={values} checked={props.checked} update={handleUpdate} />
		default:
			break;
	}
}

export default Control;
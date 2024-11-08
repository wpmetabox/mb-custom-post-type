import { ToggleControl } from '@wordpress/components';
import { useContext } from '@wordpress/element';
import dotProp from 'dot-prop';
import { SettingsContext } from '../SettingsContext';

const CheckboxList = ( { name, options, description } ) => {
	const { settings, updateSettings } = useContext( SettingsContext );
	const saved = dotProp.get( settings, name, [] );

	const update = ( value, checked ) => {
		let newSaved = [ ...saved ];
		if ( checked ) {
			newSaved.push( value );
		} else {
			newSaved = newSaved.filter( option => option !== value );
		}
		updateSettings( { [ name ]: newSaved } );
	};

	return (
		<div className="mb-cpt-field">
			<div className="mb-cpt-input">
				{ description && <p className="mb-cpt-description">{ description }</p> }
				{
					Object.entries( options ).map( ( [ value, label ] ) => (
						<ToggleControl key={ value } checked={ saved.includes( value ) } label={ label } onChange={ checked => update( value, checked ) } />
					) )
				}
			</div>
		</div>
	);
};

export default CheckboxList;
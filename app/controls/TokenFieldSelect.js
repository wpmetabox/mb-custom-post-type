import { useContext } from '@wordpress/element';
import { FormTokenField } from '@wordpress/components';
import { SettingsContext } from '../SettingsContext';

const TokenFieldSelect = ( { label, name, suggestions, placeholder } ) => {
	const { settings, updateSettings } = useContext( SettingsContext );

	const value = settings[ name ] || [];

	const handleChange = ( newValue ) => {
		updateSettings( {
			...settings,
			[ name ]: newValue,
		} );
	};

	return (
		<div className="mb-cpt-field">
			<label className="mb-cpt-label">{ label }</label>
			<div className="mb-cpt-input">
				<FormTokenField
					__experimentalExpandOnFocus
					label=""
					value={ value }
					suggestions={ suggestions }
					onChange={ handleChange }
					placeholder={ placeholder }
				/>
			</div>
		</div >
	);
};

export default TokenFieldSelect;

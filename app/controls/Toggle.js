import { ToggleControl } from '@wordpress/components';

const Toggle = ( { label, name, description, update, checked } ) => (
	<div className="mb-cpt-field">
		<div className="mb-cpt-input">
			<ToggleControl checked={ checked } label={ label } help={ description } onChange={ value => update( name, value ) } />
		</div>
	</div>
);

export default Toggle;
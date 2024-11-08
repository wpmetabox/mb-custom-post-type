import { ToggleControl } from '@wordpress/components';
import Tooltip from './Tooltip';

const Checkbox = ( { label, name, description = '', update, checked, required = false, tooltip = '' } ) => (
	<div className="mb-cpt-field">
		<label className="mb-cpt-label" htmlFor={ name }>
			{ label }
			{ required && <span className="mb-cpt-required">*</span> }
			{ tooltip && <Tooltip id={ name } content={ tooltip } /> }
		</label>
		<div className="mb-cpt-input">
			<ToggleControl checked={ checked } label={ description } onChange={ value => update( name, value ) } />
		</div>
	</div>
);

export default Checkbox;
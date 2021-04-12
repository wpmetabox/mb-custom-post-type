import Tooltip from './Tooltip';

const Checkbox = ( { label, name, description, update, checked, required = false, tooltip = '' } ) => (
	<div className="mb-cpt-field">
		<label className="mb-cpt-label" htmlFor={ name }>
			{ label }
			{ required && <span className="mb-cpt-required">*</span> }
			{ tooltip && <Tooltip id={ name } content={ tooltip } /> }
		</label>
		<div className="mb-cpt-input">
			{
				description
					? <label className="mb-cpt-description"><input type="checkbox" id={ name } name={ name } checked={ checked } onChange={ update } /> { description }</label>
					: <input type="checkbox" id={ name } name={ name } checked={ checked } onChange={ update } />
			}
		</div>
	</div>
);

export default Checkbox;
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
					? <div className="mb-cpt-toggle"><label className="mb-cpt-description"><input type="checkbox" id={ name } name={ name } checked={ checked } onChange={ update } /><div className="mb-cpt-toggle__switch"></div>{ description }</label>
						</div>
					: <div className="mb-cpt-toggle"><input type="checkbox" id={ name } name={ name } checked={ checked } onChange={ update } /><div className="mb-cpt-toggle__switch"></div></div>
			}
		</div>
	</div>
);

export default Checkbox;
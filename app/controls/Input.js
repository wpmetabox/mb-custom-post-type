import Tooltip from './Tooltip';

const Input = ( { label, name, value, update, tooltip = '', description = '', required = false } ) => (
	<div className="mb-cpt-field">
		<label className="mb-cpt-label" htmlFor={ name }>
			{ label }
			{ required && <span className="mb-cpt-required">*</span> }
			{ tooltip && <Tooltip id={ name } content={ tooltip } /> }
		</label>
		<div className="mb-cpt-input">
			<input type="text" required={ required } id={ name } name={ name } value={ value } onChange={ update } />
			{ description && <div className="mb-cpt-description">{ description }</div> }
		</div>
	</div>
);

export default Input;
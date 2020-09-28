const Input = ( { label, name, placeholder, defaultValue, update, description = '', required = false } ) => {
	return (
		<div className="mb-cpt-field">
			<label className="mb-cpt-label" htmlFor={ name }>
				{ label }
				{ required && <span className="mb-cpt-required">*</span> }
			</label>
			<div className="mb-cpt-input">
				<input type="text" required={ required } id={ name } name={ name } placeholder={ placeholder } value={ defaultValue } onChange={ update } />
				{ description && <div className="mb-cpt-description">{ description }</div> }
			</div>
		</div>
	);
};

export default Input;
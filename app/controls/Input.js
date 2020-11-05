const { useEffect } = wp.element;

const Input = ( { label, name, value, update, description = '', required = false } ) => {
	// Update the labels when loaded (run only once)
	// After migration, some labels are missing and show placeholders %name%, %singular_name%
	useEffect( () => {
		if ( ![ 'labels.name', 'labels.singular_name' ].includes( name ) ) {
			return;
		}

		// Fake event.
		const e = { target: { name, value } };
		update( e );
	}, [] );

	return (
		<div className="mb-cpt-field">
			<label className="mb-cpt-label" htmlFor={ name }>
				{ label }
				{ required && <span className="mb-cpt-required">*</span> }
			</label>
			<div className="mb-cpt-input">
				<input type="text" required={ required } id={ name } name={ name } value={ value } onChange={ update } />
				{ description && <div className="mb-cpt-description">{ description }</div> }
			</div>
		</div>
	);
};

export default Input;
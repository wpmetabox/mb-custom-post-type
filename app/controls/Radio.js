const Radio = ( { label, name, update, options, value } ) => {
	return (
		<div className="mb-cpt-field mb-cpt-field--radio">
			<label className="mb-cpt-label">{ label }</label>
			<div className="mb-cpt-input">
				{
					options.map( ( option, key ) => (
						<label key={ key } className={ `mb-cpt-choice${option.icon ? ' mb-cpt-icon' : ''}` }>
							<input type="radio" data-name={ name } value={ option.value } checked={ option.value === value } onChange={ update } />
							{option.icon && <i className={ option.icon + " wp-menu-image dashicons-before" }></i> }
							{option.label }
						</label>
					) )
				}
			</div>
		</div>
	);
};

export default Radio;
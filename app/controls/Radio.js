const Radio = ( { label, name, update, options, value } ) => (
	<div className="mb-cpt-field mb-cpt-field--radio">
		<label className="mb-cpt-label">{ label }</label>
		<div className="mb-cpt-input">
			{
				options.map( option => (
					<label key={ option.value } className="mb-cpt-choice">
						<input type="radio" name={ name } value={ option.value } checked={ option.value === value } onChange={ update } />
						{option.label }
					</label>
				) )
			}
		</div>
	</div>
);

export default Radio;
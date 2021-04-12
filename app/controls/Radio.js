import Tooltip from './Tooltip';

const Radio = ( { label, name, update, options, value, required = false, tooltip = '' } ) => (
	<div className="mb-cpt-field mb-cpt-field--radio">
		<label className="mb-cpt-label" htmlFor={ name }>
			{ label }
			{ required && <span className="mb-cpt-required">*</span> }
			{ tooltip && <Tooltip id={ name } content={ tooltip } /> }
		</label>
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
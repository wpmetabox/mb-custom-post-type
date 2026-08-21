import Tooltip from './Tooltip';

const ToggleGroup = ( { label, name, update, options, value, required = false, tooltip = '' } ) => (
	<div className="mb-cpt-field">
		<label className="mb-cpt-label" htmlFor={ name }>
			{ label }
			{ required && <span className="mb-cpt-required">*</span> }
			{ tooltip && <Tooltip id={ name } content={ tooltip } /> }
		</label>
		<div className="mb-cpt-input">
			<div className="mb-cpt-toggle-group">
				{
					options.map( option => (
						<label key={ option.value }>
							<input type="radio" name={ name } value={ option.value } checked={ String( option.value ) === String( value ) } onChange={ update } />
							<span className="dashicons dashicons-yes-alt"></span>
							<span>{ option.label }</span>
						</label>
					) )
				}
			</div>
		</div>
	</div>
);

export default ToggleGroup;

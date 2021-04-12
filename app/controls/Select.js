import Tooltip from './Tooltip';

const Select = ( { label, name, update, description = '', options, value, required = false, tooltip = '' } ) => (
	<div className="mb-cpt-field">
		<label className="mb-cpt-label" htmlFor={ name }>
			{ label }
			{ required && <span className="mb-cpt-required">*</span> }
			{ tooltip && <Tooltip id={ name } content={ tooltip } /> }
		</label>
		<div className="mb-cpt-input">
			<select id={ name } name={ name } value={ value } onChange={ update }>
				{ options.map( option => <option key={ option.value } value={ option.value }>{ option.label }</option> ) }
			</select>
			{ description && <div className="mb-cpt-description">{ description }</div> }
		</div>
	</div>
);

export default Select;
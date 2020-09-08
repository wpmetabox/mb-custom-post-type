import React from 'react';

const Select = ( {label, name, update, description = '', values, defaultValue} ) => {
	return (
		<div className="ptg-field">
			<label className="ptg-label" htmlFor={name}>{label}</label>
			<div className="ptg-input">
				<select id={name} name={name} defaultValue={defaultValue} onChange={update}>
					{values.map( (value, key) => <option key={key} value={value.value}>{value.label}</option> )}
				</select>
				{description && <div className="ptg-description">{description}</div>}
			</div>
		</div>
	)
}

export default Select;
import React from 'react';

const Input = ( {label, name, placeholder, defaultValue, update, description = '', required = false} ) => {
	return (
		<div className="ptg-field">
			<label className="ptg-label" htmlFor={name}>
				{label}
				{required && <span className="ptg-required">*</span>}
			</label>
			<div className="ptg-input">
				<input type="text" id={name} name={name} placeholder={placeholder} defaultValue={defaultValue} onChange={update} />
				{description && <div className="ptg-description">{description}</div>}
			</div>
		</div>
	)
}

export default Input;
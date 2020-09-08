import React from 'react';

const Checkbox = ( {label, name, description, update, checked} ) => {
	return (
		<div className="ptg-field">
			{label && <label className="ptg-label" htmlFor={name}>{label}</label>}
			<div className="ptg-input">
				{
					description
					? <label className="ptg-description"><input type="checkbox" id={name} name={name} defaultChecked={checked} onChange={update} /> {description}</label>
					: <input type="checkbox" id={name} name={name} defaultChecked={checked} onChange={update} />
				}
			</div>
		</div>
	)
}

export default Checkbox;
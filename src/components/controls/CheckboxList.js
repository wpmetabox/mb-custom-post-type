import React from 'react';

const CheckboxList = ( {label, name, update, values} ) => {
	return (
		<div className="ptg-field">
			{label && <label className="ptg-label" htmlFor={name}>{label}</label>}
			<div className="ptg-input">
				<ul className="ptg-input-list">
					{values.map( (value, key) => <li key={key}><label className="ptg-description"><input type="checkbox" id={value.name} name={value.name} defaultChecked={value.checked} onChange={update} /> {value.description}</label></li> )}
				</ul>
			</div>
		</div>
	)
}

export default CheckboxList;
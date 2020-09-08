import React from 'react';

const Radio = ( {label, name, update, values, defaultValue} ) => {
	return (
		<div className="ptg-field ptg-field--radio">
			<label className="ptg-label">{label}</label>
			<div className="ptg-input">
				{
					values.map( ( item, key ) => (
						<label key={key} className={`ptg-choice${item.icon ? ' ptg-icon' : ''}`}>
							<input type="radio" name={name} value={item.value} defaultChecked={item.value === defaultValue && "checked" } onChange={update} />
							{item.icon && <i className={item.icon + " wp-menu-image dashicons-before"}></i>}
							{item.label}
						</label>
					) )
				}
			</div>
		</div>
	)
}

export default Radio;
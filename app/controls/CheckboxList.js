const CheckboxList = ( {label, name, update, values} ) => {
	return (
		<div className="mb-cpt-field">
			{label && <label className="mb-cpt-label" htmlFor={name}>{label}</label>}
			<div className="mb-cpt-input">
				<ul className="mb-cpt-input-list">
					{values.map( (value, key) => <li key={key}><label className="mb-cpt-description"><input type="checkbox" name={value.name} defaultChecked={value.checked} onChange={update} /> {value.description}</label></li> )}
				</ul>
			</div>
		</div>
	)
}

export default CheckboxList;
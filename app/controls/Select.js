const Select = ( {label, name, update, description = '', values, defaultValue} ) => {
	return (
		<div className="mb-cpt-field">
			<label className="mb-cpt-label" htmlFor={name}>{label}</label>
			<div className="mb-cpt-input">
				<select id={name} name={name} defaultValue={defaultValue} onChange={update}>
					{values.map( (value, key) => <option key={key} value={value.value}>{value.label}</option> )}
				</select>
				{description && <div className="mb-cpt-description">{description}</div>}
			</div>
		</div>
	)
}

export default Select;
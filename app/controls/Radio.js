const Radio = ( {label, name, update, values, defaultValue} ) => {
	return (
		<div className="mb-cpt-field mb-cpt-field--radio">
			<label className="mb-cpt-label">{label}</label>
			<div className="mb-cpt-input">
				{
					values.map( ( item, key ) => (
						<label key={key} className={`mb-cpt-choice${item.icon ? ' mb-cpt-icon' : ''}`}>
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
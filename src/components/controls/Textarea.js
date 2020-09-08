import React from 'react';

const Textarea = ( {label, name, placeholder, update, description = '', required = false} ) => (
	<div className="ptg-field">
		<label className="ptg-label" htmlFor={name}>
			{label}
			{required && <span className="ptg-required">*</span>}
		</label>
		<div className="ptg-input">
			<textarea id={name} name={name} placeholder={placeholder} onChange={update} />
			{description && <div className="ptg-description">{description}</div>}
		</div>
	</div>
)

export default Textarea;
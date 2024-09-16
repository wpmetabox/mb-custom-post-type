const Toggle = ( { label, name, description, update, checked } ) => (
	<div className="mb-cpt-field">
		<div className="mb-cpt-input">
			{
				description
					? <div className="mb-cpt-toggle">
						<label className="mb-cpt-toggle__wrapper">
							<input type="checkbox" id={ name } name={ name } checked={ checked } onChange={ update } />
							<div className="mb-cpt-toggle__switch"></div>
							<div>
								<div className="mb-cpt-toggle__title">
									{ label }
								</div>
								<div>{ description }</div>
							</div>
						</label>
					</div>
					: <div className="mb-cpt-toggle">
						<label className="mb-cpt-toggle__wrapper">
							<input type="checkbox" id={ name } name={ name } checked={ checked } onChange={ update } />
							<div className="mb-cpt-toggle__switch"></div>
							<div className="mb-cpt-toggle__title">
								{ label }
							</div>
						</label>
					</div>
			}
		</div>
	</div>
);

export default Toggle;
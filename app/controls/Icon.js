const { Dashicon } = wp.components;
const Icon = ( { label, name, update, value } ) => (
	<div className="mb-cpt-field mb-cpt-field--radio">
		<label className="mb-cpt-label">{ label }</label>
		<div className="mb-cpt-input">
			{
				MBCPT.icons.map( icon => (
					<label key={ icon } className="mb-cpt-choice mb-cpt-icon">
						<input type="radio" name={ name } value={ `dashicons-${ icon }` } checked={ `dashicons-${ icon }` === value } onChange={ update } />
						<Dashicon icon={ icon } />
					</label>
				) )
			}
		</div>
	</div>
);

export default Icon;
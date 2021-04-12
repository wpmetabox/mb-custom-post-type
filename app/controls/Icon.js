const { Dashicon } = wp.components;
import Tooltip from './Tooltip';

const Icon = ( { label, name, update, value, required = false, tooltip = '' } ) => (
	<div className="mb-cpt-field mb-cpt-field--radio">
		<label className="mb-cpt-label" htmlFor={ name }>
			{ label }
			{ required && <span className="mb-cpt-required">*</span> }
			{ tooltip && <Tooltip id={ name } content={ tooltip } /> }
		</label>
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
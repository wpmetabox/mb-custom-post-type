import Tooltip from './Tooltip';
import { RawHTML } from '@wordpress/element';

const Fontawesome = ( { label, name, update, value, required = false, tooltip = '', description = '' } ) => {
	return (
		<div className="mb-cpt-field">
			<label className="mb-cpt-label" htmlFor={ name }>
				{ label }
				{ required && <span className="mb-cpt-required">*</span> }
				{ tooltip && <Tooltip id={ name } content={ tooltip } /> }
			</label>
			<div className='mb-cpt-input'>
				<div className='mb-cpt-icon-selected'>
					<span className={ `icon-fontawesome ${ value }` }></span>
					<input type="text" name={ name } value={ value } onChange={ update } />
				</div>
				{ description && <div className="mb-cpt-description"><RawHTML>{ description }</RawHTML></div> }
			</div>
		</div>
	);
};

export default Fontawesome;
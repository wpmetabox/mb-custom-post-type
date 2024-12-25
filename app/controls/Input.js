import { RawHTML } from '@wordpress/element';
import Tooltip from './Tooltip';

const Input = ( { label, name, value, update, tooltip = '', description = '', required = false, placeholder = '', datalist = [] } ) => (
	<div className="mb-cpt-field">
		<label className="mb-cpt-label" htmlFor={ name }>
			{ label }
			{ required && <span className="mb-cpt-required">*</span> }
			{ tooltip && <Tooltip id={ name } content={ tooltip } /> }
		</label>
		<div className="mb-cpt-input">
			<input type="text" 
				required={ required }
				id={ name }
				name={ name }
				value={ value }
				onChange={ update }  
				placeholder={ placeholder }
				list={ `${ name }-list` }
			/>
			
			{ description && <div className="mb-cpt-description"><RawHTML>{ description }</RawHTML></div> }
			{ datalist.length > 0 && <datalist id={ `${ name }-list` }>
				{ datalist.map( item => <option key={ item } value={ item } /> ) }
			</datalist> }
		</div>
	</div>
);

export default Input;
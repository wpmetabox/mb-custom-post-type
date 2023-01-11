import Tooltip from './Tooltip';
import { useState } from "@wordpress/element";

const Icon = ( { label, name, update, value, required = false, tooltip = '' } ) => {
	const [ query, setQuery ] = useState( "" );
	let data = MBCPT.icons.map( icon => {
			let label = icon;
			if ( icon.includes( '-' ) ) {
				label = icon.replace( /-/g, ' ' );
			}
			let startsText = [ 'admin', 'controls', 'editor', 'format', 'image', 'welcom' ];
			for ( let text in startsText ) {
				if ( icon.startsWith( text ) ) {
					label = label.replace( text, '' );
				}
			}
			let endsText = [ 'alt', 'alt2', 'alt3' ];
			for ( let text in endsText ) {
				if ( icon.endsWith( text ) ) {
					label = label.replace( text, `(${ text })` );
				}
			}
			return [ icon, label ];
		} );

	return (
		<div className="mb-cpt-field mb-cpt-field--radio">
			<label className="mb-cpt-label" htmlFor={ name }>
				{ label }
				{ required && <span className="mb-cpt-required">*</span> }
				{ tooltip && <Tooltip id={ name } content={ tooltip } /> }
			</label>
			<div className='mb-cpt-input'>
				<div className='mb-cpt-icon-selected'>
					<span className={ `dashicons ${ value }` }></span>
					<input type="text" className="mb-cpt-search" placeholder="Search..." value={ query } onChange={ event => setQuery( event.target.value ) } />
				</div>
				<div className="mb-cpt-items">
					{
						data.filter( ( [ key, label ] ) => {
							if ( query === '' ) {
								return [ key, label ];
							} else if ( label.toLowerCase().includes( query.toLowerCase() ) ) {
								return [ key, label ];
							}
						} ).map( ( [ key, label ] ) => (
							<div className='mb-cpt-item'>
								<label key={ key } className="mb-cpt-icon">
									<input type="radio" name={ name } value={ `dashicons-${ key }` } checked={ `dashicons-${ key }` === value } onChange={ update } />
									<span className={ `dashicons dashicons-${ key }` }></span>
								</label>
								<span className='mb-cpt-item__text'>{ label }</span>
							</div>
						) )
					}
				</div>
			</div>
		</div>
	);
};

export default Icon;
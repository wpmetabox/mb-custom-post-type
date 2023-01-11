const { Dashicon } = wp.components;
const { useState } = wp.element;
import Tooltip from './Tooltip';

const Icon = ( { label, name, update, value, required = false, tooltip = '' } ) => {
	const [ query, setQuery ] = useState( "" );
	const keyLabel = Object.entries( MBCPT.fullIcons ).find( ( [ key, label ] ) => `dashicons-${ key }` === value );
	const data = Object.entries( MBCPT.fullIcons ).filter( ( [ key, label ] ) => {
		if ( `dashicons-${ key }` !== value ) {
			return [ key, label ];
		}
	} );
	data.unshift( keyLabel );

	return (
		<div className="mb-cpt-field mb-cpt-field--radio">
			<label className="mb-cpt-label" htmlFor={ name }>
				{ label }
				{ required && <span className="mb-cpt-required">*</span> }
				{ tooltip && <Tooltip id={ name } content={ tooltip } /> }
			</label>
			<div className='mb-cpt-input'>
				<input type="text" className="mb-cpt-search" placeholder="Search..." value={ query } onChange={ event => setQuery( event.target.value ) } />
				<div className="mb-cpt-items">
					{
						Object.entries( MBCPT.fullIcons ).filter( ( [ key, label ] ) => {
							if ( query === '' ) {
								return [ key, label ];
							} else if ( label.toLowerCase().includes( query.toLowerCase() ) ) {
								return [ key, label ];
							}
						} ).map( ( [ key, label ] ) => (
							<div className='mb-cpt-item'>
								<label key={ key } className="mb-cpt-icon">
									<input type="radio" name={ name } value={ `dashicons-${ key }` } checked={ `dashicons-${ key }` === value } onChange={ update } />
									<Dashicon icon={ key } />
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
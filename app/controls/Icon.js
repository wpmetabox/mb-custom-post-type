import { Dropdown } from "@wordpress/components";
import { useState } from "@wordpress/element";
import { __ } from "@wordpress/i18n";
import Tooltip from './Tooltip';

const getIconLabel = icon => {
	let label = icon.replace( /-/g, ' ' ).trim();

	const startsText = [ 'admin', 'controls', 'editor', 'format', 'image', 'media', 'welcome' ];
	startsText.forEach( text => {
		if ( label.startsWith( text ) ) {
			label = label.replace( text, '' );
		}
	} );

	const endsText = [ 'alt', 'alt2', 'alt3' ];
	endsText.forEach( text => {
		if ( label.endsWith( text ) ) {
			label = label.replace( text, `(${ text })` );
		}
	} );

	label = label.trim();
	const specialText = {
		businessman: 'business man',
		aligncenter: 'align center',
		alignleft: 'align left',
		alignright: 'align right',
		customchar: 'custom character',
		distractionfree: 'distraction free',
		removeformatting: 'remove formatting',
		strikethrough: 'strike through',
		skipback: 'skip back',
		skipforward: 'skip forward',
		leftright: 'left right',
		screenoptions: 'screen options',
	};
	label = specialText[ label ] || label;

	return label.trim().toLowerCase();
};

const Icon = ( { label, name, update, value, required = false, tooltip = '' } ) => {
	const [ query, setQuery ] = useState( '' );
	const icons = MBCPT.icons.map( icon => [ icon, getIconLabel( icon ) ] )
		.filter( item => query === '' || item[ 1 ].includes( query.toLowerCase() ) );

	return (
		<div className="mb-cpt-field mb-cpt-field--radio">
			<label className="mb-cpt-label" htmlFor={ name }>
				{ label }
				{ required && <span className="mb-cpt-required">*</span> }
				{ tooltip && <Tooltip id={ name } content={ tooltip } /> }
			</label>
			<div className='mb-cpt-input'>
				<Dropdown
					popoverProps={ { placement: 'left-start' } }
					contentClassName="mb-cpt-icon__dropdown"
					renderToggle={ ( { onToggle } ) => (
						<button type="button" onClick={ onToggle } className="button button-secondary mb-cpt-icon__pick">
							<span className={ `dashicons ${ value }` }></span>
						</button>
					) }
					renderContent={ ( { onToggle } ) => (
						<>
							<input
								type="text"
								className="mb-cpt-icon__search"
								placeholder={ __( 'Search...', 'mb-custom-post-type' ) }
								value={ query }
								onChange={ event => setQuery( event.target.value ) }
							/>
							<div className="mb-cpt-icon__items">
								{
									icons.map( ( [ icon, label ] ) => (
										<div
											key={ icon }
											className={ `mb-cpt-icon__item ${ `dashicons-${ icon }` === value ? 'mb-cpt-icon__item--selected' : '' }` }
											onClick={ () => {
												update( { target: { name, value: `dashicons-${ icon }` } } );
												onToggle();
											} }
										>
											<span className={ `dashicons dashicons-${ icon }` }></span>
											<div className='mb-cpt-icon__text'>{ label }</div>
										</div>
									) )
								}
							</div>
						</>
					) }
				/>
			</div>
		</div>
	);
};

export default Icon;

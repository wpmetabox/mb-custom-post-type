import { RawHTML, useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import Tooltip from './Tooltip';

const Slug = ( { label, name, value, update, tooltip = '', description = '', required = false } ) => {
	const isReservedTerm = MBCPT.reservedTerms.includes( value );
	const isTooLong = value.length > 20;
	const error = isReservedTerm
		? __( 'ERROR: the slug must not be one of WordPress <a href="https://codex.wordpress.org/Reserved_Terms" target="_blank" rel="noopener noreferrer">reserved terms</a>', 'mb-custom-post-type' )
		: isTooLong
			? sprintf( __( 'ERROR: the slug must not exceed %d characters.', 'mb-custom-post-type' ), 20 )
			: '';

	useEffect( () => {
		document.querySelector( '#publish' ).disabled = !!error;
	}, [ error ] );

	return (
		<div className="mb-cpt-field">
			<label className="mb-cpt-label" htmlFor={ name }>
				{ label }
				{ required && <span className="mb-cpt-required">*</span> }
				{ tooltip && <Tooltip id={ name } content={ tooltip } /> }
			</label>
			<div className="mb-cpt-input">
				<input type="text" required={ required } id={ name } name={ name } value={ value } onChange={ update } />
				{ description && <div className="mb-cpt-description">{ description }</div> }
				{ error && <RawHTML className="mb-cpt-error">{ error }</RawHTML> }
			</div>
		</div>
	);
};

export default Slug;
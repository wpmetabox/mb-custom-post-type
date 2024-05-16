import { RawHTML,  useState, useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import Tooltip from './Tooltip';

const Slug = ({ label, name, initialValue = '', tooltip = '', description = '', required = false }) => {
	const [value, setValue] = useState(initialValue);
    const isReservedTerm = MBCPT.reservedTerms.includes(value);
    const isTooLong = value.length > 20;
    const error = isReservedTerm
        ? __('ERROR: the slug must not be one of WordPress <a href="https://codex.wordpress.org/Reserved_Terms" target="_blank" rel="noopener noreferrer">reserved terms</a>', 'mb-custom-post-type')
        : isTooLong
        ? sprintf(__('ERROR: the slug must not exceed %d characters.', 'mb-custom-post-type'), 20)
        : null;

    useEffect(() => {
        const publishButton = document.querySelector('#publish');
        if (publishButton) {
            publishButton.disabled = !!error;
        }
    }, [error]);

    const handleChange = (e) => {
		const newValue = e.target.value;
        console.log('Value:', newValue);
        console.log('Value Length:', newValue.length);
        setValue(newValue);
        // setValue(e.target.value);
    };

	return (
		<div className="mb-cpt-field">
			<label className="mb-cpt-label" htmlFor={ name }>
				{ label }
				{ required && <span className="mb-cpt-required">*</span> }
				{ tooltip && <Tooltip id={ name } content={ tooltip } /> }
			</label>
			<div className="mb-cpt-input">
				<input type="text" required={ required } id={ name } name={ name } value={ value } onChange={handleChange} />
				{ description && <div className="mb-cpt-description">{ description }</div> }
				{ error && <RawHTML className="mb-cpt-error">{ error }</RawHTML> }
			</div>
		</div>
	);
};

export default Slug;
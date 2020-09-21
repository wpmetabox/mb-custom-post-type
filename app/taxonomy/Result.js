import PhpCode from './constants/PhpCode';
import Clipboard from 'react-clipboard.js';
import PhpSettings from '../PhpSettings';
import { UnControlled as CodeMirror } from 'react-codemirror2';
const { useState, useContext } = wp.element;
const { __ } = wp.i18n;

const Result = () => {
	const [copied, setCopied] = useState( false );
	const copy = () => {
		setCopied( true );
		setTimeout( () => setCopied( false ), 1000 );
	}

	const [state, setState] = useContext( PhpSettings );

	return (
		<div className="mb-cpt-result">
			<p>{__( 'Copy the code and paste into your theme\'s <code>functions.php</code> file.', 'mb-custom-post-type' )}</p>
			<div className="mb-cpt-result__body">
				<CodeMirror value={ PhpCode( state ) } options={ { mode: 'php', lineNumbers: true } }/>
				<Clipboard className="button" title={__( 'Click to copy the code', 'mb-custom-post-type' )} data-clipboard-text={PhpCode( state )} onSuccess={copy}>{copied ? __( 'Copied', 'mb-custom-post-type' ) : __( 'Copy', 'mb-custom-post-type' )}</Clipboard>
			</div>
		</div>
	);
}

export default Result;
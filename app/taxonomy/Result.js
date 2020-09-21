import PhpCode from './constants/PhpCode';
import Clipboard from 'react-clipboard.js';
import PhpSettings from '../PhpSettings';
import { UnControlled as CodeMirror } from 'react-codemirror2';
const { useState, useContext } = wp.element;

const Result = () => {
	const [copied, setCopied] = useState( false );
	const copy = () => {
		setCopied( true );
		setTimeout( () => setCopied( false ), 1000 );
	}

	const [state, setState] = useContext( PhpSettings );

	return (
		<div className="mb-cpt-result">
			<p>Copy the code and paste into your theme's <code>functions.php</code> file.</p>
			<div className="mb-cpt-result__body">
				<CodeMirror value={ PhpCode( state ) } options={ { mode: 'php', lineNumbers: true } }/>
				<Clipboard className="button" title="Click to copy the code" data-clipboard-text={PhpCode( state )} onSuccess={copy}>{copied ? 'Copied' : 'Copy'}</Clipboard>
			</div>
		</div>
	);
}

export default Result;
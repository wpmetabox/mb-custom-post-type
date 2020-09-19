import React, { useState } from 'react';
import { render } from 'react-dom';
import PhpCode from './constants/PhpCode';
import Highlight from 'react-highlight';
import Clipboard from 'react-clipboard.js';

const Result = () => {
	const [copied, setCopied] = useState( false );
	const copy = () => {
		setCopied( true );
		setTimeout( () => setCopied( false ), 1000 );
	}

	const code = JSON.parse( document.getElementById( 'content' ).value );

	return (
		<div className="mb-cpt-result">
			<div className="alert alert-info"><p>Copy the code and paste into your theme's <code>functions.php</code> file.</p></div>
			<div className="mb-cpt-result__body">
				<Highlight className="php">{PhpCode( code )}</Highlight>
				<Clipboard title="Click to copy the code" data-clipboard-text={PhpCode( code )} onSuccess={copy}>{copied ? 'Copied' : 'Copy'}</Clipboard>
			</div>
		</div>
	);
}

render( <Result />, document.getElementById( 'code-result' ) );
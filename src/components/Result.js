import React, { useContext, useState, lazy, Suspense, memo } from 'react';
import PhpSettings from '../contexts/PhpSettings';
import PhpCode from '../constants/PhpCode';

const Spinner = () => <span class="ptg-loading">Generating code. Please wait...</span>;

const Result = () => {
	const [state, setState] = useContext( PhpSettings );

	const [copied, setCopied] = useState( false );
	const copy = () => {
		setCopied( true );
		setTimeout( () => setCopied( false ), 1000 );
	}

	const Highlight = lazy( () => import( 'react-highlight' ) );
	const Clipboard = lazy( () => import( 'react-clipboard.js' ) );

	if ( ! state.name || ! state.singular_name ) {
		return (
			<div className="ctg-result">
				<p className="alert alert-error">
					Required fields must not be emptied!
				</p>
			</div>
		);
	}

	return (
		<Suspense fallback={<Spinner/>}>
			<div className="ptg-result">
				<div className="alert alert-info">Copy the code and paste into your theme's <code>functions.php</code> file. Wanna more features or use inside the WordPress admin? <a href="https://metabox.io/pricing/" target="_blank" rel="noopener noreferrer">Become a premium user</a>.</div>
				<div className="ptg-result__body">
					<Highlight className="php">{PhpCode( state )}</Highlight>
					<Clipboard title="Click to copy the code" data-clipboard-text={PhpCode( state )} onSuccess={copy}>{copied ? 'Copied' : 'Copy'}</Clipboard>
				</div>
			</div>
		</Suspense>
	);
}

export default memo( Result );
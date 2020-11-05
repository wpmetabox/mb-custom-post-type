import { UnControlled as CodeMirror } from 'react-codemirror2';
import { SettingsContext } from '../SettingsContext';
import PhpCode from './constants/PhpCode';
const { useContext } = wp.element;
const { __ } = wp.i18n;
const { ClipboardButton } = wp.components;
const { withState } = wp.compose;

const Result = () => {
	const { settings } = useContext( SettingsContext );
	const Button = withState( {
		hasCopied: false,
	} )( ( { hasCopied, setState } ) => (
		<ClipboardButton className="button" text={ PhpCode( settings ) } onCopy={ () => setState( { hasCopied: true } ) } onFinishCopy={ () => setState( { hasCopied: false } ) }>
			{ hasCopied ? __( 'Copied!', 'meta-box-builder' ) : __( 'Copy', 'meta-box-builder' ) }
		</ClipboardButton>
	) );

	return (
		<div className="mb-cpt-result">
			<p>{ __( 'Copy and paste the following code into your theme\'s functions.php file.', 'mb-custom-post-type' ) }</p>
			<div className="mb-cpt-result__body">
				<CodeMirror value={ PhpCode( settings ) } options={ { mode: 'php', lineNumbers: true } } />
				<Button />
			</div>
		</div>
	);
};

export default Result;
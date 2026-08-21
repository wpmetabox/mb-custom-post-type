import { useEffect, useRef } from '@wordpress/element';

const CodeMirror = window.wp.CodeMirror;

const CodeMirrorEditor = ( { value, options = {} } ) => {
	const ref = useRef();
	const editorRef = useRef();

	useEffect( () => {
		editorRef.current = CodeMirror.fromTextArea( ref.current, options );

		return () => editorRef.current.toTextArea();
	}, [] );

	useEffect( () => {
		const editor = editorRef.current;
		if ( !editor ) {
			return;
		}
		const cursor = editor.getDoc().getCursor();
		editor.getDoc().setValue( value );
		editor.getDoc().setCursor( cursor );
	}, [ value ] );

	return <textarea ref={ ref } defaultValue={ value } className="react-codemirror2" />;
};

export default CodeMirrorEditor;

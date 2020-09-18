import PhpSettings from '../PhpSettings';
import DefaultSettings from './constants/DefaultSettings';
import MainTabs from './components/MainTabs';

const { useEffect, useState } = wp.element;
const { Button } = wp.components;

const Spinner = () => <span>Generating code. Please wait...</span>;

const enqueueScript = ( file ) => {
	var script = document.createElement( 'script' );

	script.setAttribute( 'src', file );
	document.getElementsByTagName( "head" )[0].appendChild( script );
};

const App = () => {
	let data = {};

	if ( MbCpt[0] ) {
		data = JSON.parse( MbCpt[0] );
	} else {
		data = DefaultSettings;
	}

	const [state, setState] = useState( data );

	if ( state['post_type'] && ! state['args_post_type'] ) {
		state['args_post_type'] = state['post_type'];
	}

	const handleShowCode = e => {
		e.preventDefault();
		// setShowCode( true );

		const request = new XMLHttpRequest();

		request.open('POST', AjaxVars.url, true);
		request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
		request.onload = function() {
			enqueueScript( this.response );
			// console.log(this.response);
		};
		request.onerror = function() {
			// Connection error
		};
		request.send( `action=show_code&nonce=${AjaxVars.nonce}&code_data=${document.getElementById( 'content' ).value}`);
	}

	useEffect( () => {
		const title = document.getElementById( 'title' );
		title.value = state.name;

		const name = document.getElementById( 'name' );
		name.value = state.singular_name;

		const content = document.getElementById( 'content' );
		content.value = JSON.stringify( state );
	} );

	return (
		<PhpSettings.Provider value={[state, setState]}>
			<MainTabs />
			<Button isPrimary onClick={ handleShowCode }>Generate Code</Button>
		</PhpSettings.Provider>
	);
}

export default App;
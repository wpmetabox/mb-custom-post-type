import PhpSettings from '../PhpSettings';
import DefaultSettings from './constants/DefaultSettings';
import MainTabs from './MainTabs';

const { useEffect, useState } = wp.element;
const { Button } = wp.components;

const enqueueScript = ( file ) => {
	var script = document.createElement( 'script' );

	script.setAttribute( 'src', file );
	document.getElementById( 'wpfooter' ).appendChild( script );
};

const requestGeneratedCode = () => {
	const request = new XMLHttpRequest();

	request.open('POST', AjaxVars.url, true);
	request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
	request.onload = function() { enqueueScript( this.response ) };
	// request.onerror = function() {};
	request.send( `action=show_code&nonce=${AjaxVars.nonce}&taxonomy_data=${document.getElementById( 'content' ).value}`);
}

const App = () => {
	let data = {};

	if ( MbTax[0] ) {
		data = JSON.parse( MbTax[0] );
	} else {
		data = DefaultSettings;
	}

	const [state, setState] = useState( data );

	if ( state['taxonomy'] && ! state['args_taxonomy'] ) {
		state['args_taxonomy'] = state['taxonomy'];
	}

	const handleShowCode = e => {
		e.preventDefault();
		requestGeneratedCode();
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

ReactDOM.render( <App />, document.getElementById( 'root' ) );
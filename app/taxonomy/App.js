import React, { useState, useEffect } from 'react';
import PhpSettings from '../PhpSettings';
import DefaultSettings from './constants/DefaultSettings';
import MainTabs from './components/MainTabs';
import Result from './components/Result';

const App = () => {
	let data = {};

	if ( MbTax[0] ) {
		data = JSON.parse( MbTax[0] );
	} else {
		data = DefaultSettings;
	}

	const [state, setState] = useState( data );
	const [showCode, setShowCode] = useState( false );

	if ( state['taxonomy'] && ! state['args_taxonomy'] ) {
		state['args_taxonomy'] = state['taxonomy'];
	}

	const handleShowCode = e => {
		e.preventDefault();
		setShowCode( true );
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
			<button className="button button-primary button-large" onClick={ handleShowCode }>Generate Code</button>
			{ showCode && <Result /> }
		</PhpSettings.Provider>
	);
}

export default App;
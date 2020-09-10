import React, { useState, useEffect } from 'react';
import PhpSettings from './contexts/PhpSettings';
import DefaultSettings from './constants/DefaultSettings';
import MainTabs from './components/MainTabs';
import Result from './components/Result';

const App = () => {
	const [state, setState] = useState( DefaultSettings );
	const [showCode, setShowCode] = useState( false );

	const handleShowCode = e => {
		e.preventDefault();
		setShowCode( true );
	}

	useEffect( () => {
		const title = document.getElementById( 'title' );
		title.value = state.name;

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
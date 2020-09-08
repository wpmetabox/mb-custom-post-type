import React, { useState } from 'react';
import PhpSettings from './contexts/PhpSettings';
import DefaultSettings from './constants/DefaultSettings';
import MainTabs from './components/MainTabs';
import Result from './components/Result';

const App = () => {
	const [state, setState] = useState( DefaultSettings );
	const [showCode, setShowCode] = useState( false );

	return (
		<PhpSettings.Provider value={[state, setState]}>
			<MainTabs setShowCode={setShowCode} />
			{ showCode && <Result /> }
		</PhpSettings.Provider>
	);
}

export default App;
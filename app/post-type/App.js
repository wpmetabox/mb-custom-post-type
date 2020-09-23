import PhpSettings from '../PhpSettings';
import DefaultSettings from './constants/DefaultSettings';
import MainTabs from './MainTabs';

const { render, useEffect, useState } = wp.element;
const i18n = MbPostType;
const settings = i18n.settings ? i18n.settings : DefaultSettings;

const App = () => {
	const [state, setState] = useState( DefaultSettings );

	useEffect( () => {
		document.getElementById( 'post_title' ).value = state.labels.singular_name;
		document.getElementById( 'content' ).value = JSON.stringify( state );
	} );

	return (
		<PhpSettings.Provider value={[state, setState]}>
			<MainTabs />
		</PhpSettings.Provider>
	);
}

render( <App />, document.getElementById( 'root' ) );
import PhpSettings from '../PhpSettings';
import DefaultSettings from './constants/DefaultSettings';
import MainTabs from './MainTabs';

const { render, useState } = wp.element;
const i18n = MbPostType;
const settings = i18n.settings ? i18n.settings : DefaultSettings;

const App = () => {
	const [state, setState] = useState( settings );

	return (
		<PhpSettings.Provider value={[state, setState]}>
			<MainTabs />
			<input type="hidden" name="post_title" value={ state.labels.singular_name } />
			<input type="hidden" name="content" value={ JSON.stringify( state ) } />
		</PhpSettings.Provider>
	);
}

render( <App />, document.getElementById( 'root' ) );
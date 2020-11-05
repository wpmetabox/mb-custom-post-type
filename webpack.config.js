const path = require( 'path' );

// https://www.cssigniter.com/how-to-use-external-react-components-in-your-gutenberg-blocks/
const externals = {
	'react': 'React',
	'react-dom': 'ReactDOM',
	'codemirror': 'wp.CodeMirror',
	'clipboard': 'ClipboardJS',
};

const commonModules = {
	rules: [
		{
			test: /\.js/,
			exclude: /node_modules/,
			use: {
				loader: 'babel-loader',
				options: {
					plugins: [ '@babel/plugin-transform-react-jsx' ]
				}
			}
		},
	]
};

const CptConfig = {
	entry: './app/post-type/App.js',
	output: {
		filename: 'post-type.js',
		path: path.resolve( __dirname, './assets' )
	},
	externals,
	module: commonModules
};

const TaxonomyConfig = {
	entry: './app/taxonomy/App.js',
	output: {
		filename: 'taxonomy.js',
		path: path.resolve( __dirname, './assets' )
	},
	externals,
	module: commonModules
};

module.exports = [ CptConfig ];
// module.exports = [ CptConfig, TaxonomyConfig ];
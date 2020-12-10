const path = require( 'path' );
const TerserPlugin = require( "terser-webpack-plugin" );

// https://www.cssigniter.com/how-to-use-external-react-components-in-your-gutenberg-blocks/
const externals = {
	'react': 'React',
	'react-dom': 'ReactDOM',
	'codemirror': 'wp.CodeMirror',
	'clipboard': 'ClipboardJS',
};

const optimization = {
	minimizer: [
		new TerserPlugin( {
			terserOptions: {
				keep_fnames: false,
			},
		} ),
	],
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
	optimization,
	module: commonModules
};

const TaxonomyConfig = {
	entry: './app/taxonomy/App.js',
	output: {
		filename: 'taxonomy.js',
		path: path.resolve( __dirname, './assets' )
	},
	externals,
	optimization,
	module: commonModules
};

module.exports = [ CptConfig, TaxonomyConfig ];
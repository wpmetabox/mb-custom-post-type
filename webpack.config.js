const path = require( 'path' );

const config = {
	externals: {
		'react': 'React',
		'react-dom': 'ReactDOM',
		'codemirror': 'wp.CodeMirror',
		'clipboard': 'ClipboardJS',
		'@wordpress/i18n': 'wp.i18n',
		'@wordpress/element': 'wp.element',
	},
	module: {
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
	}
};

const postType = {
	...config,
	entry: './app/post-type/App.js',
	output: {
		filename: 'post-type.js',
		path: path.resolve( __dirname, './assets' )
	},
};

const taxonomy = {
	...config,
	entry: './app/taxonomy/App.js',
	output: {
		filename: 'taxonomy.js',
		path: path.resolve( __dirname, './assets' )
	},
};

module.exports = [ postType, taxonomy ];
// Import the original config from the @wordpress/scripts package.
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

const path = require( 'path' );

const postType = {
	...defaultConfig,
	entry: './app/post-type/App.js',
	output: {
		filename: 'post-type.js',
		path: path.resolve( __dirname, 'assets/build' )
	},
};

const taxonomy = {
	...defaultConfig,
	entry: './app/taxonomy/App.js',
	output: {
		filename: 'taxonomy.js',
		path: path.resolve( __dirname, 'assets/build' )
	},
};

module.exports = [ postType, taxonomy ];
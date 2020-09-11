const path = require('path')

const CptConfig = {
	entry: './app/post-type/index.js',
	output: {
		filename: 'post-type.js',
		path: path.resolve( __dirname, './js' )
	},
	module: {
		rules: [
			{
				test: /\.js/,
				exclude: /node_modules/,
				use: {
				loader: 'babel-loader',
					options: {
						plugins: ['@babel/plugin-transform-react-jsx', '@babel/plugin-proposal-class-properties']
					}
				}
			},
			{
				test: /\.css$/,
				use: ['style-loader', 'css-loader']
			}
		]
	}
}

const TaxonomyConfig = {
	entry: './app/taxonomy/index.js',
	output: {
		filename: 'taxonomy.js',
		path: path.resolve( __dirname, './js' )
	},
	module: {
		rules: [
			{
				test: /\.js/,
				exclude: /node_modules/,
				use: {
				loader: 'babel-loader',
					options: {
						plugins: ['@babel/plugin-transform-react-jsx', '@babel/plugin-proposal-class-properties']
					}
				}
			},
			{
				test: /\.css$/,
				use: ['style-loader', 'css-loader']
			}
		]
	}
}

module.exports = [ CptConfig, TaxonomyConfig ];
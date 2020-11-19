module.exports = {
    transpileDependencies: ['vuetify'],
    publicPath: '',
    outputDir: '../server/public',
    devServer: {
        proxy: 'http://localhost:3000'
    }
}
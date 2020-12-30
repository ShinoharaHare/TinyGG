module.exports = {
    transpileDependencies: ['vuetify'],
    publicPath: '',
    outputDir: '../server/public',
    devServer: {
        proxy: 'http://localhost:80/TinyGG'
    }
}
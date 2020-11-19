module.exports = {
    transpileDependencies: ['vuetify'],
    publicPath: 'public',
    outputDir: '../server/public',
    devServer: {
        proxy: 'http://localhost:3000'
    }
}
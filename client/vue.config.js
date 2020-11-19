module.exports = {
    transpileDependencies: ['vuetify'],
    publicPath: process.env.NODE_ENV === 'production' ? 'public' : '',
    outputDir: '../server/public',
    devServer: {
        proxy: 'http://localhost:3000'
    }
}
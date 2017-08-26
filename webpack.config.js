module.exports = [

    {
        entry: {
            "ad-edit": "./app/views/admin/ad-edit",
            "ad-index": "./app/views/admin/ad-index",
            "cat-index": "./app/views/admin/cat-index",
            "cat-edit": "./app/views/admin/cat-edit",
            "settings": "./app/views/admin/settings",
            "ad-useredit": "./app/views/ad-useredit",
            "ad": "./app/views/ad",
            "ads": "./app/views/ads",
            "link-classified": "./app/components/link-classified.vue",
            "ad-meta": "./app/components/ad-meta.vue",
            "ad-photos": "./app/components/ad-photos.vue"
        },
        output: {
            filename: "./app/bundle/[name].js"
        },
        module: {
            loaders: [
                { test: /\.vue$/, loader: "vue-loader" }
            ]
        }
    }

];

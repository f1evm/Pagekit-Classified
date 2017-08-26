<template>

    <div class="uk-form-horizontal">

        <div class="uk-form-row">
            <label for="form-photos-title" class="uk-form-label">{{ 'Title' | trans }}</label>
            <div class="uk-form-controls">
                <input id="form-photos-title" class="uk-form-width-large" type="text" v-model="ad.data.photos['og:title']">
            </div>
        </div>

        <div class="uk-form-row">
            <label for="form-photos-description" class="uk-form-label">{{ 'Description' | trans }}</label>
            <div class="uk-form-controls">
                <textarea id="form-photos-description" class="uk-form-width-large" rows="5" type="text" v-model="ad.data.photos['og:description']"></textarea>
            </div>
        </div>

    </div>

</template>

<script>

    module.exports = {

        section: {
            label: 'Photos',
            priority: 100
        },

        props: ['ad']

    };

    window.Ad.components['ad-photos'] = module.exports;

</script>

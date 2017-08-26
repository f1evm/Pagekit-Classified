<template>

    <div class="uk-form-row">
        <label for="form-link-classified" class="uk-form-label">{{ 'View' | trans }}</label>
        <div class="uk-form-controls">
            <select id="form-link-classified" class="uk-width-1-1" v-model="link">
                <option value="@classified">{{ 'Ads View' | trans }}</option>
                <optgroup :label="'Ads' | trans">
                    <option v-for="p in ads" :value="p | link">{{ p.title }}</option>
                </optgroup>
            </select>
        </div>
    </div>

</template>

<script>

    module.exports = {

        link: {
            label: 'Classified'
        },

        props: ['link'],

        data: function () {
            return {
                ads: []
            }
        },

        created: function () {
            // TODO: Implement pagination or search
            this.$http.get('api/classified/ad', {filter: {limit: 1000}}).then(function (res) {
                this.$set('ads', res.data.ads);
            });
        },

        ready: function() {
            this.link = '@classified';
        },

        filters: {

            link: function (ad) {
                return '@classified/id?id='+ad.id;
            }

        }

    };

    window.Links.components['link-classified'] = module.exports;

</script>

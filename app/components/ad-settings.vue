<template>

    <div class="uk-grid pk-grid-large pk-width-sidebar-large uk-form-stacked" data-uk-grid-margin>
        <div class="pk-width-content">

        <div class="uk-form-row">
            <label for="form-category" class="uk-form-label">{{ 'Category' | trans }}</label>
            <div class="uk-form-controls">
                <select id="form-category" name="category" class="uk-width-1-1" v-model="ad.categ_id" v-validate:numeric>
                    <option value="">{{ 'Choose a category' | trans }}</option>
                    <option v-for="category in data.categories | orderBy 'priority'" :value="category.id">{{category.name}}</option>
                </select>
                <p class="uk-form-help-block uk-text-danger" v-show="form.category.invalid">{{ 'You must choose a category.' | trans }}</p>
            </div>
        </div>

            <div class="uk-form-row">
                <input class="uk-width-1-1 uk-form-large" type="text" name="title" :placeholder="'Enter Title' | trans" v-model="ad.title" v-validate:required>
                <p class="uk-form-help-block uk-text-danger" v-show="form.title.invalid">{{ 'Title cannot be blank.' | trans }}</p>
            </div>
            <div class="uk-form-row">
                <v-editor id="ad-content" :value.sync="ad.content" :options="{markdown : ad.data.markdown}"></v-editor>
            </div>

        </div>
        <div class="pk-width-sidebar">

            <div class="uk-panel">

                <div class="uk-form-row">
                    <label for="form-image" class="uk-form-label">{{ 'Photo' | trans }}</label>
                    <div class="uk-form-controls">
                        <input-image-meta :image.sync="ad.data.image" class="pk-image-max-height"></input-image-meta>
                    </div>
                </div>

                <div class="uk-form-row">
                    <label for="form-slug" class="uk-form-label">{{ 'Slug' | trans }}</label>
                    <div class="uk-form-controls">
                        <input id="form-slug" class="uk-width-1-1" type="text" v-model="ad.slug">
                    </div>
                </div>
                <div class="uk-form-row">
                    <label for="form-status" class="uk-form-label">{{ 'Status' | trans }}</label>
                    <div class="uk-form-controls">
                        <select id="form-status" class="uk-width-1-1" v-model="ad.status">
                            <option v-for="(id, status) in data.statuses" :value="id">{{status}}</option>
                        </select>
                    </div>
                </div>
                <div class="uk-form-row" v-if="data.canEditAll">
                    <label for="form-author" class="uk-form-label">{{ 'Author' | trans }}</label>
                    <div class="uk-form-controls">
                        <select id="form-author" class="uk-width-1-1" v-model="ad.user_id">
                            <option v-for="author in data.authors" :value="author.id">{{author.username}}</option>
                        </select>
                    </div>
                </div>
                <div class="uk-form-row">
                    <span class="uk-form-label">{{ 'Publish on' | trans }}</span>
                    <div class="uk-form-controls">
                        <input-date :datetime.sync="ad.date"></input-date>
                    </div>
                </div>

                <div class="uk-form-row">
                    <span class="uk-form-label">{{ 'Restrict Access' | trans }}</span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <p v-for="role in data.roles" class="uk-form-controls-condensed">
                            <label><input type="checkbox" :value="role.id" v-model="ad.roles" number> {{ role.name }}</label>
                        </p>
                    </div>
                </div>
                <div class="uk-form-row">
                    <span class="uk-form-label">{{ 'Options' | trans }}</span>
                    <div class="uk-form-controls">
                        <label><input type="checkbox" v-model="ad.data.markdown" value="1"> {{ 'Enable Markdown' | trans }}</label>
                    </div>
                </div>

            </div>

        </div>
    </div>

</template>

<script>

    module.exports = {

        props: ['ad', 'data', 'form'],

        section: {
            label: 'Ad'
        }

    };

</script>

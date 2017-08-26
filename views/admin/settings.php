<?php $view->script('classified-settings', 'classified:app/bundle/settings.js', 'vue') ?>

<div id="settings" class="uk-form uk-form-horizontal" v-cloak>

    <div class="uk-grid pk-grid-large" data-uk-grid-margin>
        <div class="pk-width-sidebar">

            <div class="uk-panel">

                <ul class="uk-nav uk-nav-side pk-nav-large" data-uk-tab="{ connect: '#tab-content' }">
                    <li><a><i class="pk-icon-large-settings uk-margin-right"></i> {{ 'General' | trans }}</a></li>
                    <li><a><i class="uk-icon-photo uk-icon-small uk-margin-right"></i> {{ 'Photos' | trans }}</a></li>
                </ul>

            </div>

        </div>
        <div class="pk-width-content">

            <ul id="tab-content" class="uk-switcher uk-margin">
                <li>

                    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
                        <div data-uk-margin>

                            <h2 class="uk-margin-remove">{{ 'General' | trans }}</h2>

                        </div>
                        <div data-uk-margin>

                            <button class="uk-button uk-button-primary" @click.prevent="save">{{ 'Save' | trans }}</button>

                        </div>
                    </div>

                    <div class="uk-form-row">
                        <span class="uk-form-label">{{ 'Permalink' | trans }}</span>
                        <div class="uk-form-controls uk-form-controls-text">
                            <p class="uk-form-controls-condensed">
                                <label>
                                    <input type="radio" v-model="config.permalink.type" value="">
                                    {{ 'Numeric' | trans }} <code>{{ '/123' | trans }}</code>
                                </label>
                            </p>
                            <p class="uk-form-controls-condensed">
                                <label>
                                    <input type="radio" v-model="config.permalink.type" value="{slug}">
                                    {{ 'Name' | trans }} <code>{{ '/sample-ad' | trans }}</code>
                                </label>
                            </p>
                            <p class="uk-form-controls-condensed">
                                <label>
                                    <input type="radio" v-model="config.permalink.type" value="{year}/{month}/{day}/{slug}">
                                    {{ 'Day and name' | trans }} <code>{{ '/2014/06/12/sample-ad' | trans }}</code>
                                </label>
                            </p>
                            <p class="uk-form-controls-condensed">
                                <label>
                                    <input type="radio" v-model="config.permalink.type" value="{year}/{month}/{slug}">
                                    {{ 'Month and name' | trans }} <code>{{ '/2014/06/sample-ad' | trans }}</code>
                                </label>
                            </p>
                            <p class="uk-form-controls-condensed">
                                <label>
                                    <input type="radio" v-model="config.permalink.type" value="custom">
                                    {{ 'Custom' | trans }}
                                </label>
                                <input class="uk-form-small" type="text" v-model="config.permalink.custom">
                            </p>
                        </div>
                    </div>

                    <div class="uk-form-row">
                        <label class="uk-form-label">{{ 'Ads per page' | trans }}</label>
                        <div class="uk-form-controls uk-form-controls-text">
                            <p class="uk-form-controls-condensed">
                                <input type="number" v-model="config.ads.ads_per_page" class="uk-form-width-small">
                            </p>
                        </div>
                    </div>

                    <div class="uk-form-row">
                        <span class="uk-form-label">{{ 'Default ad settings' | trans }}</span>
                        <div class="uk-form-controls uk-form-controls-text">
                            <p class="uk-form-controls-condensed">
                                <label><input type="checkbox" v-model="config.ads.markdown_enabled"> {{ 'Enable Markdown' | trans }}</label>
                            </p>
                        </div>
                    </div>

                </li>
                <li>

                    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
                        <div data-uk-margin>

                            <h2 class="uk-margin-remove">{{ 'Photos' | trans }}</h2>

                        </div>
                        <div data-uk-margin>

                            <button class="uk-button uk-button-primary" @click.prevent="save">{{ 'Save' | trans }}</button>

                        </div>
                    </div>

                    <div class="uk-form-row">
                        <span class="uk-form-label">{{ 'Photos' | trans }}</span>
                        <div class="uk-form-controls uk-form-controls-text">
                            <p class="uk-form-controls-condensed">
                                <label><input type="checkbox" v-model="config.photos.photos_enabled"> {{ 'Photos enabled.' | trans }}</label>
                            </p>
                            <p class="uk-form-controls-condensed" v-if="config.photos.photos_enabled">
                               {{ 'Max number of photos per ad ' | trans }}  <input class="uk-form-small uk-form-width-small" type="number" v-model="config.photos.photos_per_ad" min="1">
                            </p>
                        </div>
                    </div>


                </li>

            </ul>

        </div>
    </div>

</div>

<?php $view->script('cat-index', 'classified:app/bundle/cat-index.js', ['vue', 'uikit-nestable']) ?>


<div id="category" class="uk-form" v-cloak>


  <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
      <div class="uk-flex uk-flex-middle uk-flex-wrap" data-uk-margin>

          <h2 class="uk-margin-remove" v-if="!selected.length">{{ '{0} %count% Categories|{1} %count% Ad|]1,Inf[ %count% Categories' | transChoice count {count:count} }}</h2>

          <template v-else>
              <h2 class="uk-margin-remove">{{ '{1} %count% Category selected|]1,Inf[ %count% Categories selected' | transChoice selected.length {count:selected.length} }}</h2>

              <div class="uk-margin-left" >
                  <ul class="uk-subnav pk-subnav-icon">
                      <li><a class="pk-icon-check pk-icon-hover" title="Publish" data-uk-tooltip="{delay: 500}" @click="status(2)"></a></li>
                      <li><a class="pk-icon-block pk-icon-hover" title="Unpublish" data-uk-tooltip="{delay: 500}" @click="status(3)"></a></li>
                      <li><a class="pk-icon-copy pk-icon-hover" title="Copy" data-uk-tooltip="{delay: 500}" @click="copy"></a></li>
                      <li><a class="pk-icon-delete pk-icon-hover" title="Delete" data-uk-tooltip="{delay: 500}" @click="remove" v-confirm="'Delete Categories?'"></a></li>
                  </ul>
              </div>
          </template>

          <div class="pk-search">
              <div class="uk-search">
                  <input class="uk-search-field" type="text" v-model="config.filter.search" debounce="300">
              </div>
          </div>

      </div>
      <div data-uk-margin>

          <a class="uk-button uk-button-primary" :href="$url.route('/admin/classified/category/edit')">{{ 'Add Category' | trans }}</a>

      </div>
  </div>


  <div class="uk-overflow-container">
    <div class="pk-table-fake pk-table-fake-header pk-table-fake-border">
            <div class="pk-table-width-minimum"><input type="checkbox" v-check-all:selected.literal="input[name=id]" number></div>
            <div class="pk-table-width-100">{{ 'Image' | trans }}</div>
            <div class="pk-table-width-200" >{{ 'Name' | trans }}</div>
            <div class="pk-table-min-width-100">{{ 'Description' | trans }}</div>
            <div class="pk-table-width-100 uk-text-center">{{ 'Status' | trans }}</div>
            <div class="pk-table-width-100 uk-text-center">{{ 'Date' | trans }}</div>
        </div>


        <ul class="uk-nestable uk-margin-remove" v-el:nestable data-uk-nestable="{maxDepth:1}">
        <li class="uk-nestable-item check-item" v-for="category in categories" :class="{'uk-active': active(category)}" :data-id="category.id">
          <div class="uk-nestable-panel pk-table-fake uk-form uk-visible-hover">
            <div class="pk-table-width-minimum"><input type="checkbox" name="id" :value="category.id"></div>
            <div class="pk-table-width-100">
              <div v-if="isImage"><img :src="image"></div>
              <div v-else><img src="/packages/fevm/classified/assets/images/placeholder-icon.svg"></div>
            </div>
            <div class="pk-table-width-200">
                <a :href="$url.route('admin/classified/category/edit', { id: category.id })">{{ category.name }}</a>
            </div>
            <div class="pk-table-min-width-100">{{ category.description }}</div>
            <div class="pk-table-width-100 uk-text-center">
                <td class="uk-text-center">
                      <a :class="{
                              'pk-icon-circle': category.status == 0,
                              'pk-icon-circle-warning': category.status == 1,
                              'pk-icon-circle-success': category.status == 2 && category.published,
                              'pk-icon-circle-danger': category.status == 3,
                              'pk-icon-schedule': category.status == 2 && !category.published
                          }" @click="toggleStatus(category)"></a>
                </td>
            </div>
            <div class="pk-table-width-100  uk-text-center" > {{ category.date | date }} </div>
          </div>
        </li>
        </ul>

  </div>

  <h3 class="uk-h1 uk-text-muted uk-text-center" v-show="categories && !categories.length">{{ 'No category found.' | trans }}</h3>

  <v-pagination :page.sync="config.page" :pages="pages" v-show="pages > 1 || page > 0"></v-pagination>





</div>

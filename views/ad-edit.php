<?php $view->script('upload', '/app/assets/uikit/js/components/upload.min.js', ['uikit']) ?>
<?php $view->script('ad-useredit', 'classified:app/bundle/ad-useredit.js', ['vue','editor', 'uikit']) ?>
<?php $view->style('ads','classified:assets/css/classified.css', 'UIkit') ?>
<?php $view->style('place','/app/assets/uikit/css/components/placeholder.gradient.min.css', 'UIkit') ?>
<?php $view->style('form-file','/app/assets/uikit/css/components/form-file.gradient.min.css', 'UIkit') ?>
<?php $view->style('progress','/app/assets/uikit/css/components/progress.gradient.min.css', 'UIkit') ?>
<?php $view->style('slidenav','/app/assets/uikit/css/components/slidenav.gradient.min.css', 'UIkit') ?>

<form id="aduser" class="uk-form" v-validator="form" @submit.prevent="save | valid" v-cloak>

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div data-uk-margin>

            <h2 class="uk-margin-remove" v-if="ad.id">{{ 'Edit Ad' | trans }}</h2>
            <h2 class="uk-margin-remove" v-else>{{ 'Add Ad' | trans }}</h2>

        </div>
        <div data-uk-margin>

            <a class="uk-button uk-margin-small-right" :href="$url.route('classified')">{{ ad.id ? 'Close' : 'Cancel' | trans }}</a>
            <button class="uk-button uk-button-primary" type="submit">{{ 'Save' | trans }}</button>

        </div>
    </div>


    <ul class="uk-tab" data-uk-tab="{connect:'#aduser-id'}">
      <li><a href="">{{ 'Ad' | trans }}</a></li>
      <li><a href="">{{ 'Photos' | trans }}</a></li>
    </ul>

    <ul id="aduser-id" class="uk-switcher evm_ca_ad_edit_folder" >
    <li id="my-ad" class="uk-margin-large">

          <div class="uk-grid uk-grid-large uk-form data-uk-grid-match">
              <div class="uk-width-1-1 uk-form-stacked ">

                <div class="uk-form-row">
                    <label for="form-category" class="uk-form-label">{{ 'Category' | trans }}</label>
                    <div class="uk-form-controls">
                        <select id="form-category" class="uk-width-1-1 uk-form-large" name="category" v-model="ad.categ_id" v-validate:numeric >
                            <option  value='' disabled>{{'Choose a category.' | trans }}</option>
                            <option v-for="category in data.categories | orderBy 'priority'" :value="category.id">{{category.name}}</option>
                        </select>
                        <p class="uk-form-help-block uk-text-danger" v-show="form.category.invalid">{{ 'You must choose a category.' | trans }}</p>
                    </div>
                </div>


                  <div class="uk-form-row">
                    <label for="form-title" class="uk-form-label">{{ 'Title' | trans }}</label>
                    <div class="uk-form-controls">
                      <input id="form-title" class="uk-width-1-1 uk-form-large" type="text" name="title" :placeholder="'Enter Title' | trans" v-model="ad.title" v-validate:required>
                      <p class="uk-form-help-block uk-text-danger" v-show="form.title.invalid">{{ 'Title cannot be blank.' | trans }}</p>
                  </div>
                </div>

                  <div class="uk-form-row" style="width:100%;">
                    <label for="form-content" class="uk-form-label uk-display-inline-block">{{ 'Text of the ad.' | trans }}</label>

                            <span class="uk-float-right"><input type="checkbox" v-model="ad.data.markdown" value="1"> {{ 'Enable Markdown' | trans }}</span>

                    <div class="uk-form-controls" style="width:100%;">
                    <textarea  id="form-content"  rows="10" style="width:100%;" v-model="ad.content"  ></textarea>
                <!--     <v-editor id="ad-content" :value.sync="ad.content" :options="{markdown : ad.data.markdown}"></v-editor>  -->
                  </div>
                </div>


                <div class="uk-form-row uk-hidden">
                      <label for="form-slug" class="uk-form-label">{{ 'Slug' | trans }}</label>
                      <div class="uk-form-controls">
                          <input id="form-slug" class="uk-width-1-1" type="text" v-model="ad.slug">
                      </div>
                  </div>


              </div>


          </div>




  </li>

    <li id="my-ad-photos" class="uk-margin-large">
      <div><?= __('Max number of Photos:') ?> {{maxPhotos}} </div>
      <div v-if="ad.id">{{'{0} You cannot add any photos!|{1} You can still add %count% photo.|]1,Inf[ You can still add %count% photos.' | transChoice canAddPhotos {count:canAddPhotos} }}</div>
<p></p>

<div v-if="!ad.id"><em><?= __('Please save this ad a first time before uploading pictures.')?></em></div>

      <div class="uk-form-row" v-bind:class="canAddPhotos ?'' : 'uk-hidden' " >
        <div id="upload-drop" class="uk-placeholder uk-text-center">
              <i class="uk-icon-cloud-upload uk-icon-medium uk-text-muted uk-margin-small-right">
              </i> <?=__('Attach image files by dropping them here or')?> <a class="uk-form-file"><?=__('selecting them')?><input id="upload-select" type="file" multiple></input></a>.
        </div>
        <div id="progressbar" class="uk-progress  uk-progress-striped uk-active uk-hidden">
              <div class="uk-progress-bar" style="width: 0%;">0%</div>
        </div>
    </div>

    <div class="uk-form-row uk-margin-large-top uk-grid uk-grid-width-1-3">
    <figure class="uk-text-center uk-margin-top uk-margin-bottom uk-vertical-align uk-overlay" v-for="photo in ad.photos">
      <!--    <figure class="uk-overlay" style="width:100%, height:100%">  -->
              <img :src="$url('storage/fevm/classified/' + ad.id + '/' + photo)">
              <figcaption class="uk-h3 uk-text-right evm_ca_overlay_panel " >
                <a src="" class="uk-icon-check-square-o" style="color:#1dea1d" v-if="ad.ad_photo == $index"></a>
                <a src="" class="uk-icon-square-o" style="color:#444"  v-else @click="select($index)"></a>
                &nbsp;
                <a src="" class="uk-icon-trash-o" style="color:#444"  v-else @click="erase($index)"></a>

              </figcaption>
    <!--      </figure> -->


  </figure>


  </div>

  </li>
</ul>


</form>

<script>
$(function(){

    var progressbar = $("#progressbar"),
        bar         = progressbar.find('.uk-progress-bar'),
        $adId = $data.ad['id'],
        settings    = {

        action: 'upload',
        params: {'id': $adId}, //ad.id},
        allow : '*.(jpg|jpeg|gif|png|svg)', // allow only images
        single: false,


        filelimit: 6,


        loadstart: function() {
            bar.css("width", "0%").text("0%");
            progressbar.removeClass("uk-hidden");
        },

        progress: function(percent) {
            percent = Math.ceil(percent);
            bar.css("width", percent+"%").text(percent+"%");
        },

        allcomplete: function(response) {

            bar.css("width", "100%").text("100%");

            setTimeout(function(){
                progressbar.addClass("uk-hidden");
            }, 250);
            $r = "Upload Completed : " + response ;
            alert($r);
            location.reload();

        }
    };



    var select = UIkit.uploadSelect($("#upload-select"), settings),
        drop   = UIkit.uploadDrop($("#upload-drop"), settings);
});

</script>

<?php $view->script('ad', 'classified:app/bundle/ad.js', 'vue') ?>
<?php $view->style('ads','classified:assets/css/classified.css', 'UIkit') ?>
<?php $view->style('slidenav','/app/assets/uikit/css/components/slidenav.gradient.min.css', 'UIkit') ?>

<article class="uk-article">


    <?php if (count($ad->photos)): ?>
      <?php $image = "/storage/fevm/classified/" . "$ad->id" . "/" .$ad->photos[$ad->ad_photo]; ?>
      <img src="<?= $image ?>" class="evm_ca_img_thumb">
    <?php endif ?>

    <span class="uk-article-title"><?= $ad->title ?></span>

    <p class="uk-article-meta">
        <?= __('Written by %username% %name% on %date%', ['%username%' => $this->escape($ad->user->username), '%name%' => $this->escape($ad->user->name), '%date%' => '<time datetime="'.$ad->date->format(\DateTime::ATOM).'" v-cloak>{{ "'.$ad->date->format(\DateTime::ATOM).'" | date "longDate" }}</time>' ]) ?>
    </p>

    <div class="uk-margin"><?= $ad->content ?></div>

    <p></p>
    <?php  if ($l = count($ad->photos)): ?>

        <div class="uk-grid uk-grid-width-medium-1-4 uk-margin-large-top" data-uk-grid-margin>

        <?php
          $x = '/storage/fevm/classified/' . $ad->id.'/';
          for ($i = 0; $i < $l; $i++){
            $photo = $x . $ad->photos[$i];
          ?>
            <div class="uk-text-center uk-vertical-align uk-slidenav-position">
                <a href="<?= $photo ?>" data-uk-lightbox="{group:'group1'}" >
                    <img src="<?= $photo ?>" >
                </a>
                <a href="" class="uk-slidenav uk-slidenav-previous"></a>
                <a href="" class="uk-slidenav uk-slidenav-next"></a>
            </div>
        <?php
         } endif ?>


     </div>


</article>

<?php $view->script('ads', 'classified:app/bundle/ads.js', 'vue') ?>
<?php $view->style('ads','classified:assets/css/classified.css', 'UIkit') ?>

<div id="ads-user">
  <div><img src="/packages/fevm/classified/assets/images/work in progress.png"  style="height:100px"><span>Cette application est encore en cours de développement.</span></div>
  <div id="ads-page-title" class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
      <div class="uk-flex uk-flex-middle uk-flex-wrap uk-padding-large uk-alert" data-uk-margin>
          Les Petites Annonces de l'ARAM53
      </div>
  <div  data-uk-margin>
    <?php if ($isAuthenticated && ($canEditAll || $canEditOwn)) { ?>
    <a class="uk-button uk-button-primary" href="<?= $view->url('@classified/ad/edit') ?>"><?= __('Write a new Classified Ad.') ?></a>
  <?php } else { ?>
    <span class="uk-button uk-button-secondary"><?= __('You must be authenticated to write an ad.') ?></span>
  <?php } ?>
  </div>
</div>

  <?php foreach ($ads as $ad) : ?>
<article class="evm_ca_ad">

    <div class="evm_ca_ad_header uk-button-primary">
      <span class="uk-margin-right">N° <?= $ad->id ?></span>
      <span>  <a class="uk-button-primary" href="<?= $view->url('@classified/id', ['id' => $ad->id]) ?>"><?= $ad->title ?></a></span>
      <span class="uk-float-right"><?= $ad->date->format(__('Y-m-d')) ?> </span>
    </div>

    <div class="uk-flex">
      <div class="uk-vertical-align uk-flex-left">
        <?php if (!count($ad->photos)): ?>
          <?php $image = '/packages/fevm/classified/assets/images/placeholder-icon.svg';  ?>
        <?php else: ?>
          <?php $image = '/storage/fevm/classified/' . $ad->id . '/' . $ad->photos[$ad->ad_photo]; ?>
        <?php endif ?>
        <a class="" href="<?= $view->url('@classified/id', ['id' => $ad->id]) ?>"><img src="<?= $image ?>" class="evm_ca_img_thumb"></a>
      </div>
      <div class="uk-width-1-1">
        <p id="ad-categ" class="evm_ca_ad_category">
          <?=__('Category: ') . ($ad->category ? $ad->category->name : "<i>".__('undefined')."</i>") ?>
        </p>
        <?= $ad->content ?>
      </div>
    </div>

<div>
    <p class="evm_ca_ad-meta">

    <?php if ($emailsActive){ ?>
<!--        <a href="" class="" title="<?= __('Send to a friend.') ?>"><i class="uk-margin-small-left uk-margin-small-right uk-icon-paper-plane"> </i></a>
        <a href="" class="uk-padding" title="<?= __('Answer to the Ad.') ?>"><i class="uk-margin-small-left uk-margin-small-right uk-icon-envelope"> </i></a>
-->
        <a href="" class="" title="<?= __('Fonction à venir.') ?>"><i class="uk-margin-small-left uk-margin-small-right uk-icon-paper-plane"> </i></a>
        <a href="" class="uk-padding" title="<?= __('Fonction à venir.') ?>"><i class="uk-margin-small-left uk-margin-small-right uk-icon-envelope"> </i></a>
    <?php } ?>
        <?php if ($isAuthenticated && ($canEditAll || (($user->username == $ad->getAuthor()) && $canEditOwn))){ ?>
          <a href="<?= $view->url('@classified/ad/edit', [ 'id' => $ad->id ]) ?>" class="" title="<?= __('Edit the Ad.') ?>"><i class="uk-margin-small-left uk-margin-small-right uk-icon-edit"> </i></a>
        <?php } ?>
        <?= __('Written by %username% %name% on %date%', ['%username%' => $this->escape($ad->user->username), '%name%' => $this->escape($ad->user->name), '%date%' => '<time datetime="'.$ad->date->format(\DateTime::ATOM).'" v-cloak>{{ "'.$ad->date->format(\DateTime::ATOM).'" | date "longDate" }}</time>' ]) ?>
    </p>

  </div>


</article>
<?php endforeach ?>
<?php
if (!$ads || !count($ads)){ ?>
<h3 class="uk-h1 uk-text-muted uk-text-center"><?= __('No ads found.') ?></h3>
<?php
} ?>
<?php

    $range     = 3;
    $total     = intval($total);
    $page      = intval($page);
    $pageIndex = $page - 1;

?>

<?php if ($total > 1) : ?>
<ul class="uk-pagination">


    <?php for($i=1;$i<=$total;$i++): ?>
        <?php if ($i <= ($pageIndex+$range) && $i >= ($pageIndex-$range)): ?>

            <?php if ($i == $page): ?>
            <li class="uk-active"><span><?=$i?></span></li>
            <?php else: ?>
            <li>
                <a href="<?= $view->url('@classified/page', ['page' => $i]) ?>"><?=$i?></a>
            <li>
            <?php endif; ?>

        <?php elseif($i==1): ?>

            <li>
                <a href="<?= $view->url('@classified/page', ['page' => 1]) ?>">1</a>
            </li>
            <li><span>...</span></li>

        <?php elseif($i==$total): ?>

            <li><span>...</span></li>
            <li>
                <a href="<?= $view->url('@classified/page', ['page' => $total]) ?>"><?=$total?></a>
            </li>

        <?php endif; ?>
    <?php endfor; ?>


</ul>
<?php endif ?>
</div>

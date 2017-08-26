<?php

namespace fevm\Classified\Controller;

use Pagekit\Application as App;
use fevm\Classified\Model\Ad;
use fevm\Classified\Model\Category;
use Pagekit\Module\Module;
use Pagekit\User\Model\Role;

class SiteController
{
    /**
     * @var Module
     */
    protected $classified;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->classified = App::module('classified');
    }

    /**
     * @Route("/")
     * @Route("/page/{page}", name="page", requirements={"page" = "\d+"})
     */
    public function indexAction($page = 1)
    {
        $user = App::user();

        $query = Ad::where(['status = ?', 'date < ?'], [Ad::STATUS_PUBLISHED, new \DateTime]);
  /*      $query->orWhere(function($query){
          $query->where(['user_id = ?'],['$user->id']);
        });
  */
        //->orwhere('$user->isAuthenticated' && ['user_id = ?'],[$user->id]);
        /* ->where(function ($query) {
            return $query->where('roles IS NULL')->whereInSet('roles', App::user()->roles, false, 'OR');
        }) */
        $query = $query->related('user')->related('category');

        if (!$limit = $this->classified->config('ads.ads_per_page')) {
            $limit = 10;
        }

        $count = $query->count('id');

        $total = ceil($count / $limit);
        $page = max(1, min($total, $page));

        $query->offset(($page - 1) * $limit)->limit($limit)->orderBy('date', 'DESC');

        foreach ($ads = $query->get() as $ad) {
            $ad->excerpt = App::content()->applyPlugins($ad->excerpt, ['ad' => $ad, 'markdown' => $ad->get('markdown')]);
            $ad->content = App::content()->applyPlugins($ad->content, ['ad' => $ad, 'markdown' => $ad->get('markdown'), 'readmore' => true]);
        }

        return [
            '$view' => [
                'title' => __('Classified'),
                'name' => 'classified/ads.php',
                'link:feed' => [
                    'rel' => 'alternate',
                    'href' => App::url('@classified/feed'),
                    'title' => App::module('system/site')->config('title'),
                    'type' => App::feed()->create($this->classified->config('feed.type'))->getMIMEType()
                ]
            ],
            'emailsActive' => true,
            'classified' => $this->classified,
            'canEditAll' => App::user()->hasAccess('classified: manage all ads'),
            'canEditOwn' => App::user()->hasAccess('classified: manage own ads'),
            'user' => $user,
            'isAuthenticated'=> $user->isAuthenticated(),
            'ads' => $ads,
            'total' => $total,
            'page' => $page
        ];
    }

    /**
     * @Route("/ad/edit", name="/ad/edit")
     *
     * @access("classified: manage own ads || classified: manage all ads")
     * @Request({"id": "int"})
     */
    public function editAction($id = 0)
    {
        try {


          if (!$ad = Ad::where(compact('id'))->related('user')->first()) {

                if ($id) {
                    App::abort(404, __('Invalid ad id.'));
                }

                $module = App::module('classified');

                $ad = Ad::create([
                    'user_id' => App::user()->id,
                    'status' => Ad::STATUS_DRAFT,
                    'date' => new \DateTime(),
                    'categ_id' => ''
                ]);

                $ad->set('title', $module->config('ads.show_title'));
                $ad->set('markdown', $module->config('ads.markdown_enabled'));
            }

            $user = App::user();
            if(!$user->hasAccess('classified: manage all ads') && $ad->user_id !== $user->id) {
                App::abort(403, __('Insufficient User Rights.'));
            }

            $roles = App::db()->createQueryBuilder()
                ->from('@system_role')
                ->where(['id' => Role::ROLE_ADMINISTRATOR])
                ->whereInSet('permissions', ['classified: manage all ads', 'classified: manage own ads'], false, 'OR')
                ->execute('id')
                ->fetchAll(\PDO::FETCH_COLUMN);

            $authors = App::db()->createQueryBuilder()
                ->from('@system_user')
                ->whereInSet('roles', $roles)
                ->execute('id, username')
                ->fetchAll();


            return [
                '$view' => [
                    'title' => $id ? __('Edit Ad') : __('Add Ad'),
                    'name'  => 'classified/ad-edit.php'
                ],
                '$data' => [
                    'ad'     => $ad,
                    'statuses' => Ad::getStatuses(),
                    'config' => App::module('classified')->config(),
                    'categories' =>  Category::getCategories(),
                    'roles'    => array_values(Role::findAll()),
                    'canEditAll' => $user->hasAccess('classified: manage all ads'),
                    'authors'  => $authors
                ],
                'ad' => $ad
            ];

        } catch (\Exception $e) {

            App::message()->error($e->getMessage());

            return App::redirect('@classified');
        }
    }



        /**
         * @Route("/ad/upload", methods="POST", name="/ad/upload")
         *
         *
         */
        public function uploadAction()
        {

    if (isset($_POST['id'])){
      $id = $_POST['id'];

    if(isset($_FILES['files'])){
      $path = 'storage/fevm/';
      if (!file_exists($path)) {
          mkdir($path, 0755);

      }
      $path = 'storage/fevm/classified';
      if (!file_exists($path)) {
          mkdir($path, 0755);

      }

      $path = 'storage/fevm/classified/'.$id;
      if (!file_exists($path)) {
          mkdir($path, 0755);

      }

      $ad = Ad::find($id);

      $newfiles = array();
      $files = self::rearrange($_FILES['files']);
      foreach ($files as $file) {
        preg_match('/^.*\.(jpg|jpeg|png|svg|gif)$/i', $file['name'], $match);
        if (!array_key_exists(0, $match)) {
            App::abort(400, 'Only JPG / PNG / SVG / GIF is supported');
        }

        $file['name'] = str_replace($match[0], '', $file['name']);
        $new_filename = strtolower(time().'_'.App::filter($file['name'], 'slugify').$match[0]);
        $new_filename = str_replace(' ', '_', $new_filename);
        move_uploaded_file($file['tmp_name'], $path.'/'.$new_filename);
        array_push($newfiles, $new_filename);
        array_push($ad->photos, $new_filename);
        }


        $ad->save();

    return ['message' => 'success'];
  }}
  return ['message' => 'error'];
  }


  /**
   * Rearrange $_FILES array.
   *
   * @param $arr
   *
   * @return mixed
   */
  private function rearrange($arr)
  {
      foreach ($arr as $key => $all) {
          foreach ($all as $i => $val) {
              $new[$i][$key] = $val;
          }
      }

      return $new;
  }


    /**
     * @Route("/feed")
     * @Route("/feed/{type}")
     */
    public function feedAction($type = '')
    {
        // fetch locale and convert to ISO-639 (en_US -> en-us)
        $locale = App::module('system')->config('site.locale');
        $locale = str_replace('_', '-', strtolower($locale));

        $site = App::module('system/site');
        $feed = App::feed()->create($type ?: $this->classified->config('feed.type'), [
            'title' => $site->config('title'),
            'link' => App::url('@classified', [], 0),
            'description' => $site->config('description'),
            'element' => ['language', $locale],
            'selfLink' => App::url('@classified/feed', [], 0)
        ]);

        if ($last = Ad::where(['status = ?', 'date < ?'], [Ad::STATUS_PUBLISHED, new \DateTime])->limit(1)->orderBy('modified', 'DESC')->first()) {
            $feed->setDate($last->modified);
        }

        foreach (Ad::where(['status = ?', 'date < ?'], [Ad::STATUS_PUBLISHED, new \DateTime])->where(function ($query) {
            return $query->where('roles IS NULL')->whereInSet('roles', App::user()->roles, false, 'OR');
        })->related('user')->limit($this->classified->config('feed.limit'))->orderBy('date', 'DESC')->get() as $ad) {
            $url = App::url('@classified/id', ['id' => $ad->id], 0);
            $feed->addItem(
                $feed->createItem([
                    'title' => $ad->title,
                    'link' => $url,
                    'description' => App::content()->applyPlugins($ad->content, ['ad' => $ad, 'markdown' => $ad->get('markdown'), 'readmore' => true]),
                    'date' => $ad->date,
                    'author' => [$ad->user->name, $ad->user->email],
                    'id' => $url
                ])
            );
        }

        return App::response($feed->output(), 200, ['Content-Type' => $feed->getMIMEType().'; charset='.$feed->getEncoding()]);
    }

    /**
     * @Route("/{id}", name="id")
     */
    public function adAction($id = 0)
    {
        if (!$ad = Ad::where(['id = ?', 'status = ?', 'date < ?'], [$id, Ad::STATUS_PUBLISHED, new \DateTime])->related('user')->first()) {
            App::abort(404, __('Ad not found!'));
        }

        if (!$ad->hasAccess(App::user())) {
            App::abort(403, __('Insufficient User Rights.'));
        }

        $ad->excerpt = App::content()->applyPlugins($ad->excerpt, ['ad' => $ad, 'markdown' => $ad->get('markdown')]);
        $ad->content = App::content()->applyPlugins($ad->content, ['ad' => $ad, 'markdown' => $ad->get('markdown')]);

        $user = App::user();

        $description = $ad->get('meta.og:description');
        if (!$description) {
            $description = strip_tags($ad->excerpt ?: $ad->content);
            $description = rtrim(mb_substr($description, 0, 150), " \t\n\r\0\x0B.,") . '...';
        }

        return [
            '$view' => [
                'title' => __($ad->title),
                'name' => 'classified/ad.php',
                'og:type' => 'article',
                'article:published_time' => $ad->date->format(\DateTime::ATOM),
                'article:modified_time' => $ad->modified->format(\DateTime::ATOM),
                'article:author' => $ad->user->name,
                'og:title' => $ad->get('meta.og:title') ?: $ad->title,
                'og:description' => $description,
                'og:image' =>  $ad->get('image.src') ? App::url()->getStatic($ad->get('image.src'), [], 0) : false
            ],
            'classified' => $this->classified,
            'ad' => $ad
        ];
    }



    protected function sendWelcomeEmail($user)
    {
        try {

            $mail = App::mailer()->create();
            $mail->setTo($user->email)
                ->setSubject(__('Welcome to %site%!', ['%site%' => App::module('system/site')->config('title')]))
                ->setBody(App::view('system/user:mails/welcome.php', compact('user', 'mail')), 'text/html')
                ->send();

        } catch (\Exception $e) {
        }
    }


}

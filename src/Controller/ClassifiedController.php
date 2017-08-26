<?php

namespace fevm\Classified\Controller;

use Pagekit\Application as App;
use fevm\Classified\Model\Ad;
use fevm\Classified\Model\Category;
use Pagekit\User\Model\Role;

/**
 * @Access(admin=true)
 */
class ClassifiedController
{
    /**
     * @Access("classified: manage own ads || classified: manage all ads")
     * @Request({"filter": "array", "page":"int"})
     */
    public function adAction($filter = null, $page = null)
    {
        return [
            '$view' => [
                'title' => __('Ads'),
                'name'  => 'classified/admin/ad-index.php'
            ],
            '$data' => [
                'statuses' => Ad::getStatuses(),
                'authors'  => Ad::getAuthors(),
                'categories' => Category::getCategories(),
                'canEditAll' => App::user()->hasAccess('classified: manage all ads'),
                'config'   => [
                    'filter' => (object) $filter,
                    'page'   => $page
                ]
            ]
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
                    'date' => new \DateTime()
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
                    'name'  => 'classified/admin/ad-edit.php'
                ],
                '$data' => [
                    'ad'     => $ad,
                    'statuses' => Ad::getStatuses(),
                    'categories' => Category::getCategories(),
                    'roles'    => array_values(Role::findAll()),
                    'canEditAll' => $user->hasAccess('classified: manage all ads'),
                    'authors'  => $authors
                ],
                'ad' => $ad
            ];

        } catch (\Exception $e) {

            App::message()->error($e->getMessage());

            return App::redirect('@classified/ad');
        }
    }



    /**
     * @Access("system: access settings")
     */
    public function settingsAction()
    {
        return [
            '$view' => [
                'title' => __('Classified Settings'),
                'name'  => 'classified/admin/settings.php'
            ],
            '$data' => [
                'config' => App::module('classified')->config()
            ]
        ];
    }
}

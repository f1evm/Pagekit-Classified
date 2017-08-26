<?php

namespace fevm\Classified\Controller;

use Pagekit\Application as App;
use fevm\Classified\Model\Ad;
use fevm\Classified\Model\Category;
use Pagekit\User\Model\Role;


/**
 * @Access(admin=true)
 */
class CategoryController
{
  /**
   * @Access("classified: manage categories")
   * @Request({"filter": "array", "page":"int"})
   */
  public function indexAction($filter = null, $page = null)
  {
      return [
          '$view' => [
              'title' => __('Categories'),
              'name'  => 'classified/admin/cat-index.php'
          ],
          '$data' => [
              'statuses' => Category::getStatuses(),
              'canEditAll' => App::user()->hasAccess('classified: manage categories'),
              //'categories' => Category::findAll(),
              'config'   => [
                  'filter' => (object) $filter,
                  'page'   => $page
              ]
          ]
      ];
  }

    /**
     * @Access("classified: manage categories")
     *
     */
    public function testAction()
    {
        $categories = Category::findAll();
        $page= 0;

        return [
            '$view' => [
                'title' => __('Categories'),
                'name'  => 'classified/admin/cat-index.php'
            ],
            '$data' => [
                'config'   => [
                    'page'   => $page
                ]
            ],
            'categories' => $categories
        ];
    }

    /**
     *
     *
     * @access("classified: manage categories")
     * @Request({"id": "int"})
     */
    public function editAction($id = 0)
    {
        try {

            if (!$category = Category::where(compact('id'))->first()) {

                if ($id) {
                    App::abort(404, __('Invalid category id.'));
                }

                $module = App::module('classified');
                $nbCategories = count(Category::getCategories());
                $category = Category::create([
                    'status' => Category::STATUS_DRAFT,
                    'date' => new \DateTime(),
                    'priority' => $nbCategories
                ]);
                $category->set('name', $module->config('categories.show_name'));
                $category->set('markdown', $module->config('categories.markdown_enabled'));
            }



            $user = App::user();
            if(!$user->hasAccess('classified: manage categories')) {
                App::abort(403, __('Insufficient User Rights.'));
            }

            $roles = App::db()->createQueryBuilder()
                ->from('@system_role')
                ->where(['id' => Role::ROLE_ADMINISTRATOR])
                ->whereInSet('permissions', ['classified: manage categories'], false, 'OR')
                ->execute('id')
                ->fetchAll(\PDO::FETCH_COLUMN);

            $authors = App::db()->createQueryBuilder()
                ->from('@system_user')
                ->whereInSet('roles', $roles)
                ->execute('id, username')
                ->fetchAll();

            return [
                '$view' => [
                    'title' => $id ? __('Edit Category') : __('Add Category'),
                    'name'  => 'classified/admin/cat-edit.php'
                ],
                '$data' => [
                    'category'     => $category,
                    'statuses' => Category::getStatuses(),
                    'roles'    => array_values(Role::findAll()),
                    'canEditAll' => $user->hasAccess('classified: manage categories'),
                    'authors'  => $authors
                ],
                'category' => $category
            ];

        } catch (\Exception $e) {

            App::message()->error($e->getMessage());

            return App::redirect('@category');
        }
    }




}

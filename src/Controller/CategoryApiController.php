<?php

namespace fevm\Classified\Controller;

use Pagekit\Application as App;
use fevm\Classified\Model\Category;

/**
 * @Access("classified: manage categories")
 *
 */
class CategoryApiController
{
    /**
     * @Route("/", methods="GET")
     * @Request({"filter": "array", "page":"int"})
     */
    public function indexAction($filter = [], $page = 0)
    {

        $query  = Category::query();
        $filter = array_merge(array_fill_keys(['status', 'search', 'order', 'limit'], ''), $filter);

        extract($filter, EXTR_SKIP);

/*        if(!App::user()->hasAccess('classified: manage categories')) {
            $author = App::user()->id;
        }
*/
        if (is_numeric($status)) {
            $query->where(['status' => (int) $status]);
        }

        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->orWhere(['name LIKE :search', 'slug LIKE :search'], ['search' => "%{$search}%"]);
            });
        }

/*        if ($author) {
            $query->where(function ($query) use ($author) {
                $query->orWhere(['user_id' => (int) $author]);
            });
        }
*/
        if (!preg_match('/^(date|title|priority)\s(asc|desc)$/i', $order, $order)) {
            $order = [1 => 'id', 2 => 'asc'];
        }


        $limit = (int) $limit ?: App::module('classified')->config('categories.categories_per_page');
        $count = $query->count();
        $pages = ceil($count / $limit);
        $page  = max(0, min($pages - 1, $page));

        $categories = array_values($query->offset($page * $limit)->limit($limit)->orderBy($order[1], $order[2])->get());

        return compact('categories', 'pages', 'count');
    }

    /**
     * @Route("/{id}", methods="GET", requirements={"id"="\d+"})
     */
    public function getAction($id)
    {
        return Category::where(compact('id'))->first();
    }

    /**
     * @Route("/", methods="POST")
     * @Route("/{id}", methods="POST", requirements={"id"="\d+"})
     * @Request({"category": "array", "id": "int"}, csrf=true)
     */
    public function saveAction($data, $id = 0)
    {
        if (!$id || !$category = Category::find($id)) {

            if ($id) {
                App::abort(404, __('Category not found.'));
            }

            $category = Category::create();
        }

        if (!$data['slug'] = App::filter($data['slug'] ?: $data['name'], 'slugify')) {
            App::abort(400, __('Invalid slug.'));
        }

        // user without universal access is not allowed to assign categories to other users
        if(!App::user()->hasAccess('classified: manage categories')) {
            $data['user_id'] = App::user()->id;
        }

        // user without universal access can only edit their own categories
        if(!App::user()->hasAccess('classified: manage categories')) {
            App::abort(400, __('Access denied.'));
        }

        $category->save($data);

        return ['message' => 'success', 'category' => $category];
    }

    /**
     * @Route("/{id}", methods="DELETE", requirements={"id"="\d+"})
     * @Request({"id": "int"}, csrf=true)
     */
    public function deleteAction($id)
    {
        if ($category = Category::find($id)) {

            if(!App::user()->hasAccess('classified: manage categories')) {
                App::abort(400, __('Access denied.'));
            }

            $category->delete();
        }

        return ['message' => 'success'];
    }

    /**
     * @Route(methods="POST")
     * @Request({"ids": "int[]"}, csrf=true)
     */
    public function copyAction($ids = [])
    {
        foreach ($ids as $id) {
            if ($category = Category::find((int) $id)) {
                if(!App::user()->hasAccess('classified: manage categories')) {
                    continue;
                }

                $category = clone $category;
                $category->id = null;
                $category->status = Category::STATUS_DRAFT;
                $category->name = $category->name.' - '.__('Copy');
                $category->date = new \DateTime();
                $category->save();
            }
        }

        return ['message' => 'success'];
    }

    /**
     * @Route("/bulk", methods="POST")
     * @Request({"categories": "array"}, csrf=true)
     */
    public function bulkSaveAction($categories = [])
    {
        foreach ($categories as $data) {
            $this->saveAction($data, isset($data['id']) ? $data['id'] : 0);
        }

        return ['message' => 'success'];
    }

    /**
     * @Route("/bulk", methods="DELETE")
     * @Request({"ids": "array"}, csrf=true)
     */
    public function bulkDeleteAction($ids = [])
    {
        foreach (array_filter($ids) as $id) {
            $this->deleteAction($id);
        }

        return ['message' => 'success'];
    }


    /**
     * @Route("/updateOrder", methods="POST")
     * @Request({"categories": "array"}, csrf=true)
     */
    public function updateOrderAction($categories = [])
    {

        foreach ($categories as $data) {

            if ($category = Category::find($data['id'])) {

                $category->priority  = $data['order'];
                $category->parent_id = $data['parent_id'] ?: 0;

                $category->save();
            }
        }

        return ['message' => 'success'];
    }


}

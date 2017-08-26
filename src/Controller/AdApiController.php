<?php

namespace fevm\Classified\Controller;

use Pagekit\Application as App;
use fevm\Classified\Model\Ad;

/**
 * @Access("classified: manage own ads || classified: manage all ads")
 * @Route("ad", name="ad")
 */
class AdApiController
{
    /**
     * @Route("/", methods="GET")
     * @Request({"filter": "array", "page":"int"})
     */
    public function indexAction($filter = [], $page = 0)
    {
        $query  = Ad::query()->related('category');
        $filter = array_merge(array_fill_keys(['status', 'search', 'author', 'order', 'limit'], ''), $filter);

        extract($filter, EXTR_SKIP);

        if(!App::user()->hasAccess('classified: manage all ads')) {
            $author = App::user()->id;
        }

        if (is_numeric($status)) {
            $query->where(['status' => (int) $status]);
        }

        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->orWhere(['title LIKE :search', 'slug LIKE :search'], ['search' => "%{$search}%"]);
            });
        }

        if ($author) {
            $query->where(function ($query) use ($author) {
                $query->orWhere(['user_id' => (int) $author]);
            });
        }

        if (!preg_match('/^(date|title)\s(asc|desc)$/i', $order, $order)) {
            $order = [1 => 'date', 2 => 'desc'];
        }

        $limit = (int) $limit ?: App::module('classified')->config('ads.ads_per_page');
        $count = $query->count();
        $pages = ceil($count / $limit);
        $page  = max(0, min($pages - 1, $page));

        $ads = array_values($query->offset($page * $limit)->related('user')->limit($limit)->orderBy($order[1], $order[2])->get());

        return compact('ads', 'pages', 'count');
    }

    /**
     * @Route("/{id}", methods="GET", requirements={"id"="\d+"})
     */
    public function getAction($id)
    {
        return Ad::where(compact('id'))->related('user')->first();
    }

    /**
     * @Route("/", methods="POST")
     * @Route("/{id}", methods="POST", requirements={"id"="\d+"})
     * @Request({"ad": "array", "id": "int"}, csrf=true)
     */
    public function saveAction($data, $id = 0)
    {
        if (!$id || !$ad = Ad::find($id)) {

            if ($id) {
                App::abort(404, __('Ad not found.'));
            }

            $ad = Ad::create();
        }

        if (!$data['slug'] = App::filter($data['slug'] ?: $data['title'], 'slugify')) {
            App::abort(400, __('Invalid slug.'));
        }

        // user without universal access is not allowed to assign ads to other users
        if(!App::user()->hasAccess('classified: manage all ads')) {
            $data['user_id'] = App::user()->id;
        }

        // user without universal access can only edit their own ads
        if(!App::user()->hasAccess('classified: manage all ads') && !App::user()->hasAccess('classified: manage own ads') && $ad->user_id !== App::user()->id) {
            App::abort(400, __('Access denied.'));
        }

        $ad->save($data);

        return ['message' => 'success', 'ad' => $ad];
    }

    /**
     * @Route("/{id}", methods="DELETE", requirements={"id"="\d+"})
     * @Request({"id": "int"}, csrf=true)
     */
    public function deleteAction($id)
    {
        if ($ad = Ad::find($id)) {

            if(!App::user()->hasAccess('classified: manage all ads') && !App::user()->hasAccess('classified: manage own ads') && $ad->user_id !== App::user()->id) {
                App::abort(400, __('Access denied.'));
            }

            $ad->delete();
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
            if ($ad = Ad::find((int) $id)) {
                if(!App::user()->hasAccess('classified: manage all ads') && !App::user()->hasAccess('classified: manage own ads') && $ad->user_id !== App::user()->id) {
                    continue;
                }

                $ad = clone $ad;
                $ad->id = null;
                $ad->status = Ad::STATUS_DRAFT;
                $ad->title = $ad->title.' - '.__('Copy');
                $ad->date = new \DateTime();
                $ad->save();
            }
        }

        return ['message' => 'success'];
    }

    /**
     * @Route("/bulk", methods="POST")
     * @Request({"ads": "array"}, csrf=true)
     */
    public function bulkSaveAction($ads = [])
    {
        foreach ($ads as $data) {
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
     * @Route("/delPhoto", methods="POST")
     * @Route("/delPhoto{id}", methods="POST", requirements={"id"="\d+"})
     * @Request({"num": "int", "id": "int", "adp": "int"}, csrf=true)
     */
    public function delPhotoAction($num, $id, $adp)
    {
      if ($ad = Ad::find($id)) {

            if(!App::user()->hasAccess('classified: manage all ads') && !App::user()->hasAccess('classified: manage own ads') && $ad->user_id !== App::user()->id) {
                App::abort(400, __('Access denied.'));
            }
            $photoToDelete = $ad->id . "/" .$ad->photos[$num];
            array_splice($ad->photos, $num, 1);
            $ad->ad_photo = $adp;
            $path = 'storage/fevm/';
            if (!file_exists($path)) {
                mkdir($path, 0755);
            }
            $path = 'storage/fevm/classified';
            if (!file_exists($path)) {
                mkdir($path, 0755);
            }

            $path = 'storage/fevm/classified/archives';
            if (!file_exists($path)) {
                mkdir($path, 0755);
            }

            $path = 'storage/fevm/classified/archives/'.$id;
            if (!file_exists($path)) {
                mkdir($path, 0755);
            }
            rename("storage/fevm/classified/" . $photoToDelete, "storage/fevm/classified/archives/" . $photoToDelete);
            $ad->save();
            return ['message' => 'success'];
        }

        return ['message' => 'error'];
    }


}

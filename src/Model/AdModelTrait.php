<?php

namespace fevm\Classified\Model;

use Pagekit\Application as App;
use Pagekit\Database\ORM\ModelTrait;

trait AdModelTrait
{
    use ModelTrait;


    /**
     * Get all users who have written an article
     */
    public static function getAuthors()
    {
        return self::query()->select('user_id', 'name', 'username')->groupBy('user_id', 'name', 'username')->join('@system_user', 'user_id = @system_user.id')->execute()->fetchAll();
    }

    /**
     * @Saving
     */
    public static function saving($event, Ad $ad)
    {
        $ad->modified = new \DateTime();

        $i  = 2;
        $id = $ad->id;

        while (self::where('slug = ?', [$ad->slug])->where(function ($query) use ($id) {
            if ($id) {
                $query->where('id <> ?', [$id]);
            }
        })->first()) {
            $ad->slug = preg_replace('/-\d+$/', '', $ad->slug).'-'.$i++;
        }
    }

    /**
     * @Deleting
     */
    public static function deleting($event, Ad $ad)
    {
        
    }
}

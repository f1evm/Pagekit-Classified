<?php

namespace fevm\Classified\Model;

use Pagekit\Application as App;
use Pagekit\System\Model\DataModelTrait;
//use Pagekit\User\Model\AccessModelTrait;
//use Pagekit\User\Model\User;

/**
 * @Entity(tableClass="@classified_category")
 */
class Category implements \JsonSerializable
{
    use DataModelTrait, CategoryModelTrait;

    /* Category draft status. */
    const STATUS_DRAFT = 0;

    /* Category pending review status. */
    const STATUS_PENDING_REVIEW = 1;

    /* Category published. */
    const STATUS_PUBLISHED = 2;

    /* Category unpublished. */
    const STATUS_UNPUBLISHED = 3;

    /** @Column(type="integer") @Id */
    public $id;

    /** @Column(type="string") */
    public $name;

    /** @Column(type="string") */
    public $slug;

    /** @Column(type="datetime") */
    public $date;

    /** @Column(type="text") */
    public $description = '';

    /** @Column(type="smallint") */
    public $status;

    /** @Column(type="datetime") */
    public $modified;

    /** @Column(type="integer") */
    public $parent_id = 0;

    /** @Column(type="integer") */
    public $priority = 0;

    /**
     *
     */



         /** @var array */
         protected static $properties = [
          //   'author' => 'getAuthor',
             'published' => 'isPublished',
          //   'accessible' => 'isAccessible'
         ];


    public static function getCategories()
    {
        $st = self::STATUS_PUBLISHED;
        $td = new \DateTime;

        $categories = Category::query()->where('status= ?',[$st])->where('date <= ?',[$td])->get();

        return $categories;
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_PUBLISHED => __('Published'),
            self::STATUS_UNPUBLISHED => __('Unpublished'),
            self::STATUS_DRAFT => __('Draft'),
            self::STATUS_PENDING_REVIEW => __('Pending Review')
        ];
    }

    public function getStatusText()
    {
        $statuses = self::getStatuses();

        return isset($statuses[$this->status]) ? $statuses[$this->status] : __('Unknown');
    }


    public function isPublished()
    {
        return $this->status === self::STATUS_PUBLISHED && $this->date <= new \DateTime;
    }


    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        $data = [
            'url' => App::url('@category/id', ['id' => $this->id ?: 0], 'base')
        ];

        return $this->toArray($data);
    }
}

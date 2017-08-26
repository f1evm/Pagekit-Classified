<?php

namespace fevm\Classified;

use Pagekit\Application as App;
use fevm\Classified\Model\Ad;
use Pagekit\Routing\ParamsResolverInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class UrlResolver implements ParamsResolverInterface
{
    const CACHE_KEY = 'classified.routing';

    /**
     * @var bool
     */
    protected $cacheDirty = false;

    /**
     * @var array
     */
    protected $cacheEntries;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->cacheEntries = App::cache()->fetch(self::CACHE_KEY) ?: [];
    }

    /**
     * {@inheritdoc}
     */
    public function match(array $parameters = [])
    {
        if (isset($parameters['id'])) {
            return $parameters;
        }

        if (!isset($parameters['slug'])) {
            App::abort(404, 'Ad not found.');
        }

        $slug = $parameters['slug'];

        $id = false;
        foreach ($this->cacheEntries as $entry) {
            if ($entry['slug'] === $slug) {
                $id = $entry['id'];
            }
        }

        if (!$id) {

            if (!$ad = Ad::where(compact('slug'))->first()) {
                App::abort(404, 'Ad not found.');
            }

            $this->addCache($ad);
            $id = $ad->id;
        }

        $parameters['id'] = $id;
        return $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(array $parameters = [])
    {
        $id = $parameters['id'];

        if (!isset($this->cacheEntries[$id])) {

            if (!$ad = Ad::where(compact('id'))->first()) {
                throw new RouteNotFoundException('Ad not found!');
            }

            $this->addCache($ad);
        }

        $meta = $this->cacheEntries[$id];

        preg_match_all('#{([a-z]+)}#i', self::getPermalink(), $matches);

        if ($matches) {
            foreach($matches[1] as $attribute) {
                if (isset($meta[$attribute])) {
                    $parameters[$attribute] = $meta[$attribute];
                }
            }
        }

        unset($parameters['id']);
        return $parameters;
    }

    public function __destruct()
    {
        if ($this->cacheDirty) {
            App::cache()->save(self::CACHE_KEY, $this->cacheEntries);
        }
    }

    /**
     * Gets the classified's permalink setting.
     *
     * @return string
     */
    public static function getPermalink()
    {
        static $permalink;

        if (null === $permalink) {

            $classified = App::module('classified');
            $permalink = $classified->config('permalink.type');

            if ($permalink == 'custom') {
                $permalink = $classified->config('permalink.custom');
            }

        }

        return $permalink;
    }

    protected function addCache($ad)
    {
        $this->cacheEntries[$ad->id] = [
            'id'     => $ad->id,
            'slug'   => $ad->slug,
            'year'   => $ad->date->format('Y'),
            'month'  => $ad->date->format('m'),
            'day'    => $ad->date->format('d'),
            'hour'   => $ad->date->format('H'),
            'minute' => $ad->date->format('i'),
            'second' => $ad->date->format('s'),
        ];

        $this->cacheDirty = true;
    }
}

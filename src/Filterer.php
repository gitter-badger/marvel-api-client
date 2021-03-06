<?php

namespace Chadicus\Marvel\Api;

use DominionEnterprises\Filterer as BaseFilterer;

/**
 * Custom filterer for sanitizing API result data.
 */
abstract class Filterer
{
    /**
     * @see \DominionEntepries\Filterer::filter().
     *
     * @param array $spec    The filter specification.
     * @param array $input   The data to be filtered.
     * @param array $options Array of filterer options.
     *
     * @return array
     */
    final public static function filter(array $spec, array $input, array $options = [])
    {
        BaseFilterer::setFilterAliases(self::getFilterAliases() + BaseFilterer::getFilterAliases());
        return BaseFilterer::filter($spec, $input, $options);
    }

    /**
     * Return the filter aliases.
     *
     * @return array array where keys are aliases and values pass is_callable().
     */
    final public static function getFilterAliases()
    {
        return [
            '_date' => '\Chadicus\Marvel\Api\Entities\Date::fromArray',
            '_dates' => '\Chadicus\Marvel\Api\Entities\Date::fromArrays',
            'image' => '\Chadicus\Marvel\Api\Entities\Image::fromArray',
            'images' => '\Chadicus\Marvel\Api\Entities\Image::fromArrays',
            'price' => '\Chadicus\Marvel\Api\Entities\Price::fromArray',
            'prices' => '\Chadicus\Marvel\Api\Entities\Price::fromArrays',
            'resource-list' => '\Chadicus\Marvel\Api\Entities\ResourceList::fromArray',
            'summary' => '\Chadicus\Marvel\Api\Entities\Summary::fromArray',
            'summaries' => '\Chadicus\Marvel\Api\Entities\Summary::fromArrays',
            'text-object' => '\Chadicus\Marvel\Api\Entities\TextObject::fromArray',
            'text-objects' => '\Chadicus\Marvel\Api\Entities\TextObject::fromArrays',
            '_url' => '\Chadicus\Marvel\Api\Entities\Url::fromArray',
            '_urls' => '\Chadicus\Marvel\Api\Entities\Url::fromArrays',
            'character' => '\Chadicus\Marvel\Api\Entities\Character::fromArray',
            'characters' => '\Chadicus\Marvel\Api\Entities\Character::fromArrays',
            'comic' => '\Chadicus\Marvel\Api\Entities\Comic::fromArray',
            'comics' => '\Chadicus\Marvel\Api\Entities\Comic::fromArrays',
            'series' => '\Chadicus\Marvel\Api\Entities\Series::fromArrays',
            'events' => '\Chadicus\Marvel\Api\Entities\Event::fromArrays',
            'stories' => '\Chadicus\Marvel\Api\Entities\Story::fromArrays',
            'creators' => '\Chadicus\Marvel\Api\Entities\Creator::fromArrays',
            'boolToString' => function ($bool) {
                return $bool ? 'true' : 'false';
            },
            'formatDate' => function ($date, $format = 'c') {
                return $date instanceof \DateTime ? $date->format($format) : null;
            },
        ];
    }
}

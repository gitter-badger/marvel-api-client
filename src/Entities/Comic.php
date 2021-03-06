<?php

namespace Chadicus\Marvel\Api\Entities;

use Chadicus\Marvel\Api;
use DominionEnterprises\Util;

/**
 * Represents a Marvel API Comic Entity
 *
 * @property-read integer $id The unique ID of the comic resource.
 * @property-read integer $digitalId The ID of the digital comic representation of this comic. Will be if the comic is
 *                                   not available digitally.
 * @property-read string $title The canonical title of the comic.
 * @property-read string $issueNumber The number of the issue in the series (will generally be for collection formats).
 * @property-read string $variantDescription If the issue is a variant (e.g. an alternate cover, second printing, or
 *                                           director's cut), a text description of the variant.
 * @property-read string $description The preferred description of the comic.
 * @property-read DateTime $modified The date the resource was most recently modified.
 * @property-read string $isbn The ISBN for the comic (generally only populated for collection formats).
 * @property-read string $upc The UPC barcode number for the comic (generally only populated for periodical formats).
 * @property-read string $diamondCode The Diamond code for the comic.
 * @property-read string $ean The EAN barcode for the comic.
 * @property-read string $issn The ISSN barcode for the comic.
 * @property-read string $format The publication format of the comic e.g. comic, hardcover, trade paperback.
 * @property-read integer $pageCount The number of story pages in the comic.
 * @property-read TextObject[] $textObjects A set of descriptive text blurbs for the comic.
 * @property-read string $resourceURI The canonical URL identifier for this resource.
 * @property-read Url[] $urls A set of public web site URLs for the resource.
 * @property-read Summary $series A summary representation of the series to which this comic belongs.
 * @property-read Summary[] $variants A list of variant issues for this comic (includes the "original" issue if the
 *                                    current issue is a variant).
 * @property-read Summary[] $collections A list of collections which include this comic (will generally be empty if the
 *                                       comic's format is a collection).
 * @property-read Summary[] $collectedIssues A list of issues collected in this comic (will generally be empty for
 *                                           periodical formats such as "comic" or "magazine").
 * @property-read Date[] $dates A list of key dates for this comic.
 * @property-read Price[] $prices A list of prices for this comic.
 * @property-read Image $thumbnail The representative image for this comic.
 * @property-read Image[] $images A list of promotional images associated with this comic.
 * @property-read ResourceList $creators A resource list containing the creators associated with this comic.
 * @property-read ResourceList $characters A resource list containing the characters which appear in this comic.
 * @property-read ResourceList $stories A resource list containing the stories which appear in this comic.
 * @property-read ResourceList $events A resource list containing the events in which this comic appears.
 */
class Comic extends AbstractEntity
{
    /**
     * The name of the comic api resource
     *
     * @const string
     */
    const API_RESOURCE = 'comics';

    /**
     * @see AbstractEntity::getFilters()
     *
     * @return array
     */
    final protected function getFilters()
    {
        return [
            'id' => [['int', true]],
            'digitalId' => [['int', true]],
            'title' => [['string', true, 0]],
            'issueNumber' => [['strval'], ['string', true, 0]],
            'variantDescription' => [['string', true, 0]],
            'description' => [['string', true, 0]],
            'modified' => [['date', true]],
            'isbn' => [['string', true, 0]],
            'upc' => [['string', true, 0]],
            'diamondCode' => [['string', true, 0]],
            'ean' => [['string', true, 0]],
            'issn' => [['string', true, 0]],
            'format' => [['string', true, 0]],
            'pageCount' => [['int', true]],
            'textObjects' => ['default' => [], ['text-objects']],
            'resourceURI' => [['string', true, 0]],
            'urls' => ['default' => [], ['_urls']],
            'series' => ['default' => new Summary(), ['summary']],
            'variants' => ['default' => [], ['summaries']],
            'collections' => ['default' => [], ['summaries']],
            'collectedIssues' => ['default' => [], ['summaries']],
            'dates' => ['default' => [], ['_dates']],
            'prices' => ['default' => [], ['prices']],
            'thumbnail' => ['default' => new Image(), ['image']],
            'images' => ['default' => [], ['images']],
            'creators' => ['default' => new ResourceList(), ['resource-list']],
            'characters' => ['default' => new ResourceList(), ['resource-list']],
            'stories' => ['default' => new ResourceList(), ['resource-list']],
            'events' => ['default' => new ResourceList(), ['resource-list']],
        ];
    }

    /**
     * Returns a collection containing all Comics which match the given criteria.
     *
     * @param Api\Client $client   The API Client.
     * @param array      $criteria The criteria for searching.
     *
     * @return Api\Collection
     */
    final public static function findAll(Api\Client $client, array $criteria = [])
    {
        $filters = [
            'format' => [
                [
                    'in',
                    [
                        'comic',
                        'hardcover',
                        'trade paperback',
                        'magazine',
                        'digest',
                        'graphic novel',
                        'digital comic',
                        'infinite comic',
                    ]
                ],
            ],
            'formatType' => [['in', ['comic', 'collection']]],
            'noVariants' => [['bool'], ['boolToString']],
            'dateDescriptor' => [['in', ['lastWeek', 'thisWeek', 'nextWeek', 'thisMonth']]],
            'fromDate' => [['date', true]],
            'toDate' => [['date', true]],
            'hasDigitalIssue' => [['bool'], ['boolToString']],
            'modifiedSince' => [['date', true], ['formatDate']],
            'creators' => [['ofScalars', [['uint']]], ['implode', ',']],
            'characters' => [['ofScalars', [['uint']]], ['implode', ',']],
            'series' => [['ofScalars', [['uint']]], ['implode', ',']],
            'events' => [['ofScalars', [['uint']]], ['implode', ',']],
            'stories' => [['ofScalars', [['uint']]], ['implode', ',']],
            'sharedAppearances' => [['ofScalars', [['uint']]], ['implode', ',']],
            'collaborators' => [['ofScalars', [['uint']]], ['implode', ',']],
            'orderBy' => [
                [
                    'in',
                    [
                        'focDate',
                        'onsaleDate',
                        'title',
                        'issueNumber',
                        'modified',
                        '-focDate',
                        '-onsaleDate',
                        '-title',
                        '-issueNumber',
                        '-modified',
                    ],
                ]
            ],

        ];

        list($success, $filteredCriteria, $error) = Api\Filterer::filter($filters, $criteria);
        Util::ensure(true, $success, $error);

        $toDate = Util\Arrays::get($filteredCriteria, 'toDate');
        $fromDate = Util\Arrays::get($filteredCriteria, 'fromDate');
        if ($toDate !== null && $fromDate !== null) {
            unset($filteredCriteria['toDate'], $filteredCriteria['fromDate']);
            $filteredCriteria['dateRange'] = "{$fromDate->format('c')},{$toDate->format('c')}";
        }

        return new Api\Collection($client, self::API_RESOURCE, $filteredCriteria);
    }
}

<?php

namespace Spiral\Pages\Services;

use Spiral\Core\FactoryInterface;
use Spiral\Core\Service;
use Spiral\Core\Traits\SaturateTrait;
use Spiral\Listing\Filters\ComplexFilter;
use Spiral\Listing\Filters\SearchFilter;
use Spiral\Listing\Filters\StaticFilter;
use Spiral\Listing\Filters\ValueFilter;
use Spiral\Listing\Listing;
use Spiral\Listing\SorterInterface;
use Spiral\Listing\Sorters\BinarySorter;
use Spiral\Listing\StaticState;
use Spiral\ORM\Entities\RecordSelector;

class ListingService extends Service
{
    use SaturateTrait;

    /** @var FactoryInterface */
    protected $factory;

    /**
     * ListingService constructor.
     *
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param RecordSelector $selector
     * @return Listing
     */
    public function pagesListing(RecordSelector $selector): Listing
    {
        /** @var Listing $listing */
        $listing = $this->factory->make(Listing::class, [
            'selector' => $selector->distinct(),
        ]);

        $listing->addSorter('id', new BinarySorter('id'));
        $listing->addSorter('title', new BinarySorter('title'));
        $listing->addSorter('slug', new BinarySorter('slug'));
        $listing->addSorter('revisions_count', new BinarySorter('revisions_count'));
        $listing->addSorter('time_created', new BinarySorter('time_created'));
        $listing->addSorter('time_updated', new BinarySorter('time_updated'));

        $listing->addFilter('revisions', new ComplexFilter([
            'has_revisions'    => new StaticFilter(['revisions_count' => ['>' => 0]]),
            'has_no_revisions' => new StaticFilter(['revisions_count' => 0])
        ]));

        $listing->addFilter(
            'status',
            new ValueFilter('status')
        );

        $listing->addFilter(
            'search',
            new SearchFilter([
                'title'       => SearchFilter::LIKE_STRING,
                'slug'        => SearchFilter::LIKE_STRING,
                'description' => SearchFilter::LIKE_STRING,
                'keywords'    => SearchFilter::LIKE_STRING,
                'source'      => SearchFilter::LIKE_STRING,
            ])
        );

        $defaultState = new StaticState('title', ['status' => 'active'], SorterInterface::ASC);
        $listing = $listing->setDefaultState($defaultState)->setNamespace('page');

        return $listing;
    }

    /**
     * @param RecordSelector $selector
     * @return Listing
     */
    public function revisionsListing(RecordSelector $selector): Listing
    {
        /** @var Listing $listing */
        $listing = $this->factory->make(Listing::class, [
            'selector' => $selector->distinct(),
        ]);

        $listing->addSorter('time_started', new BinarySorter('time_started'));
        $listing->addSorter('time_ended', new BinarySorter('time_ended'));

        $listing->addFilter(
            'search',
            new SearchFilter([
                'title'       => SearchFilter::LIKE_STRING,
                'slug'        => SearchFilter::LIKE_STRING,
                'description' => SearchFilter::LIKE_STRING,
                'keywords'    => SearchFilter::LIKE_STRING,
                'source'      => SearchFilter::LIKE_STRING,
            ])
        );

        $defaultState = new StaticState('time_ended', [], SorterInterface::DESC);
        $listing->setDefaultState($defaultState)->setNamespace('revision');

        return $listing;
    }
}
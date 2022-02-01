<?php

namespace Test\Joblistings\Entity;

use Brain\Monkey\Functions;
use Mockery;
use JobListings\Entity\Taxonomy;

class TaxonomyTest extends \PluginTestCase\PluginTestCase
{
    public function testAddInitHook()
    {
        new Taxonomy('namePlural', 'nameSingular', 'slug', ['postType'], []);

        self::assertNotFalse(has_action('init', 'JobListings\Entity\Taxonomy->registerTaxonomy()'));
    }

    public function testRegisterTaxonomy()
    {
        $testslug = 'slug';

        Functions\expect('register_taxonomy')->once()->with(
            $testslug,
            Mockery::type('array'),
            Mockery::type('array')
        );

        $taxonomy = new Taxonomy('namePlural', 'nameSingular', $testslug, ['postType'], []);
        $slug = $taxonomy->registerTaxonomy();

        $this->assertSame($testslug, $slug);
    }
}

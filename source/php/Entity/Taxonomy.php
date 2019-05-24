<?php

namespace JobListings\Entity;

/**
 * Class Taxonomy
 * @package JobListings\Entity
 */
class Taxonomy
{
    public $namePlural;
    public $nameSingular;
    public $slug;
    public $args;
    public $postTypes;

    /**
     * Taxonomy constructor.
     * @param $namePlural
     * @param $nameSingular
     * @param $slug
     * @param $postTypes
     * @param $args
     */
    public function __construct($namePlural, $nameSingular, $slug, $postTypes, $args)
    {
        $this->namePlural = $namePlural;
        $this->nameSingular = $nameSingular;
        $this->slug = $slug;
        $this->args = $args;
        $this->postTypes = $postTypes;

        $this->registerTaxonomy();
    }

    /**
     * @return string
     */
    public function registerTaxonomy()
    {
        $labels = array(
            'name'              => $this->namePlural,
            'singular_name'     => $this->nameSingular,
            'search_items'      => sprintf(__('Search %s', 'todo'), $this->namePlural),
            'all_items'         => sprintf(__('All %s', 'todo'), $this->namePlural),
            'parent_item'       => sprintf(__('Parent %s:', 'todo'), $this->nameSingular),
            'parent_item_colon' => sprintf(__('Parent %s:', 'todo'), $this->nameSingular) . ':',
            'edit_item'         => sprintf(__('Edit %s', 'todo'), $this->nameSingular),
            'update_item'       => sprintf(__('Update %s', 'todo'), $this->nameSingular),
            'add_new_item'      => sprintf(__('Add New %s', 'todo'), $this->nameSingular),
            'new_item_name'     => sprintf(__('New %s Name', 'todo'), $this->nameSingular),
            'menu_name'         => $this->nameSingular,
        );

        $this->args['labels'] = $labels;

        register_taxonomy($this->slug, $this->postTypes, $this->args);
        return $this->slug;
    }
}
<?php

namespace Test\Joblistings\Entity;

use Brain\Monkey\Functions;
use Mockery;
use JobListings\Entity\PostType;

class PostTypeTest extends \PluginTestCase\PluginTestCase
{
    public function testAddHooks()
    {
        $testslug = 'slug';

        new PostType('namePlural', 'nameSingular', $testslug, ['postType'], []);

        self::assertNotFalse(has_action('init', 'JobListings\Entity\PostType->registerPostType()'));
        self::assertNotFalse(has_filter('manage_edit-' . $testslug . '_columns', 'JobListings\Entity\PostType->tableColumns()'));
        self::assertNotFalse(has_filter('manage_edit-' . $testslug . '_sortable_columns', 'JobListings\Entity\PostType->tableSortableColumns()'));
        self::assertNotFalse(has_action('manage_' . $testslug . '_posts_custom_column', 'JobListings\Entity\PostType->tableColumnsContent()'));

    }

    public function testRegisterPostType()
    {
        $testslug = 'slug';

        Functions\expect('register_post_type')->once()->with(
            $testslug,
            Mockery::type('array')
        );

        $postType = new PostType('namePlural', 'nameSingular', $testslug, ['postType'], []);
        $slug = $postType->registerPostType();

        $this->assertSame($testslug, $slug);
    }

    public function testAddTableColumn()
    {
        $postType = new PostType('namePlural', 'nameSingular', 'slug', ['postType'], []);

        $key = 'key';
        $title = 'title';
        $postType->addTableColumn($key, $title, false, false);

        $this->assertSame($postType->tableColumns, [$key => $title]);
        $this->assertSame($postType->tableSortableColumns, []);
        $this->assertSame($postType->tableColumnsContentCallback, []);
    }

    public function testAddTableColumnSortable()
    {
        $postType = new PostType('namePlural', 'nameSingular', 'slug', ['postType'], []);

        $key = 'key';
        $title = 'title';
        $postType->addTableColumn($key, $title, true, false);

        $this->assertSame($postType->tableColumns, [$key => $title]);
        $this->assertSame($postType->tableSortableColumns, [$key => $key]);
        $this->assertSame($postType->tableColumnsContentCallback, []);
    }

    public function testAddTableColumnSortableWithCallback()
    {
        $postType = new PostType('namePlural', 'nameSingular', 'slug', ['postType'], []);

        $key = 'key';
        $title = 'title';
        $callback = 'callback';
        $postType->addTableColumn($key, $title, true, $callback);

        $this->assertSame($postType->tableColumns, [$key => $title]);
        $this->assertSame($postType->tableSortableColumns, [$key => $key]);
        $this->assertSame($postType->tableColumnsContentCallback, [$key => $callback]);
    }

    public function testAddTableColumnWithCallback()
    {
        $postType = new PostType('namePlural', 'nameSingular', 'slug', ['postType'], []);

        $key = 'key';
        $title = 'title';
        $callback = 'callback';
        $postType->addTableColumn($key, $title, false, $callback);

        $this->assertSame($postType->tableColumns, [$key => $title]);
        $this->assertSame($postType->tableSortableColumns, []);
        $this->assertSame($postType->tableColumnsContentCallback, [$key => $callback]);
    }


    public function testAddTableColumnAll()
    {
        $key = 'key';
        $title = 'title';

        $callback = 'callback';

        $postType = new PostType('namePlural', 'nameSingular', 'slug', ['postType'], []);
        
        $postType->addTableColumn($key, $title);

        $this->assertSame($postType->tableColumns, [$key => $title]);
        $this->assertSame($postType->tableSortableColumns, []);
        $this->assertSame($postType->tableColumnsContentCallback, []);


        $postType = new PostType('namePlural', 'nameSingular', 'slug', ['postType'], []);

        $postType->addTableColumn($key, $title, true);

        $this->assertSame($postType->tableColumns, [$key => $title]);
        $this->assertSame($postType->tableSortableColumns, [$key => $key]);
        $this->assertSame($postType->tableColumnsContentCallback, []);


        $postType = new PostType('namePlural', 'nameSingular', 'slug', ['postType'], []);

        $postType->addTableColumn($key, $title, true, $callback);

        $this->assertSame($postType->tableColumns, [$key => $title]);
        $this->assertSame($postType->tableSortableColumns, [$key => $key]);
        $this->assertSame($postType->tableColumnsContentCallback, [$key => $callback]);

        $postType = new PostType('namePlural', 'nameSingular', 'slug', ['postType'], []);

        $postType->addTableColumn($key, $title, false, $callback);

        $this->assertSame($postType->tableColumns, [$key => $title]);
        $this->assertSame($postType->tableSortableColumns, []);
        $this->assertSame($postType->tableColumnsContentCallback, [$key => $callback]);
    }

    public function testTableColumns()
    {
        $postType = new PostType('namePlural', 'nameSingular', 'slug', ['postType'], []);
        $key = 'key';
        $title = 'title';
        $postType->addTableColumn($key, $title);
        $columns = $postType->tableColumns([1, 2, 3]);
        $this->assertSame($columns, [1, 2, $key => $title, 3]);
    }

    public function testTableColumnsWithEmptyArray()
    {
        $postType = new PostType('namePlural', 'nameSingular', 'slug', ['postType'], []);
        $columns = $postType->tableColumns([]);
        $this->assertSame($columns, []);
    }

    public function testTableSortableColumns()
    {
        $postType = new PostType('namePlural', 'nameSingular', 'slug', ['postType'], []);
        $testColumns = [
            'A' => 'D',
            'B' => 'E',
            'C' => 'F'
        ];

        $expectColumns = [
            'a' => 'd',
            'b' => 'e',
            'c' => 'f'
        ];

        foreach($testColumns as $key => $title) {
            $postType->addTableColumn($key, $title, true);
        }

        
        $columns = $postType->tableSortableColumns([]);

        $this->assertSame($columns, $expectColumns);
    }

    public function testTableColumnsContent()
    {
        $postType = new PostType('namePlural', 'nameSingular', 'slug', ['postType'], []);
        $key = 'key';
        $title = 'title';
        $callback = 'callback';


        Functions\expect('call_user_func_array')->once()->with(
            [$key => $callback], [$key, 1]
        );

        $postType->addTableColumn($key, $title, true, $callback);

        $postType->tableColumnsContent($key, 1);
    }

    public function testTableColumnsContentWithNoCallback()
    {
        $postType = new PostType('namePlural', 'nameSingular', 'slug', ['postType'], []);

        $this->assertSame($postType->tableColumnsContent('key', 1), null);
    }
}

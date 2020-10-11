<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testList()
    {
        factory(Category::class, 1)->create();
        $categories = Category::all();
        $this->assertCount(1, $categories);
        $categoryKeys = array_keys($categories->first()->getAttributes());
        $this->assertEqualsCanonicalizing(
            [
                'id',
                'name',
                'description',
                'is_active',
                'created_at',
                'updated_at',
                'deleted_at'
            ],
            $categoryKeys
        );
    }

    public function testUuid()
    {
        factory(Category::class, 1)->create();

        $category = Category::first();

        /**
         * usando o regex para verificar se o padrão bate
         * note que a versão do uuid importa, que no nosso 4 é a v4
         * então, no terceiro grupo, o primeiro número tem que ser 4
         * [4]
         */
        $this->assertRegExp(
            '/^[0-9A-F]{8}-[0-9A-F]{4}-[4][0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i',
            $category->id
        );
    }

    public function testCreate()
    {
        $category = Category::create([
            'name' => 'test2'
        ]);
        $category->refresh();

        $this->assertEquals('test2', $category->name);
        $this->assertNull($category->description);
        $this->assertTrue($category->is_active);

        $category = Category::create([
            'name' => 'test2',
            'description' => null
        ]);
        $this->assertNull($category->description);

        $category = Category::create([
            'name' => 'test2',
            'description' => 'description_test'
        ]);
        $this->assertEquals('description_test', $category->description);

        $category = Category::create([
            'name' => 'test2',
            'is_active' => false
        ]);
        $this->assertFalse($category->is_active);

        $category = Category::create([
            'name' => 'test2',
            'is_active' => true
        ]);
        $this->assertTrue($category->is_active);
    }

    public function testUpdate()
    {
        /** @var Category $category*/
        $category = factory(Category::class)->create([
            'description' => 'description_test',
            'is_active' => false
        ]);

        $data = [
            'name' => 'name_test',
            'description' => 'description_test_update',
            'is_active' => true
        ];

        $category->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $category->{$key});
        }
    }

    public function testDelete()
    {
        /** @var Category $category*/
        $category = factory(Category::class)->create([
            'name' => 'test_delete',
            'description' => 'description_test',
        ]);

        $category->delete();

        $categories = Category::all();

        $this->assertCount(0, $categories);
    }
}

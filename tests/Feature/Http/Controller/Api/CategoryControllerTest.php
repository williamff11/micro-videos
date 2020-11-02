<?php

namespace Tests\Feature\Http\Controller\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

    private $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = factory(Category::class)->create();
    }

    public function testIndex()
    {
        $response = $this->get(route('categories.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$this->category->toArray()]);
    }

    public function testShow()
    {
        $response = $this->get(route('categories.show', ['category' => $this->category->id]));

        $response
            ->assertStatus(200)
            ->assertJson($this->category->toArray());
    }


    public function testDelete()
    {
        $response = $this->delete(route('categories.destroy', ['category' => $this->category->id]));

        $response
            ->assertStatus(204)
            ->assertNoContent();

        $this->assertNull(Category::find($this->category->id));
        $this->assertNotNull(Category::withTrashed()->find($this->category->id));
    }

    public function test422WhenSendWithoutParams()
    {
        $dataCreate = [
            'name' => ''
        ];
        $this->assertInvalidationInStoreAction($dataCreate, 'required');
        $this->assertInvalidationInUpdateAction($dataCreate, 'required');
    }

    public function test422WhenSendNameBiggerThan255Chars()
    {
        $dataCreate = [
            'name' => str_repeat('a', 256),
        ];
        $this->assertInvalidationInStoreAction($dataCreate, 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateAction($dataCreate, 'max.string', ['max' => 255]);
    }

    public function test422PassingStringForBoolean()
    {
        $dataCreate = [
            'is_active' => 'a'
        ];
        $this->assertInvalidationInStoreAction($dataCreate, 'boolean');
        $this->assertInvalidationInUpdateAction($dataCreate, 'boolean');
    }

    public function testStore()
    {
        $data = [
            'name' => 'test'
        ];
        $response = $this->assertStore($data, $data + ['description' => null, 'is_active' => true, 'deleted_at' => null]);
        $response->assertJsonStructure([
            'created_at', 'updated_at'
        ]);
    }

    public function testStoreWithDescription()
    {
        $data = [
            'name' => 'test',
            'description' => 'description_test'
        ];
        $this->assertStore($data, $data + ['description' => 'description_test', 'is_active' => true, 'deleted_at' => null]);
    }

    public function testStoreWithIsActiveFalse()
    {
        $data = [
            'name' => 'test',
            'is_active' => false,
        ];
        $this->assertStore($data, $data + ['description' => null, 'is_active' => false, 'deleted_at' => null]);
    }

    public function testUpdate()
    {
        $this->category = factory(Category::class)->create([
            'description' => 'description',
            'is_active' => false,
        ]);

        $data = [
            'name' => 'test',
            'is_active' => true,
            'description' => 'descriptionTest',
        ];
        $response = $this->assertUpdate($data, $data + ['deleted_at' => null]);
        $response->assertJsonStructure([
            'created_at', 'updated_at'
        ]);
    }

    public function testUpdateWhenDescriptionEmptyEqualsNull()
    {
        $data = [
            'name' => 'test',
            'is_active' => true,
            'description' => '',
        ];
        $this->assertUpdate($data, array_merge($data, ['description' => null]));
    }

    public function testUpdateWhenDescriptionNull()
    {
        $data = [
            'name' => 'test',
            'is_active' => true,
            'description' => null,
        ];
        $this->assertUpdate($data, array_merge($data, ['description' => null]));
    }

    protected function routeStore()
    {
        return route('categories.store');
    }

    protected function routeUpdate()
    {
        return route('categories.update', ['category' => $this->category->id]);
    }

    protected function model()
    {
        return Category::class;
    }
}

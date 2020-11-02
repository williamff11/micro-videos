<?php

namespace Tests\Feature\Http\Controller\Api;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidation;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidation, TestSaves;

    private $genre;

    protected function setUp(): void
    {
        parent::setUp();
        $this->genre = factory(Genre::class)->create();
    }

    public function testIndex()
    {
        $reponse = $this->get(route('genres.index'));

        $reponse
            ->assertStatus(200)
            ->assertJson([$this->genre->toArray()]);
    }

    public function testShow()
    {
        $response = $this->get(route('genres.show', ['genre' => $this->genre->id]));

        $response
            ->assertStatus(200)
            ->assertJson($this->genre->toArray());
    }

    public function testDelete()
    {
        $response = $this->delete(route('genres.destroy', ['genre' => $this->genre->id]));

        $response
            ->assertStatus(204)
            ->assertNoContent();

        $this->assertNull(Genre::find($this->genre->id));
        $this->assertNotNull(Genre::withTrashed()->find($this->genre->id));
    }

    public function test422WhenSendWithoutParams()
    {
        $dataCreate = [
            'name' => null
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
        $response = $this->assertStore($data, $data + ['is_active' => true, 'deleted_at' => null]);
        $response->assertJsonStructure([
            'created_at', 'updated_at'
        ]);
    }

    public function testStoreWithIsActiveFalse()
    {
        $data = [
            'name' => 'test',
            'is_active' => false,
        ];
        $this->assertStore($data, $data + ['is_active' => false, 'deleted_at' => null]);
    }

    public function testUpdate()
    {
        $this->genre = factory(Genre::class)->create([
            'is_active' => false,
        ]);

        $data = [
            'name' => 'test',
            'is_active' => true,
        ];
        $response = $this->assertUpdate($data, $data + ['deleted_at' => null]);
        $response->assertJsonStructure([
            'created_at', 'updated_at'
        ]);
    }

    protected function routeStore()
    {
        return route('genres.store');
    }

    protected function routeUpdate()
    {
        return route('genres.update', ['genre' => $this->genre->id]);
    }

    protected function model()
    {
        return Genre::class;
    }
}

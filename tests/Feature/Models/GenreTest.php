<?php

namespace Tests\Feature\Models;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class GenreTest extends TestCase
{
    use DatabaseMigrations;

    public function testList()
    {
        factory(Genre::class, 5)->create();

        $genres = Genre::all();
        $this->assertCount(5, $genres);

        $genresKeys = array_keys($genres->first()->getAttributes());
        $this->assertEqualsCanonicalizing(
            [
                'id',
                'name',
                'is_active',
                'created_at',
                'updated_at',
                'deleted_at'
            ],
            $genresKeys
        );
    }

    public function testUuid()
    {
        factory(Genre::class, 1)->create();

        $genre = Genre::first();

        /**
         * usando o regex para verificar se o padrão bate
         * note que a versão do uuid importa, que no nosso 4 é a v4
         * então, no terceiro grupo, o primeiro número tem que ser 4
         * [4]
         */
        $this->assertRegExp(
            '/^[0-9A-F]{8}-[0-9A-F]{4}-[4][0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i',
            $genre->id
        );
    }

    public function testCreateName()
    {
        $genre = Genre::create([
            'name' => 'test'
        ]);
        $genre->refresh();

        $this->assertEquals('test', $genre->name);
        $this->assertTrue($genre->is_active);
    }

    public function testCreateIsActive()
    {
        $genre = Genre::create([
            'name' => 'test',
            'is_active' => false
        ]);

        $this->assertFalse($genre->is_active);
    }

    public function testUpdate()
    {
        /** @var Genre $genre*/
        $genre = factory(Genre::class)->create([
            'is_active' => false
        ]);

        $data = [
            'name' => 'testUpdate',
            'is_active' => true
        ];

        $genre->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $genre->{$key});
        }
    }

    public function testDelete()
    {
        /** @var Genre $genre*/
        $genre = factory(Genre::class)->create([
            'name' => 'test_delete',
        ]);

        $genre->delete();

        $genres = Genre::all();

        $this->assertCount(0, $genres);
    }

    public function testRestore()
    {
        $genre = factory(Genre::class)->create();
        $genre->delete();
        $this->assertNull(Genre::find($genre->id));

        $genre->restore();
        $this->assertNotNull(Genre::find($genre->id));
    }
}

<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenreRequest;
use Illuminate\Http\Request;
use App\Models\Genre;

class GenreController extends Controller
{
    public function index()
    {
        return Genre::all();
    }

    public function store(GenreRequest $request)
    {
        return Genre::create($request->all());
    }

    public function show(Genre $genre)
    {
        return Genre::findOrFail($genre);
    }

    public function update(GenreRequest $request, Genre $genre)
    {
        $genre->update($request->all());
        return $genre;
    }

    public function destroy(Genre $genre)
    {
        $genre->delete();
        return response()->noContent();
    }
}

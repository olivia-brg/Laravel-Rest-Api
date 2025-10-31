<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAuthorRequest;
use App\Http\Resources\AuthorResource;
use App\Models\Author;
use http\Message;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthorController extends Controller
{
    public function index()
    {
        $authors = Author::with('books')->paginate(10);

        return AuthorResource::collection($authors);

        // Method basique
//        return response()->json([
//            'authors' => $authors,
//            'message' => 'Authors retrieved successfully',
//        ], 200);
    }


    public function store(StoreAuthorRequest $request)
    {
        $author = Author::create($request->validated());
        return new AuthorResource($author);
    }


    public function show(Author $author)
    {
        return new AuthorResource($author);
    }


    public function update(StoreAuthorRequest $request, Author $author)
    {
        $author->update($request->validated());
        return new AuthorResource($author);
    }


    public function destroy(Author $author)
    {
        $author->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Author deleted successfully'
        ], 200);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;


class BookController extends Controller
{
    public function index(Request $request)
    {
        $books = Book::with('author')
            ->when($request->input('search'), function ($q, $search) {

                $q->where(function ($sub) use ($search) {

                    $sub->where('title', 'ilike', "%{$search}%")
                        ->orWhere('isbn', 'ilike', "%{$search}%")
                        ->orWhereHas('author', fn($a) =>
                        $a->where('name', 'ilike', "%{$search}%")
                        );
                });
            })
            ->when($request->input('genre'), fn($q, $genre) => $q->where('genre', $genre))
            ->paginate(10);

        return BookResource::collection($books);
    }


    public function store(StoreBookRequest $request)
    {
        $book = Book::create($request->validated());
        $book->load('author');
        return new BookResource($book);
    }


    public function show(Book $book)
    {

        $book->load('author');
        return new BookResource($book);

    }


    public function update(StoreBookRequest $request, Book $book)
    {
        $book->update($request->validated());
        $book->load('author');
        return new BookResource($book);
    }


    public function destroy(Book $book)
    {
        $book->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Book deleted successfully'
        ], 200);
    }
}

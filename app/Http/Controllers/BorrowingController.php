<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBorrowingRequest;
use App\Http\Resources\BorrowingResource;
use App\Models\Book;
use App\Models\Borrowing;
use Illuminate\Http\Request;

class BorrowingController extends Controller
{
    public function index(Request $request)
    {
        $query = Borrowing::with('book', 'member');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('member_id')) {
            $query->where('member_id', $request->member_id);
        }

        $borrowings = $query->latest()->paginate(15);

        return BorrowingResource::collection($borrowings);
    }


    public function store(StoreBorrowingRequest $request)
    {
        $book = Book::findOrFail($request->book_id);

        if (!$book->isAvailable()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Book is not available'
            ], 422);
        }

        $borrowing = Borrowing::create($request->validated());

        $book->borrow();

        $borrowing->load('book', 'member');

        return new BorrowingResource($borrowing);
    }


    public function show(Borrowing $borrowing)
    {
        $borrowing->load('book', 'member');

        return new BorrowingResource($borrowing);
    }

    public function returnBook(Borrowing $borrowing)
    {
        if ($borrowing->status !== 'borrowed' && $borrowing->returned_date !== null) {

            return response()->json([
                'status' => 'error',
                'message' => 'Book has been returned'
            ], 422);
        }

        $borrowing->update([
            'returned_date' => now(),
            'status' => 'returned'
        ]);

        $borrowing->book->returnBook();

        $borrowing->load('book', 'member');

        return new BorrowingResource($borrowing);
    }

    public function overdue()
    {
        $overdueBorrowings = Borrowing::with('book', 'member')
            ->where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->get();

        Borrowing::where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->update(['status' => 'overdue']);

        return BorrowingResource::collection($overdueBorrowings);
    }
}

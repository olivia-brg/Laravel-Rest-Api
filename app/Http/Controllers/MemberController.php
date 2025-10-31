<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use App\Http\Resources\MemberResource;
use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = Member::with('activeBorrowings')
        ->when($request->input('search'), function ($q, $search) {
            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'ilike', "%{$search}%")
                ->orWhere('email', 'ilike', "%{$search}%");
            });
        })
        ->when($request->input('status'), fn($q, $status) => $q->where('status', $status))
        ->paginate(10);

        return MemberResource::collection($query);
    }


    public function store(StoreMemberRequest $request)
    {
        $member = Member::create($request->validated());
        return new MemberResource($member);
    }


    public function show(Member $member)
    {
        $member->load(['activeBorrowings', 'borrowings']);
        return new MemberResource($member);
    }


    public function update(StoreMemberRequest $request, Member $member)
    {
        $member->update($request->validated());
        return new MemberResource($member);
    }


    public function destroy(Member $member)
    {
        if ($member->activeBorrowings()->exists()) {

            return response()->json([
                'status' => 'error',
                'message' => 'Member has active borrowings'
            ], 422);
        }

        $member->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Member deleted successfully'
        ], 200);
    }
}

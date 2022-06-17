<?php

namespace App\Http\Controllers;


use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Throwable;
use function response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function index(): JsonResponse
    {
        $perPage = request()->get('perPage', 10);
        if ($perPage > 100) {
            $perPage = 100;
        }

        return response()->json([
            User::paginate($perPage)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        return response()->json([
            'data' => User::findOrFail($id)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateUserRequest  $request
     * @param User $user
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        if (!$user->isAdmin()) {
            if (auth()->id() === $user->id) {
                $validatedData = $request->validated();
                if (isset($validatedData['new_password'])) {
                    $validatedData['password'] = bcrypt($validatedData['new_password']);
                    unset(
                        $validatedData['new_password'],
                        $validatedData['new_password_confirmation'],
                        $validatedData['current_password']
                    );
                }
                $user->update($validatedData);
            }
        } else {
            $validatedData = $request->validated();
            if (isset($validatedData['new_password'])) {
                $validatedData['password'] = bcrypt($validatedData['new_password']);
                unset(
                    $validatedData['new_password'],
                    $validatedData['new_password_confirmation'],
                    $validatedData['current_password']
                );
            }
            $user->update($validatedData);
        }
        return response()->json([
            'data' => $user
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        User::findOrFail($id)->delete();
        return response()->json([
            'message' => 'deleted'
        ], 204);
    }
}

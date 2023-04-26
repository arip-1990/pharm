<?php

namespace App\Http\Controllers\V1\Panel\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UpdateController extends Controller
{
    public function handle(UpdateRequest $request): JsonResponse
    {
        $requestValidated = $request->validated();
        if ($user = User::find($requestValidated['userIdentifier'])) {
            $user->update($requestValidated);
            return new JsonResponse(['user' => new UserResource($user)]);
        }

        return new JsonResponse(['message' => 'Not found'], 500);
    }
}

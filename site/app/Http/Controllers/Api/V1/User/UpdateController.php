<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UpdateController extends Controller
{
    public function handle(UpdateUpdateRequest $request): JsonResponse
    {
        $requestValidated = $request->validated();
        $user = User::find($requestValidated['userIdentifier']);
        if ($user) {
            $user->update($requestValidated);
            return new JsonResponse([
                'user' => new UserResource($user)
            ]);
        }
        
        
    }
}

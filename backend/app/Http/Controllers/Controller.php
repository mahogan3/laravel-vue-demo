<?php

namespace App\Http\Controllers;

use App\Models\AuthUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class Controller
{
    protected function deleteOrConflict(Model $model, string $conflictMessage): Response
    {
        try {
            $model->delete();
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return response(['message' => $conflictMessage], 409);
            }

            throw $e;
        }

        return response()->noContent();
    }

    protected function authUser(Request $request): ?AuthUser
    {
        return $request->attributes->get('authUser');
    }

    /**
     * Resolve the logged-in user and abort with 403 unless they're an admin.
     */
    protected function requireAdmin(Request $request): AuthUser
    {
        $user = $this->authUser($request);

        abort_unless($user?->isAdmin(), 403);

        return $user;
    }
}

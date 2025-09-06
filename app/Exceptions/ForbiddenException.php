<?php

namespace App\Exceptions;

use Exception;

class ForbiddenException extends Exception
{
    public function render($request)
    {
        if ($request->inertia()) {
            return Inertia::render('Error', [
                'status' => 403,
                'message' => $this->getMessage() ?: 'Доступ запрещён',
            ]);
        }

        return parent::render($request);
    }
}

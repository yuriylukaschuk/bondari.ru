<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class Handler extends ExceptionHandler
{
    /**
     * Обработка отображения исключений
     */
    public function render($request, Throwable $e): Response
    {
        $response = parent::render($request, $e);

        // Обработка только для Inertia-запросов
        if ($request->header('X-Inertia') && in_array($response->status(), [401, 403, 404, 419, 429, 500, 503])) {
            return Inertia::render('Error', [
                'status' => $response->status(),
                'message' => $this->getErrorMessage($response->status(), $e),
            ])
                ->toResponse($request)
                ->setStatusCode($response->status());
        }

        return $response;
    }

    /**
     * Получение сообщения об ошибке
     */
    protected function getErrorMessage(int $status, Throwable $e): string
    {
        return match ($status) {
            401 => 'Требуется авторизация',
            403 => 'Доступ запрещён',
            404 => 'Страница не найдена',
            419 => 'Срок действия страницы истёк',
            429 => 'Слишком много запросов',
            500 => 'Ошибка сервера',
            503 => 'Сервис недоступен',
            default => $e->getMessage() ?: 'Произошла ошибка',
        };
    }
}

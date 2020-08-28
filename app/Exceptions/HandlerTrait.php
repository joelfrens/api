<?php
namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpFoundation\Response;

trait HandlerTrait
{
	public function apiException($request, $exception)
	{
		if ($exception instanceof ModelNotFoundException) {
			return response()->json(
				[
					'errors' => 'Resource not found!'
				],
				Response::HTTP_NOT_FOUND
			);
		}

		if ($exception instanceof NotFoundHttpException) {
			return response()->json(
				[
					'errors' => 'Resource not found!'
				],
				Response::HTTP_NOT_FOUND
			);
		}

		if ($exception instanceof MethodNotAllowedHttpException) {
			return response()->json(
				[
					'errors' => 'Method not allowed!'
				],
				Response::HTTP_NOT_FOUND
			);
		}

		return parent::render($request, $exception);

	}
}
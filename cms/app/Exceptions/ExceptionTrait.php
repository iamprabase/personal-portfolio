<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait ExceptionTrait
{
	public function apiException($request,$e)
	{
		if($this->isModel($e)){
            return $this->ModelResponse($e);
        }
        if($this->isHttp($e)){
            return $this->HttpResponse($e);
        }
        if($this->isUnauthorized($e)){
        	return $this->SpatieResponse($e);
        }
        if($this->isAPIHttpException($e)){
        	return $this->HttpExceptionResponse($e);
        }
        return parent::render($request, $e);
	}

	private function isModel($e)
	{
		return $e instanceof ModelNotFoundException;
	}

	private function isHttp($e)
	{
		return $e instanceof NotFoundHttpException;
	}

	private function isAPIHttpException($e)
	{
		return $e instanceof HttpException;
	}

	private function isUnauthorized($e)
	{
		return $e instanceof UnauthorizedException;
	}

	private function ModelResponse($e)
	{
		return response()->json([
                'errors'=>'Model Not Found'
            ],Response::HTTP_NOT_FOUND);
	}

	private function HttpResponse($e)
	{
		return response()->json([
                'errors'=>'incorrect route'
            ],Response::HTTP_NOT_FOUND);
	}

	private function SpatieResponse($e)
	{
		return response()->json([
                'errors'=>'No Access'
            ],Response::HTTP_UNAUTHORIZED);
	}

	private function HttpExceptionResponse($e)
	{
		return response()->json([
                'errors'=>'Invalid Http route'
            ],Response::HTTP_NOT_FOUND);
	}
}
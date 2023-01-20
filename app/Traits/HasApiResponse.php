<?php

namespace App\Traits;

use App\Exceptions\ValidationResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use RuntimeException;
use Throwable;

trait HasApiResponse
{
    use Translatable;

    /**
     * Determine whether error code should be part of the response.
     */
    private bool $useResponseErrorCode = false;

    /**
     * Choose to wrap data or not.
     */
    private bool $useResponseWrapper = true;

    /**
     * Returns a successful ok HTTP response.
     *
     * @param string $message
     * @param mixed $data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function okResponse(string $message, $data = null): JsonResponse
    {
        return $this->successResponse($message, $data, 200);
    }

    /**
     * Returns a successful created HTTP response.
     *
     * @param mixed $data
     */
    public function createdResponse(string $message, $data = null): JsonResponse
    {
        return $this->successResponse($message, $data, 201);
    }

    /**
     * Returns a successful accepted HTTP response.
     *
     * @param mixed $data
     */
    public function acceptedResponse(string $message, $data = null): JsonResponse
    {
        return $this->successResponse($message, $data, 202);
    }

    /**
     * Returns a successful no content HTTP response.
     */
    public function noContentResponse(): JsonResponse
    {
        return $this->successResponse('', null, 204);
    }

    /**
     * Return a generic successful HTTP response.
     *
     * @param mixed $data
     */
    public function successResponse(string $message, $data = null, int $status = 200): JsonResponse
    {
        return $this->toJsonResponse($message, $status, $data);
    }

    /**
     * Returns a validation error response.
     */
    public function validationErrorResponse(Validator $validator, Request $request = null): JsonResponse
    {
        return (new ValidationResponseException($validator, $request))->getResponse();
    }

    /**
     * Returns an unauthenticated HTTP error response.
     */
    public function unauthenticatedResponse(string $message)
    {
        return $this->clientErrorResponse($message, 401);
    }

    /**
     * Returns a bad request HTTP error response.
     */
    public function badRequestResponse(string $message, ?array $error = null): JsonResponse
    {
        return $this->clientErrorResponse($message, 400, $error);
    }

    /**
     * Returns a bad request HTTP error response.
     */
    public function badRequestWithErrorCode(string $errorCode, ?array $error = null): JsonResponse
    {
        $this->useResponseErrorCode = true;

        return $this->badRequestResponse($errorCode, $error);
    }

    /**
     * Returns a forbidden HTTP error response.
     */
    public function forbiddenResponse(string $message, ?array $error = null): JsonResponse
    {
        return $this->clientErrorResponse($message, 403, $error);
    }

    /**
     * Return a not found HTTP error response.
     */
    public function notFoundResponse(string $message, ?array $error = null): JsonResponse
    {
        return $this->clientErrorResponse($message, 404, $error);
    }

    /**
     * Return a generic client HTTP error response.
     */
    public function clientErrorResponse(string $message, int $status = 400, ?array $error = null): JsonResponse
    {
        return $this->toJsonResponse($message, $status, $error);
    }

    /**
     * Return a generic server HTTP error response.
     */
    public function serverErrorResponse(string $string, int $status = 500, ?Throwable $exception = null): JsonResponse
    {
        if ($exception !== null) {
            report($exception);
        }

        return $this->toJsonResponse($string, $status);
    }

    /**
     * Return a generic HTTP response.
     *
     * @param string $message
     * @param int $status
     * @param mixed $data
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \RuntimeException
     */
    public function toJsonResponse(string $message, int $status, $data = null): JsonResponse
    {
        $isSuccessful = $status >= 100 && $status < 400;

        $translatedData = $this->translateMessageToArray(
            $message ?: 'Request was received'
        );

        $responseData = [
            'status' => $isSuccessful,
            'message' => $translatedData['message'],
        ];

        if (! $isSuccessful && $this->useResponseErrorCode) {
            if (is_null($translatedData['key'])) {
                throw new RuntimeException(
                    'The error code is not translatable. Check the specified translation path.'
                );
            }

            $responseData['error_code'] = $translatedData['key'];
        }

        if ($this->useResponseWrapper && ! empty($data)) {
            $responseData[$isSuccessful ? 'data' : 'error'] = $data;
        } elseif (! empty($data)) {
            $responseData = array_merge(
                $responseData,
                (new JsonResponse())->setData($data)->getData(true)
            );
        }

        return new JsonResponse($responseData, $status);
    }

    /**
     * Wrap JsonResponses to conform to the API response structure.
     *
     * Particularly handy for Laravel API Resources/Collections.
     *
     * Usages
     *
     * $this->wrapJsonResponse(new UserCollection(User::paginate())->response())
     * $this->wrapJsonResponse(new UserResource(User::find())->response())
     *
     * @param \Illuminate\Http\JsonResponse $response
     * @param string $message
     * @param bool $wrap
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function wrapJsonResponse(JsonResponse $response, string $message, ?bool $wrap = false): JsonResponse
    {
        $data = $response->getData(true);
        $responseData = is_array($data) ? $data : ['message_data' => $data];
        $message = (string) ($message ?: Arr::pull($responseData, 'message', ''));

        $this->useResponseWrapper = $wrap;

        return $this->toJsonResponse($message, $response->status(), $responseData)
            ->withHeaders($response->headers);
    }

    /**
     * Force json response to ignore response wrapper.
     */
    private function withoutWrap(): self
    {
        $this->useResponseWrapper = false;

        return $this;
    }
}

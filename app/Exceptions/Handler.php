<?php

namespace App\Exceptions;

use Throwable;

use Illuminate\Http\JsonResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Illuminate\Queue\MaxAttemptsExceededException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Route;

use Laravel\Passport\Exceptions\OAuthServerException;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

use Spatie\Permission\Exceptions\UnauthorizedException;

use App\Exceptions\Custom\{
    APIException,
    AbstractAPIException
};

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        APIException::class,
        BaseException::class,

        OAuthServerException::class,
        MaxAttemptsExceededException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  Throwable  $e
     * @return void
     */
    public function report(Throwable $e)
    {
        if ($e instanceof AbstractAPIException && !$e->shouldBeReported)
            return;

        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return mixed
     */
    public function render($request, Throwable $e)
    {
        if (
            $request->ajax() ||
            $request->wantsJson() ||
            ($request->subdomain() == 'api' ||
                $request->subdomain() == 'laravel'
            ) ||
            // this should never occur on a non json route but just incase
            $e instanceof AbstractAPIException
        ) {
            return $this->prepareJsonResponse($request, $e);
        }
        return parent::render($request, $e);
    }

    /**
     * Prepare a JSON response for the given exception.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Illuminate\Http\JsonResponse
     */
    protected function prepareJsonResponse($request, Throwable $e)
    {
        $errorMsg = collect($this->convertExceptionToArray($e))->prepend($this->getPrettyMessage($e), 'error');
        return new JsonResponse(
            // remove default message and replace it with pretty error to standardize all bh apis
            config('app.debug') ? $errorMsg : $errorMsg->forget('message'),
            ($e instanceof HttpExceptionInterface) ? $e->getStatusCode() : $this->getPrettyStatusCode($e),
            ($e instanceof HttpExceptionInterface) ? $e->getHeaders() : [],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }

    /**
     * Take in an error and convert it to a status code to return
     * 
     * @param Throwable $e 
     * @return int 
     */
    private function getPrettyStatusCode(Throwable $e): int
    {
        return match (true) {
            $e instanceof ValidationException => 400,
            $e instanceof AuthenticationException, $e instanceof AuthorizationException, $e instanceof UnauthorizedException => 401,
            $e instanceof ModelNotFoundException, $e instanceof NotFoundHttpException => 404,
            $e instanceof MethodNotAllowedHttpException => 405,
            $e instanceof TokenMismatchException => 419,
            // use a separate if inside because php intelephense doesnt approve of matches yet
            $e instanceof AbstractAPIException => ($e instanceof AbstractAPIException) ? $e->getStatusCode() : 500, // @phpstan-ignore-line
            default => 500
        };
    }

    /**
     * Take in an error and convert it to a standardized response for API methods
     * 
     * @param Throwable $e 
     * @return array 
     */
    private function getPrettyMessage(Throwable $e): array
    {
        return match (true) {
            $e instanceof ValidationException => ($e instanceof ValidationException) ? [
                'message' => $e->errors(),
                'prettyMessage' => $e->validator->errors()->first()
            ] : [], // @phpstan-ignore-line
            $e instanceof AuthenticationException => [
                'message' => 'Not authenticated',
                'prettyMessage' => 'Error authenticating, try refreshing the page'
            ],
            $e instanceof AuthorizationException => [
                'message' => $e->getMessage(),
                'prettyMessage' => $e->getMessage()
            ],
            $e instanceof UnauthorizedException => [
                'message' => $e->getMessage(),
                'prettyMessage' => 'Error authenticating, try refreshing the page'
            ],
            $e instanceof TokenMismatchException => [
                'message' => 'CSRF Token invalid',
                'prettyMessage' => 'Error authenticating, try refreshing the page'
            ],
            $e instanceof MethodNotAllowedHttpException => [
                'message' => 'HTTP method not allowed',
                'prettyMessage' => 'Sorry, something went wrong'
            ],
            $e instanceof ModelNotFoundException => [
                // allow error message passthroughs when using in app code but dont allow laravel to give its own message
                'message' => stripos($e->getMessage(), "model") === false ? $e->getMessage() : "Record not found",
                'prettyMessage' => 'Requested record does not exist'
            ],
            $e instanceof NotFoundHttpException => [
                'message' => 'Page not found',
                'prettyMessage' => 'Sorry, something went wrong'
            ],
            $e instanceof LockTimeoutException => [
                'message' => 'Request lock timeout',
                'prettyMessage' => 'Server currently busy, try again soon'
            ],
            $e instanceof ThrottleRequestsException => [
                'message' => 'Page throttle reached',
                'prettyMessage' => 'You are requesting too much. Please wait to try again'
            ],
            $e instanceof AbstractAPIException => ($e instanceof AbstractAPIException) ? [
                'message' => $e->getDetailedMessage(),
                'prettyMessage' => $e->getPrettyMessage()
            ] : [], // @phpstan-ignore-line
            default => [
                'message' => 'Sorry, something went wrong',
                'prettyMessage' => 'Sorry, something went wrong'
            ]
        };
    }
}

<?php

namespace App\Exceptions;

use Carbon\Exceptions\Exception as ExceptionsException;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Telegram\Bot\Laravel\Facades\Telegram;
use Throwable;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Facade;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        // $this->renderable(function (CustomException  $e, $request) {
        //     return response()->view('errors.custom', [], 500);
        // });
    }
    public function report(Throwable $exception)
    {
        if (!$exception instanceof ValidationException && !$exception instanceof NotFoundHttpException && !$exception instanceof MethodNotAllowedException && !$exception instanceof MethodNotAllowedHttpException) {
            // parent::report($exception);
            $html = '<b>[Lỗi] : </b><code>' . htmlentities($exception->getMessage()) . '</code>';
            $html .= '<b>[File] : </b><code>' . $exception->getFile() . '</code>';
            $html .= '<b>[Line] : </b><code>' . $exception->getLine() . '</code>';
            $html .= '<b>[URL] : </b><a href="' . url()->full() . '">' . url()->full() . '</a>';
            $html .= '<b>[Method] : </b><code>' . request()->method() . '</code>';
            $html .= '<b>[Time] : </b><code>' . now() . '(Asia/Ho_Chi_Minh)</code>';
            Telegram::sendMessage([
                'chat_id' => env('TELEGRAM_CHANNEL_ID', '-1001561327890'),
                'parse_mode' => 'HTML',
                'text' => $html
            ]);
        }
    }

    public function render($request, Throwable $exception)
    {
        if ($request->is('api/*')) {
            if ($exception instanceof ValidationException) {
                return $this->convertValidationExceptionToResponse($exception, $request);
            }
            if ($exception instanceof NotFoundHttpException) {
                return response(['success' => false, 'message' => 'Request not found'], 404);
            }
            return response([
                'success' => false,
                'error' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine() ,
                'code' => $exception->getCode()
            ], 500);
        }else{
            return parent::render($request, $exception);
        }

        
    }
}

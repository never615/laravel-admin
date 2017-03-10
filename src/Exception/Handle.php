<?php

namespace Encore\Admin\Exception;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

class Handle
{
    /**
     * Render exception.
     *
     * @param \Exception $exception
     *
     * @return string
     */
    public static function renderException(\Exception $exception)
    {
        Log::info("laravel admin handler");

        $error = new MessageBag([
            'type'      => get_class($exception),
            'message'   => $exception->getMessage(),
            'file'      => $exception->getFile(),
            'line'      => $exception->getLine(),
        ]);

        $errors = new ViewErrorBag();
        $errors->put('exception', $error);

        return view('admin::partials.exception', compact('errors'))->render();
    }
}

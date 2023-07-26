<?php

namespace App\Exceptions;

use Exception;

class BaseException extends Exception
{
    protected $errorMsg;
    protected $route;

    public function __construct($error = "Sorry, something went wrong", $route = false)
    {
        $this->errorMsg = $error;
        $this->route = $route;
    }
    /**
     * Render the exception into an HTTP response.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function render()
    {
        if ($this->route) {
            return redirect($this->route)
                ->withInput()
                ->withErrors($this->errorMsg);
        } else {
            if (request()->fullUrl() === redirect()->back()->getTargetUrl())
                return redirect('/')->withErrors($this->errorMsg);
            return back()
                ->withInput()
                ->withErrors($this->errorMsg);
        }
    }
}

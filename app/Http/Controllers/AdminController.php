<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use BadMethodCallException;

class AdminController extends Controller
{
    /**
     * All “dashboard” methods that should show the same view.
     */
    protected array $dashboardMethods = [
        'index',
        'marketing',
        'it',
        'procurement',
        'accounting',
        'support',
        'hrga',
        'spi',
    ];

    /**
     * Catch any calls to index(), marketing(), it(), etc.
     */
    public function __call(string $method, array $parameters)
    {
        if (in_array($method, $this->dashboardMethods, true)) {
            return view('layouts.app');
        }

        throw new BadMethodCallException("Method {$method} does not exist.");
    }
}

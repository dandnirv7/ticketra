<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make(\Illuminate\Contracts\Debug\ExceptionHandler::class)
            ->shouldRenderJsonWhen(function ($request, $e) {
                return $request->expectsJson()
                    || $request->wantsJson()
                    || $request->ajax()
                    || str_contains((string) $request->header('Accept'), 'application/json');
            });
    }
}

<?php

  namespace App\Http\Middleware;

  use Illuminate\Http\Request;

  class RobotsMiddleware
  {
    /**
     * @param  Request  $request
     * @return bool
     */
    protected function shouldIndex(Request $request): bool
    {
      return $request->segment(1) !== 'admin';
    }
  }
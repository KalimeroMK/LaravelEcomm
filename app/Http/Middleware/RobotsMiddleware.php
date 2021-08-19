<?php

  namespace App\Http\Middleware;

  use Illuminate\Http\Request;
  use Spatie\RobotsMiddleware\RobotsMiddleware as BaseRobotsMiddleware;


  class RobotsMiddleware extends BaseRobotsMiddleware
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
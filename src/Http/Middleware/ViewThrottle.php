<?php

namespace Canvas\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ViewThrottle
{
    /**
     * The number of seconds until a view expires.
     *
     * @const int
     */
    private const EXPIRES_IN = 3600;

    /**
     * Handle the incoming request.
     *
     * @param $request
     * @param $next
     * @return Response
     */
    public function handle(Request $request, Closure $next)
    {
        $posts = $this->getViewedPosts();

        if (! is_null($posts)) {
            $this->pruneExpiredViews($posts);
        }

        return $next($request);
    }

    /**
     * Get the viewed posts in session.
     *
     * @return array|null
     */
    private function getViewedPosts()
    {
        return session()->get('viewed_posts', null);
    }

    /**
     * Prune expired posts from the session.
     *
     * @param array $posts
     * @return void
     */
    private function pruneExpiredViews(array $posts)
    {
        foreach (collect($posts) as $key => $value) {
            if ($value < time() - self::EXPIRES_IN) {
                session()->forget('viewed_posts.'.$key);
            }
        }
    }
}

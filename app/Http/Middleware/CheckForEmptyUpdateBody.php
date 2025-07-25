<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckForEmptyUpdateBody
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the request method is PUT or PATCH
        if ($request->isMethod('PUT') || $request->isMethod('PATCH')) {
            // Get all request data (including JSON body)
            $input = $request->all();

            // If the input is empty, return a 400 Bad Request response
            if (empty($input)) {
                return response()->json([
                    'message' => 'The request body cannot be empty for update operations. Please provide data to update.',
                ], Response::HTTP_BAD_REQUEST); // HTTP 400 Bad Request
            }
        }

        return $next($request);
    }
}

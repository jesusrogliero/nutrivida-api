<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\UsersRole;

class RolesMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {  
            $user = $request->user();
            $user_role = UsersRole::where('user_id', $user->id)->first();

            if( $user_role->role_id != 1 && $user_role->role_id != 2 ) 
                throw new \Exception("Usted No Esta Autorizado Para Esta Sección", 1);

            return $next($request);

        } catch(\Exception $e) {
            \Log::info("Error  ({$e->getCode()}):  {$e->getMessage()}  in {$e->getFile()} line {$e->getLine()}");
            return \Response::json([
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 401);
        }
        
    }
}

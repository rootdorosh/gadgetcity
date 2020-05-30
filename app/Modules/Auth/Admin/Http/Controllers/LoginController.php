<?php

namespace App\Modules\Auth\Admin\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Base\AdminController;
use App\Modules\Auth\Admin\Http\Requests\LoginRequest;
use App\Services\Response\FractalManager;
use App\Base\CoreHelper;

/**
 * @group AUTH
 */
class LoginController extends AdminController
{    
    public function form()
    {
        return $this->view('login.form');
    }
    
    /**
     * Login
     *
     * @param LoginRequest $request
     */
    public function submit(LoginRequest $request)
    {
        if ($this->guard()->attempt(['email' => $request->email, 'password' => $request->password])) {
            
            return redirect(r('admin.dashboard'));
            //return redirect()->intended($this->redirectPath());
        } else {
            return back()->with('error', __('auth::login_form.invalid_credentials'));
        }
    }
    
    /**
     * Logout
     */
    public function logout()
    {
        $this->guard()->logout();
        
        return redirect(r('admin.auth.login.form'));
    }
    
    /*
     * 
     */
    public function guard()
    {
        return Auth::guard();
    }
}

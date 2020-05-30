<?php

namespace App\Modules\Auth\Admin\Services;

use Illuminate\Support\Str;
use App\Modules\Auth\Http\Requests\RemindPasswordEmail;
use App\Modules\Auth\Models\PasswordReset;
use App\Modules\User\Models\User;

/**
 * @package App\Modules\Auth
 */
class AuthService
{
    /*
     * @param RemindPasswordEmail $request
     * 
     * @return int
     */
    public function remindPasswordSendCode(RemindPasswordEmail $request): int
    {
        $code = rand(10000, 999999);
        
        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $request->email],
            ['token' => $code]
        );
        
        // send email TODO
        
        return $code;
    }

    /*
     * @param string $email
     * @param string $password
     * @return void
     */
    public function setPassword(string $email, string $password): void
    {
        $user = User::where('email', $email)->first();
        $user->password = $password;
        $user->save();
        
        //remove code
        PasswordReset::where('email', $email)->delete();
    }

    /*
     * @param User $user
     * @return string
     */
    public function login(User $user): string
    {
        $token = (string) Str::random(60);

        $user->forceFill([
            'api_token' => hash('sha256', $token),
        ])->save(); 
        
        return $token;
    }
    
    /*
     * @param User $user
     * @return void
     */
    public function logout(User $user): void
    {
        $user->api_token = null;
        $user->save();
    }
    
    /*
     * @param string $email
     * @return string|null
     */
    public function getApiToken(string $email): ?string
    {
        return User::where('email', $email)->first()->api_token;
    }
    
    /*
     * @param string $email
     * @return string|null
     */
    public function getPasswordResetCode(string $email): ?string
    {
        return PasswordReset::where('email', $email)->first()->token;
    }

}

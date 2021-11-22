<?php

namespace App\Auth;

use App\Models\User;

class Auth
{
    protected $user;

    public function attempt($username, $password)
    {
        $user = User::where('person_username', $username)
                    ->get(['person_firstname','person_lastname','person_username','person_password','position_id'])
                    ->first();

        if(!$user) {
            return false;
        }

        if($password == $user->person_password) {
            $this->user = $user;
            
            return true;
        }

        return false;
    }

    public function getUser()
    {
        if($this->user) return $this->user;
    }
}

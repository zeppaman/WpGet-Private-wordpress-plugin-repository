<?php  
namespace   WpGet\Models;
class UserInfo
{
    public $roles;
    public $user;
    public $token;

    public function hasRole($role)
    {
        return array_key_exists($role,$roles);
    }

}
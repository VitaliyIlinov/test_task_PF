<?php

namespace app\Http\Controllers;


use app\Models\User;

class UserController
{

    public function show($id)
    {
        $user = User::getUser($id);

        return $user ?: "There is no user with id {$id}.Please choose from 1 to " . User::$userCountData;
    }

    public function showAll()
    {
        return User::getData();
    }


}
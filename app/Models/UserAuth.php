<?php

namespace app\Models;


class UserAuth
{

    static $userCount = 6;
    static $users;

    public static function getUsers()
    {
        if (!self::$users) {
            for ($i = 1; $i <= self::$userCount; $i++) {
                self::$users[] = [
                    'id' => $i,
                    'name' => "user_{$i}",
                    'password' => "password_{$i}"
                ];

            }
        }
        return self::$users;
    }

    public static function find($name)
    {
        $data = self::getUsers();

        return array_filter($data, function ($v, $k) use ($name) {
            return $v['name'] == $name;
        }, ARRAY_FILTER_USE_BOTH);
    }
}
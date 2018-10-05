<?php


namespace app\Models;


class User
{
    static $userCountData = 30;
    static $data;

    public static function getData()
    {
        if (!self::$data) {
            for ($i = 1; $i <= self::$userCountData; $i++) {
                self::$data[] = [
                    'id' => $i,
                    'name' => "name_{$i}",
                    'age' => rand(1, 60),
                    'phone' => substr(str_repeat($i, 9), 0, 9)
                ];

            }
        }
        return self::$data;
    }

    public static function getUser($id)
    {
        $data = self::getData();

        return array_filter($data, function ($v, $k) use ($id) {
            return $v['id'] == $id;
        }, ARRAY_FILTER_USE_BOTH);
    }
}
<?php

class SportCategory
{
    private static $data = array(
        "1" => array(
            'zh_name' => '足球',
            'en_name' => 'Football',
        ),
        "2" => array(
            'zh_name' => '篮球',
            'en_name' => 'Basketball',
        ),
        "3" => array(
            'zh_name' => '台球',
            'en_name' => 'Snooker',
        ),
        "4" => array(
            'zh_name' => '电竞',
            'en_name' => 'E-Sport',
        ),
        "8" => array(
            'zh_name' => '信息中心',
            'en_name' => 'Message Centre',
        ),
    );

    public static function check($category_id, $name_type = "cn") {
        foreach (self::$data as $cat_id => $names) {
            if($cat_id == $category_id) {
                if($name_type == "en") {
                    return $names['en_name'];
                } else if($name_type == "cn") {
                    return $names['zh_name'];
                }
            }
        }
    }

    public static function getChineseNames() {
        $result = array();
        foreach (self::$data as $cat_id => $names) {
            $result[$cat_id] = $names['zh_name'];
        }
        return $result;
    }

    public static function getEnglishNames() {
        $result = array();
        foreach (self::$data as $cat_id => $names) {
            $result[$cat_id] = $names['en_name'];
        }
        return $result;
    }

}
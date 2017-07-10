<?php

class Conf_Mysql{
    const TRY_MAX = 3;

    public static $dbconf = array(
        'servers' => array(
            0 => 'mysql:host=255.355.255.255;port=3306',
            1 => 'mysql:host=255.355.255.255;port=3306',
        ),
        'encode' => 'utf8',
        'media' => array(
            'user' => 'nana',
            'passwd' => '123456',
            'dbname' => 'media',
        ),
        'video' => array(
            'user' => 'nana',
            'passwd' => '123456',
            'dbname' => 'video',
        ),
    );
}

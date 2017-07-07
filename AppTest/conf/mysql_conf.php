<?php

class Conf_Mysql{
    const TRY_MAX = 3;

    public static $dbconf = array(
        'servers' => array(
            0 => 'mysql:host=172.17.12.95;port=3306',
            1 => 'mysql:host=172.17.12.95;port=3306',
        ),
        'encode' => 'utf8',
        'jupiter_media' => array(
            'user' => 'poseidon',
            'passwd' => '123456',
            'dbname' => 'db_jupiter_media',
        ),
        'jupiter_video' => array(
            'user' => 'poseidon',
            'passwd' => 'lfAPVoIsU+YI6U',
            'dbname' => 'db_jupiter_video',
        ),
    );
}

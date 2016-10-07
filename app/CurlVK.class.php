<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 07.10.16
 * Time: 18:18
 */

namespace App;


class CurlVK
{
    public $token;
    private $url = 'https://api.vk.com/method';
    public $username;


    function __construct($token) {

    }

    function executeMethod($method_name, $options) {
        $this->url .= '/' . $method_name;
        if ($options) {
            $options_out = [];
            foreach($options as $k=>$v)
            {
                $options_out[] = "$k=$v";
            }
            $this->url .= '?'.implode("&", $options_out);
        }
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $exec = curl_exec($ch);
        curl_close($ch);
        return json_decode($exec, true);
    }
}
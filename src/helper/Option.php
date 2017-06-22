<?php

namespace helper;

class Option
{
    public static function get($f3, $overwrite = ['debug' => true])
    {
        $options = [
            'app_id' => $f3->get('WECHAT_APP_ID'),
            'secret' => $f3->get('WECHAT_APP_SECRET'),
            'token' => $f3->get('WECHAT_TOKEN'),
            'log' => [
                'level' => 'debug',
                'file' => $f3->get('LOGS') . 'wechat.log',
            ]
        ];

        foreach ($overwrite as $key => $value) {
            $options[$key] = $value;
        }

        return $options;
    }
}
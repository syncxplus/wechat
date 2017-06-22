<?php

namespace app;

use EasyWeChat\Foundation\Application;
use helper\Option;

class Menu
{
    function main($f3)
    {
        $app = new Application(Option::get($f3));
        $menu = $app->menu;
        if ($f3->get('VERB') == 'GET') {
            echo $menu->current();
        } else {
            echo $menu->add([
                [
                    'type' => 'view',
                    'name' => '看图',
                    'url' => $f3->get('WECHAT_SERVER') . '/image'
                ]
            ]);
        }
    }
}
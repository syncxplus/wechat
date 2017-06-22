<?php

namespace app;

use EasyWeChat\Foundation\Application;
use EasyWeChat\Message\Text;
use helper\Image;
use helper\Option;

class Index
{
    function main($f3)
    {
        $app = new Application(Option::get($f3));
        $app->server->setMessageHandler(function ($message) use ($app, $f3) {
            switch ($message->MsgType) {
                case 'text':
                    if ($message->Content == '1') {
                        $lastVisitAlbum = $f3->get('LAST_VISIT_ALBUM');
                        if (!empty($lastVisitAlbum)) {
                            return new Text(['content' => $lastVisitAlbum]);
                        }
                    }
                    return new Text(['content' => $f3->get('WECHAT_SERVER') . '/image']);
            }
            return new Text(['content' => 'Hello world']);
        });
        $app->server->serve()->send();
    }
}

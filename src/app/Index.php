<?php

namespace app;

use EasyWeChat\Foundation\Application;
use EasyWeChat\Message\Image;
use EasyWeChat\Message\Text;
use helper\Option;

class Index
{
    function main($f3)
    {
        $app = new Application(Option::get($f3));
        $app->server->setMessageHandler(function ($message) use ($app, $f3) {
            switch ($message->MsgType) {
                case 'text':
                    if ($message->Content == 'album') {
                        $lastVisitAlbum = $f3->get('LAST_VISIT_ALBUM');
                        if (!empty($lastVisitAlbum)) {
                            return new Text(['content' => $lastVisitAlbum]);
                        }
                    }
                    return new Text(['content' => $message->Content]);
                case 'image':
                    file_put_contents('/tmp/images.log', $message->PicUrl, LOCK_EX | FILE_APPEND);
                    return new Image(['media_id' => $message->MediaId]);
                default:
                    ob_start();
                    var_dump($message);
                    $f3->log(ob_get_clean());
            }
            return new Text(['content' => 'Hello world']);
        });
        $app->server->serve()->send();
    }
}

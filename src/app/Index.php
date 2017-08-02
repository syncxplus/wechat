<?php

namespace app;

use EasyWeChat\Foundation\Application;
use EasyWeChat\Message\Image;
use EasyWeChat\Message\Text;
use helper\Database;
use helper\Option;

class Index
{
    use Url;

    function main($f3)
    {
        $app = new Application(Option::get($f3));
        $app->server->setMessageHandler(function ($message) use ($app, $f3) {
            switch ($message->MsgType) {
                case 'text':
                    switch ($message->Content) {
                        case '看看':
                            return new Text(['content' => $this->url('/image')]);
                        case '切换':
                            return new Text(['content' => $this->url('/shot')]);
                        case '最近':
                            $lastVisitAlbum = $f3->get('LAST_VISIT_ALBUM');
                            if (!empty($lastVisitAlbum)) {
                                return new Text(['content' => $lastVisitAlbum['url']]);
                            } else {
                                return new Text(['content' => '最近啥也没有']);
                            }
                    }
                    return new Text(['content' => $message->Content]);
                case 'image':
                    $image = new Database('image');
                    $image['url'] = $message->PicUrl;
                    $image->save();
                    return new Image(['media_id' => $message->MediaId]);
                default:
                    ob_start();
                    var_dump($message);
                    (new \Logger())->debug(ob_get_clean());
            }
            return new Text(['content' => 'Hello world']);
        });
        $app->server->serve()->send();
    }
}

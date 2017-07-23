<?php

namespace app;

class Upload extends \Web
{
    function get()
    {
        echo \Template::instance()->render('upload.html');
    }

    function post($f3)
    {
        $data = [];
        $f3->set('UPLOADS', WEB . '/images/');
        $receive = array_keys(parent::receive(null, true, false));
        foreach ($receive as $item) {
            $data[] = $f3->get('BASE') . '/images/' . preg_replace('/^.+[\\\\\\/]/', '', $item);
        }
        echo json_encode(['images' => $data], JSON_UNESCAPED_UNICODE);
    }
}

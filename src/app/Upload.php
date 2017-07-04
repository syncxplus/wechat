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
        $f3->set('UPLOADS', WEB . '/photo/');
        list($receive) = array_keys(parent::receive(null, true, false));
        echo $f3->get('BASE') . '/photo/' . preg_replace('/^.+[\\\\\\/]/', '', $receive);
    }
}

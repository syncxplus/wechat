<?php

namespace app;

class Upload extends \Web
{
    private $code;

    function get(\Base $f3)
    {
        $f3->set('code', $this->code);
        echo \Template::instance()->render('upload.html');
    }

    function post($f3)
    {
        $data = [];
        $receive = array_keys(parent::receive(null, true, false));
        foreach ($receive as $item) {
            $data[] = $f3->get('BASE') . '/data/uploads/' . preg_replace('/^.+[\\\\\\/]/', '', $item);
        }
        echo json_encode(['images' => $data], JSON_UNESCAPED_UNICODE);
    }

    function beforeRoute(\Base $f3) {
        $this->code = $f3->get('REQUEST.code') ?? false;
        if (!$this->code || $this->code != $f3->get('DEFAULT_VERIFY_CODE')) {
            $f3->error('Unauthorized access');
        }
    }
}

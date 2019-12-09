<?php

namespace app;

use helper\Database;
use helper\QiniuHelper;

class Blame
{
    function get(\Base $f3)
    {
        $factories = [];
        $query = Database::db()->exec("select value from meta where type='SYS_CONFIG_OPTION' and meta_key='MANUFACTORY' order by version desc limit 1");
        if ($query) {
            $factories = explode(',', $query[0]['value']);
        }
        sort($factories);
        $f3->set('factories', $factories);
        echo \Template::instance()->render('blame.html');
    }

    function post()
    {
        $files = [];
        foreach ($_FILES as $file) {
            $files[] = $file['tmp_name'];
        }
        echo json_encode(['images' => QiniuHelper::instance()->upload($files)], JSON_UNESCAPED_UNICODE);
    }
}

<?php

namespace app;

use helper\Database;
use helper\Image as ImageHelper;

class Image
{
    function get($f3)
    {
        $image = ImageHelper::get($f3);
        $context = substr($image, strlen(ImageHelper::$TARGET));
        $name = preg_replace('/\//', '_', $context);

        $dir = WEB . '/download';
        if (!is_dir($dir)) {
            mkdir($dir);
        }

        $file = $dir . '/' . $name;
        if (!is_file($file)) {
            file_put_contents($file, file_get_contents($image));
        }

        $f3->set('image', $f3->get('BASE') . '/download/' . $name);
        $f3->set('tag', 'download');
        echo \Template::instance()->render('image.html');
    }

    function shot($f3)
    {
        $dir = WEB . '/photo';
        if (!is_dir($dir)) {
            mkdir($dir);
        }

        $image = new Database('image');
        $image->load();
        if (!$image->dry()) {
            $name = preg_replace('/^.+[\\\\\\/]/', '', $image['url']);
            $file = $dir . '/' . $name;
            if (!is_file($file)) {
                file_put_contents($file, file_get_contents($image['url']));
            }
            $image->erase();
        }

        $list = scandir($dir);
        array_shift($list); //remove .
        array_shift($list); //remove ..

        if ($list) {
            $f3->set('image', $f3->get('BASE') . '/photo/' . $list[array_rand($list)]);
            $f3->set('tag', 'photo');
            echo \Template::instance()->render('image.html');
        } else {
            $this->get($f3);
        }
    }

    function delete($f3)
    {
        $name = $_POST['name'];
        $tag = $_POST['tag'];
        $file = WEB . '/' . $tag . '/' . $name;

        if (is_file($file)) {
            unlink($file);
        }

        echo 'SUCCESS';
    }

    function deleteAll($f3)
    {
        exec('rm -rf /var/www/html/download/*');
        echo 'SUCCESS';
    }

    function move($f3)
    {
        $name = $_POST['name'];
        $source = WEB . '/download/' . $name;
        $target = WEB . '/photo/' . $name;

        if (is_file($source)) {
            rename($source, $target);
        }

        echo 'SUCCESS';
    }
}

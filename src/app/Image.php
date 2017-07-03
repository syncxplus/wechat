<?php

namespace app;

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
        echo \Template::instance()->render('image.html');
    }

    function delete($f3)
    {
        $name = $_POST['name'];
        $file = WEB . '/download' . $name;
        if (is_file($file)) {
            unlink($file);
        }
        echo 'SUCCESS';
    }
}

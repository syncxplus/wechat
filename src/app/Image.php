<?php

namespace app;

use helper\Image as ImageHelper;

class Image
{
    const HOLDER = 'https://qiniu.syncxplus.com/meta/holder.jpg';

    function get(\Base $f3)
    {
        //$image = ImageHelper::get($f3);
        //$f3->set('image', $this->download($f3, $image));
        //echo \Template::instance()->render('download.html');
        $f3->set('images', implode(',', ImageHelper::get()));
        echo \Template::instance()->render('view.html');
    }

    function open($f3)
    {
        $excludePattern = '/^(2015-05-|P70206-).+\\.jpg$/';
        $list = scandir($f3->UPLOADS);
        array_shift($list); //remove .
        array_shift($list); //remove ..
        if ($list) {
            $image = $list[array_rand($list)];
            while (preg_match($excludePattern, $image)) {
                $image = $list[array_rand($list)];
            }
            $f3->set('image', $f3->get('BASE') . '/data/uploads/' . $image);
        } else {
            $f3->set('image', self::HOLDER);
        }
        echo \Template::instance()->render('open.html');
    }

    function shot($f3)
    {
        $dir = $f3->UPLOADS;

        $list = scandir($dir);
        array_shift($list); //remove .
        array_shift($list); //remove ..

        if ($list) {
            $f3->set('image', $f3->get('BASE') . '/data/uploads/' . $list[array_rand($list)]);
        } else {
            $f3->set('image', self::HOLDER);
        }

        echo \Template::instance()->render('image.html');
    }

    function download($f3)
    {
        $url = $f3->get('POST.url');
        $path = explode('/', parse_url($url, PHP_URL_PATH));
        $name = end($path);
        $dir = HTML . '/data/uploads/';
        $file = $dir . $name;
        if (!is_file($file)) {
            $f3->get('LOGGER')->write(sprintf('curl --refer %s -o %s %s', ImageHelper::HOST, $file, $url));
            exec(sprintf('curl --refer %s -o %s %s', ImageHelper::HOST, $file, $url));
        }
        echo $f3->get('BASE') . '/data/uploads/' . $name;
    }

    function delete($f3)
    {
        $name = $_POST['name'];
        $file = $f3->UPLOADS . $name;

        if (is_file($file)) {
            unlink($file);
        }

        echo 'SUCCESS';
    }

    function emptyAll($f3)
    {
        exec('rm -rf /var/www/html/data/downloads/*');
        echo 'SUCCESS';
    }

    function move($f3)
    {
        $name = $_POST['name'];
        $source = HTML . '/data/downloads/' . $name;
        $target = HTML . '/data/uploads/' . $name;

        if (is_file($source)) {
            rename($source, $target);
        }

        echo 'SUCCESS';
    }
}

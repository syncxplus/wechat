<?php

namespace app;

use helper\Database;
use helper\Image as ImageHelper;

class Image
{
    function get($f3)
    {
        $image = ImageHelper::get($f3);
        $f3->set('image', $this->download($f3, $image));
        echo \Template::instance()->render('download.html');
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
            $f3->set('image', 'http://qiniu.syncxplus.com/meta/holder.jpg');
        }
        echo \Template::instance()->render('open.html');
    }

    function shot($f3)
    {
        $dir = $f3->UPLOADS;
        $image = new Database('image');
        $image->load();
        if (!$image->dry()) {
            //http://mmbiz.qpic.cn/mmbiz_jpg/EDHSv38xr2ISkTu2uTTiaXwMu8NlnRVVwn0WodBKjOw9sWR0N8NFngOjjmvkkaJbU2zuGWFq6Gt7uibpJdhxdxIw/0
            //$name = preg_replace('/^.+[\\\\\\/]/', '', $image['url']);
            $name = 'wx_' . date('YmdHis') . '.jpg';
            $file = $dir . $name;
            file_put_contents($file, file_get_contents($image['url']));
            $image->erase();
        }

        $list = scandir($dir);
        array_shift($list); //remove .
        array_shift($list); //remove ..

        if ($list) {
            $f3->set('image', $f3->get('BASE') . '/data/uploads/' . $list[array_rand($list)]);
        } else {
            $f3->set('image', 'http://qiniu.syncxplus.com/meta/holder.jpg');
        }

        echo \Template::instance()->render('image.html');
    }

    function review($f3)
    {
        $album = $f3->get('LAST_VISIT_ALBUM');
        if ($album) {
            $page = $_GET['page'] ?? 1;
            if ($page >= 1 && $page <= $album['count']) {
                $url = $album['url'] . '/' . $page;
                $f3->set('page', $page);
                $f3->set('count', $album['count']);
                $f3->set('image', $this->download($f3, ImageHelper::getFromUrl($url)));
                echo \Template::instance()->render('review.html');
                return;
            } else {
                echo '<h1>Overflow</h1>';
            }
        } else {
            echo '<h1>Nothing to review</h1>';
        }
    }

    function download($f3, $image)
    {
        $context = substr($image, strlen(ImageHelper::$TARGET));
        $name = preg_replace('/\//', '_', $context);
        $dir = HTML . '/data/downloads/';
        $file = $dir . $name;
        if (!is_file($file)) {
            //file_put_contents($file, file_get_contents($image));
            exec(sprintf('curl --refer %s -o %s %s', ImageHelper::$TARGET, $file, $image));
        }
        return $f3->get('BASE') . '/data/downloads/' . $name;
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

<?php

namespace helper;

use Httpful\Request;
use Symfony\Component\DomCrawler\Crawler;

class Image
{
    public static $TARGET = 'http://www.mmjpg.com';

    public static function get($f3)
    {
        $crawler = new Crawler();

        // 解析相册总数
        $albumCount = $f3->get('ALBUM_COUNT');
        if (empty($albumCount)) {
            $crawler->clear();
            $response = Request::get(self::$TARGET)->send();
            $crawler->addHtmlContent($response->body);
            $latest = $crawler->filter('.main .pic ul li>a')->eq(0)->attr('href');
            $path = explode('/', $latest);
            $albumCount = $path[count($path) - 1];
            $f3->set('ALBUM_COUNT', $albumCount, 3600);
            $f3->log('Album count: {albumCount}', ['albumCount' => $albumCount]);
        }

        // 随机选择相册
        $crawler->clear();
        $album = random_int(1, $albumCount);

        //解析相册图片总数
        $response = Request::get(self::$TARGET . '/mm/' . $album)->send();
        $crawler->addHtmlContent($response->body);
        $imageNav = $crawler->filter('#page a');
        $lastImageHref = $imageNav->eq($imageNav->count() - 2)->attr('href');
        $path = explode('/', $lastImageHref);
        $imageCount = $path[count($path) - 1];
        $path[count($path) - 1] = random_int(1, $imageCount);
        $f3->log('Album {album} with {imageCount} images',
            [
                'album' => self::$TARGET . '/mm/' . $album,
                'imageCount' => $imageCount
            ]
        );

        // 随机选择图片
        $crawler->clear();
        $image = self::getFromUrl(self::$TARGET . implode('/', $path));

        //缓存本次访问相册
        array_pop($path);
        $f3->set('LAST_VISIT_ALBUM', ['url' => self::$TARGET . implode('/', $path), 'count' => $imageCount], 600);

        return $image;
    }

    public static function getFromUrl($url)
    {
        $crawler = new Crawler();
        $response = Request::get($url)->send();
        $crawler->addHtmlContent($response->body);
        return $crawler->filter('#content img')->eq(0)->attr('src');
    }
}

<?php

namespace helper;

use Httpful\Request;
use Symfony\Component\DomCrawler\Crawler;

class Image
{
    public static $TARGET = 'http://www.mmjpg.com';
    const HOST = 'https://www.ku137.net';
    const CACHE = 3600 * 24 * 7;

    public static function get()
    {
        $f3 = \Base::instance();
        $logger = $f3->get('LOGGER');
        $crawler = new Crawler();
        //解析相册总数
        if ($f3->exists('tags')) {
            $tags = $f3->get('tags');
        } else {
            $response = Request::get(self::HOST . '/b/tag/')->send();
            $crawler->clear();
            $crawler->addHtmlContent($response->body);
            $list = $crawler->filter('.jigou ul li a');
            $tags = [];
            for ($i = 0; $i < $list->count(); $i++) {
                $tags[$i] = $list->eq($i)->attr('href');
            }
            $f3->set('tags', $tags, self::CACHE);
        }
        //随机选择一个相册计算页面总数
        $tag = random_int(1, count($tags));
        $key = "tag_{$tag}_page_count";
        if ($f3->exists($key)) {
            $pageCount = $f3->get($key);
        } else {
            $tagUrl = self::HOST . $tags[$tag];
            $logger->write('tag:' . $tagUrl);
            $response = Request::get($tagUrl)->send();
            $crawler->clear();
            $crawler->addHtmlContent($response->body);
            $pages = $crawler->filter('.page a[href]');
            $lastPage = $pages->eq(count($pages) - 1)->attr('href');
            $explode = explode('_', substr($lastPage, 0, 0 - strlen('.html')));
            $pageCount = array_pop($explode);
            $f3->set($key, $pageCount, self::CACHE);
        }
        $page = ($pageCount == 1) ? $pageCount : random_int(1, $pageCount);
        $tagNumber = explode('/', $tags[$tag])[2];
        $pageUrl = self::HOST . $tags[$tag] . 'list_' . $tagNumber . '_' . $page . '.html';
        $logger->write('page: ' . $pageUrl);
        $key = 'page_' . md5($pageUrl);
        if ($f3->exists($key)) {
            $body = $f3->get($key);
        } else {
            $body = Request::get($pageUrl)->send()->body;
            $f3->set($key, $body, self::CACHE);
        }
        $crawler->clear();
        $crawler->addHtmlContent($body);
        $links = $crawler->filter('a[title]');
        $total = $links->count();
        $header = 6;
        $footer = 2;
        $logger->write("total: $total, header: $header, footer: $footer");
        $idx = random_int($header, $total - $footer - 1);
        $logger->write("id: $idx");
        $url = $links->eq($idx)->attr('href');
        $logger->write('url: ' . $url);
        //提取图片链接
        $key = 'image_' . md5($url);
        if ($f3->exists($key)) {
            $body = $f3->get($key);
        } else {
            $body = Request::get($url)->send()->body;
            $f3->set($key, $body, self::CACHE);
        }
        $crawler->clear();
        $crawler->addHtmlContent($body);
        $images = $crawler->filter('img.tupian_img');
        $data = [];
        for ($i = 0; $i < $images->count(); $i++) {
            $data[] = $images->eq($i)->attr('src');
        }
        //缓存本次访问相册
        $f3->set('LAST_VISIT_ALBUM', $url, 600);
        return $data;
    }

    public static function getFromUrl($url)
    {
        $crawler = new Crawler();
        $response = Request::get($url)->send();
        $crawler->addHtmlContent($response->body);
        return $crawler->filter('#content img')->eq(0)->attr('src');
    }
}

<?php

namespace helper;

use Httpful\Request;
use Symfony\Component\DomCrawler\Crawler;

class Image
{
    public static $TARGET = 'http://www.mmjpg.com';
    const HOST = 'https://www.ku137.net';

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
            $f3->set('tags', $tags, 3600);
        }
        //随机选择一个相册计算页面总数
        $tag = random_int(1, count($tags));
        $tagUrl = self::HOST . $tags[$tag];
        $logger->write('tag:' . $tagUrl);
        $response = Request::get($tagUrl)->send();
        $crawler->clear();
        $crawler->addHtmlContent($response->body);
        $pages = $crawler->filter('.page a');
        if ($pages->count() > 1) {
            $lastPage = $pages->eq($pages->count() - 1)->attr('href');
            $suffix = '.html';
            $lastPage = substr($lastPage, 0, 0 - strlen($suffix));
            $explode = explode('_', $lastPage);
            $pageCount = array_pop($explode);
            //随机选择一个页面内图片
            $page = random_int(1, $pageCount);
            $pageUrl = self::HOST . $tags[$tag] . implode('_', $explode) . '_' . $page . $suffix;
        } else {
            $pageUrl = $tagUrl;
        }
        $logger->write('page: ' . $pageUrl);
        $response = Request::get($pageUrl)->send();
        $crawler->clear();
        $crawler->addHtmlContent($response->body);
        $links = $crawler->filter('a[title]');
        $total = $links->count();
        $header = 6;
        $footer = 2;
        $count = $total - $header - $footer;
        $logger->write("total: $total, header: $header, footer: $footer");
        $idx = random_int($header, $count - 1);
        $logger->write("id: $idx");
        $url = $links->eq($idx)->attr('href');
        $logger->write('url: ' . $url);
        //提取图片链接
        $response = Request::get($url)->send();
        $crawler->clear();
        $crawler->addHtmlContent($response->body);
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

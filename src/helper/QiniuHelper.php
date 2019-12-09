<?php

namespace helper;

use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

class QiniuHelper extends \Prefab
{
    function upload(array $files)
    {
        $results = [];
        $f3 = \Base::instance();
        $auth = new Auth($f3->get('QINIU_ACCESS_KEY'), $f3->get('QINIU_SECRET_KEY'));
        $bucket = $f3->get('QINIU_BUCKET');
        $token = $auth->uploadToken($bucket);
        foreach ($files as $file) {
            $uploadMgr = new UploadManager();
            list($ret, $err) = $uploadMgr->putFile($token, pathinfo($file, PATHINFO_BASENAME), $file);
            if ($err) {
                throw new \Exception($err);
            } else {
                unlink($file);
                $results[] = "https://onlymaker.syncxplus.com/{$ret['key']}";
            }
        }
        return $results;
    }

    function thumbnail($url, $scale = 100)
    {
        $suffix = "imageView2/0/w/$scale";
        $query = parse_url($url, PHP_URL_QUERY);
        if (!$query) {
            return "$url?$suffix";
        } else {
            $matched = preg_match('/^.*imageView2\\/0\\/w\\/(?<scale>\\d+)$/', $url, $matches);
            if ($matched) {
                if ($matches['scale'] != $scale) {
                    $url = str_replace("imageView2/0/w/{$matches['scale']}", $suffix, $url);
                }
                return $url;
            } else {
                return $url . $suffix;
            }
        }
    }
}

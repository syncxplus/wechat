<?php

namespace app;

use helper\Image as ImageHelper;

class Image
{
    function get($f3)
    {
        $image = ImageHelper::get($f3);
        header('Content-Type:' . \Web::instance()->mime($image));
        echo file_get_contents($image);
    }
}
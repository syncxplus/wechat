<?php

namespace app;

class Verify
{
    function base($f3)
    {
        echo $_POST['code'] == $f3->DEFAULT_VERIFY_CODE ? "SUCCESS" : "FAILURE";
    }
}

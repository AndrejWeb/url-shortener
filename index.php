<?php

require 'app/vendor/autoload.php';

if(isset($_GET['url'])) {
    $db = \App\Database\DB::getInstance();
    $urlShortener = new \App\URLShortener();

    $url = $urlShortener->getShortURLByCode($_GET['url']);

    if($url)
    {
        if($url["destination"] !== null && $url["destination"] != "")
        {
            $urlShortener->visitURL($url);
            header("Location: ".$url["destination"]);
            exit();
        }
    }
}

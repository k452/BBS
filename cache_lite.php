<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ja" xml:lang="ja">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>キャッシュ</title>
</head>
<body>
<?php
//セッション情報の有無を判別
session_start();
if(!$_SESSION[NAME]){
    header("location:login.php");
}

// Include the package
require_once('Cache/Lite.php');

// Set a id for this cache
$id = '123';

// Set a few options
$options = array(
    'cacheDir' => 'Cache/tmp/',
    'caching'  => 'true',
    'lifeTime' => 600,
    'automaticSerialization' => 'true'
);

// Create a Cache_Lite object
$Cache_Lite = new Cache_Lite($options);

// Test if thereis a valide cache for this id
$cache_data = $Cache_Lite->get($id);

if ($cache_data = $Cache_Lite->get($id)){
    $content = $cache_data;
    echo($content);
}else{
    $content = include("bbs.php");
    $Cache_Lite->save($content);
}
?>
</body>
</html>
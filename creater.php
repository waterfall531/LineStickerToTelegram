<?php
include __DIR__.DIRECTORY_SEPARATOR.'Controller.php';
$c = new Controller();

$name     = 'dmlkfj'.rand(100,999);
$linkName = $name.'_by_MakeStickersWithRandyBot';

$return = $c->call(
    "{$c->apiUrl}/{$c->botKey}/createnewstickerset",
    [
        'user_id'      => $c->userID,
        'name'         => $linkName,//英文開頭 _by_MakeStickersWithRandyBot 結尾 、 shardLink
        'title'        => $name,//showName
        'png_sticker' => 'https://f2e-test.baifu-tech.net/tgImage/tg_157778399.png.png', //firstSticker
        'emojis'       => '😀',
    ], "POST"
);
echo PHP_EOL.$return.PHP_EOL;

echo "https://t.me/addstickers/".$linkName;
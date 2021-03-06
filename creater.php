<?php
include __DIR__.DIRECTORY_SEPARATOR.'Controller.php';

function generateRandomString($length = 10) {
    $characters       = 'abcdefghijklmnopqrstuvwxyz';
    $charactersLength = strlen($characters);
    $randomString     = '';
    for ($i = 0 ; $i < $length ; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}

$c = new Controller();

$name     = generateRandomString(6);
$linkName = $name.'_by_MakeStickersWithRandyBot';

$return = $c->call(
    "{$c->apiUrl}/{$c->botKey}/createnewstickerset",
    [
        'user_id'     => $c->userID,
        'name'        => $linkName,//英文開頭 _by_MakeStickersWithRandyBot 結尾 、 shardLink
        'title'       => $name,//showName
        'png_sticker' => curl_file_create(realpath('./tgImage/tg_157778398.png.png')), //firstSticker
        'emojis'      => '😀',
    ], "POST"
);
echo PHP_EOL.$return.PHP_EOL;

echo "https://t.me/addstickers/".$linkName;
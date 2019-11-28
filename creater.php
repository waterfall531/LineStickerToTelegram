<?php
include __DIR__ . DIRECTORY_SEPARATOR . 'Controller.php';
$c = new Controller();

$return = $c->call(
    "{$c->apiUrl}/{$c->botKey}/createnewstickerset",
    [
        'user_id'=>$c->userID,
        'name' => $c->stickerPackName,
        'title' => $c->stickerPackName,
        'png_sticker' => $c->mainURL.'tgImage/tg_157778390.png.png',
        'emojis' => '😀',
    ],"POST"
);
echo PHP_EOL.$return.PHP_EOL;
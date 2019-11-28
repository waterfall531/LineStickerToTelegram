<?php
require __DIR__.'/vendor/autoload.php';

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

class Maker {
    private $bot_api_key = '687866318:AAGe4zkO---677eXnFtctP2Dg_dfvQxItnc';
    private $bot_username = 'MakeStickersWithRandyBot';
    private $chatId = 311768984;
    private $selfId = 311768984;
    private $fileLocation = '/tgImage';

    function __construct($type = 'resize') {
        echo 'Start Get Image<br>';
        if (is_numeric($type)) {
            $this->getImage();
            $list = $this->resizeImage();
            $this->send($list);
        }

        if ($type == 'resize') {
            $list = $this->resizeImage();
            $this->send($list);
        }

        if ($type == 'send') {
            $list = $this->getFileList();
            $this->send($list);
        }
    }

    function getImage() {
        echo 'Download~';

        $url  = $_GET['id_s'];
        $url2 = $_GET['id_e'];
        if (count($_GET) < 2) {
            echo 'error of id_s or id_e<br>';
            exit();
        }
        $staticUrl    = 'https://stickershop.line-scdn.net/stickershop/v1/sticker/';
        $staticFooter = '/iPhone/sticker@2x.png';
        $shell        = 'cd tgImage;';

        //curl -o "#1.png" "https://stickershop.line-scdn.net/stickershop/v1/sticker/[249916390-249916429]/iPhone/sticker@2x.png
        //https://stickershop.line-scdn.net/stickershop/v1/sticker/249916390/iPhone/sticker@2x.png;compress=true
        //curl -o "#1.png" "https://stickershop.line-scdn.net/stickershop/v1/sticker/[244231117-244231494]/android/sticker_key.png"
        if (!isset($type)) {
            //type 1
            $shell .= 'curl -o "#1.png" "'.$staticUrl.'['.$url.'-'.$url2.']'.$staticFooter.'"';
        } else {
            //type 2
            $shell .= 'for a in {'.$url.'..'.$url2.'};';
            $shell .= 'do curl -o ${a}.png '.$staticUrl.'/${a}'.$staticFooter.' ;';
            $shell .= 'done';
        }
        echo shell_exec($shell);
    }

    function resizeImage() {
        echo 'Start Transform To png~<br>';

        $files1 = scandir($this->fileLocation);
        foreach ($files1 as $filename) {
            if (is_file($this->fileLocation.'/'.$filename) && $filename != '.DS_Store') {
                $shellCode = "cd tgImage;";
                $shellCode .= 'convert -resize 512x512 "'.$filename.'" '.'"tg_'.$filename.'.png";';

                $resizeFileList[] = 'tg_'.$filename.'.png';
                shell_exec($shellCode);
            }
        }

        return $resizeFileList;
    }

    function send($resizeFileList) {
        echo 'send to Tg <br>';
        try {
            $telegram = new Telegram($this->bot_api_key, $this->bot_username);

            $telegram->setUploadPath($this->fileLocation);

            $name      = 'lineCR'.date('YmdHis').rand(10, 99);
            $photoList = [];
            foreach ($resizeFileList as $fileName) {
                $photoList[] = '/tgImage/'.$fileName;
            }
            $emoji = "😀";

            echo "http://f2e.baifu-tech.net:8443/randy".$photoList[0];
            print_r($photoList,1);

            //call Api Create
            $respone = Request::createNewStickerSet([
                'user_id'     => $this->selfId,
                'name'        => $name.'bysWithRandyBot',
                'png_sticker' => "http://f2e.baifu-tech.net:8443/randy".$photoList[0],
                'emojis'      => $emoji,
            ]);

            /*
             * [
                'user_id' => $c->userID,
                'name' => $c->stickerPackName,
                'png_sticker' => "{$c->mainURL}/stickers/stickers/{$sticker['filename']}_{$rnd}.png",
                'emojis' => $sticker['emoji'][0],
            ]
             * */
            //call Api addstickertoset
            foreach ($photoList as $poto) {
                var_dump("http://f2e.baifu-tech.net:8443".$poto);
                $respone = Request::addstickertoset([
                    'user_id'     => $this->selfId,
                    'name'        => $name.'bysWithRandyBot',
                    'png_sticker' => "http://f2e.baifu-tech.net:8443".$poto,
                    'emojis'      => $emoji,
                ]);
            }
        } catch (Longman\TelegramBot\Exception\TelegramException $e) {
            var_dump($e);
        }
    }

    function getFileList() {
        $resizeFileList = [];
        $files1         = scandir($this->fileLocation);
        foreach ($files1 as $filename) {
            if (is_file($this->fileLocation.'/'.$filename) && $filename != '.DS_Store' && substr($filename, 0, 3) == 'tg_') {
                $resizeFileList[] = $filename;
            }
        }

        return $resizeFileList;
    }
}

$tmp = new Maker($_GET['id_s']);

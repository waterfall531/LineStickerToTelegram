<?php
require __DIR__.'/vendor/autoload.php';

use Longman\TelegramBot\Telegram;

class Maker {
    private $bot_api_key = '687866318:AAGe4zkO---677eXnFtctP2Dg_dfvQxItnc';
    private $bot_username = 'MakeStickersWithRandyBot';
    private $chatId = 311768984;
    private $selfId = 311768984;
    private $fileLocation = './tgImage';

    function __construct($type = 'resize') {
        echo "Start Get Image \r\n";
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
        echo "Download~ \r\n";

        $shellCode = "cd tgImage;";
        $shellCode .= "rm -rf *.png";
        shell_exec($shellCode);

        echo "Clean Up \r\n";

        $url  = $_GET['id_s'];
        $url2 = $_GET['id_e'];
        if (count($_GET) < 2) {
            echo "error of id_s or id_e\r\n";
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
        echo "Start Transform To png~\r\n";

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
        echo "send to Tg \r\n";
        try {
            $telegram = new Telegram($this->bot_api_key, $this->bot_username);

            $telegram->setUploadPath($this->fileLocation);

            $name      = $this->generateRandomString(6);
            $photoList = [];
            foreach ($resizeFileList as $fileName) {
                $photoList[] = './tgImage/'.$fileName;
            }

            $common = "curl --location --request POST 'https://api.telegram.org/bot687866318:AAGe4zkO---677eXnFtctP2Dg_dfvQxItnc/createnewstickerset' \\";
            $common .= "--form 'png_sticker=@{$photoList[0]}' \\";
            $common .= "--form 'name={$name}_by_MakeStickersWithRandyBot' \\";
            $common .= "--form 'user_id=311768984' \\";
            $common .= "--form 'title={$name}' \\";
            $common .= "--form 'emojis=😀'";
            echo $common."\r\n";
            sleep(1);
            system($common);
            echo "\r\n";

            $common = "curl --location --request POST 'https://api.telegram.org/bot687866318:AAGe4zkO---677eXnFtctP2Dg_dfvQxItnc/getStickerSet' \\";
            $common .= "--form 'name={$name}_by_MakeStickersWithRandyBot' \\";
            echo $common."\r\n";
            sleep(1);
            system($common);
            echo "\r\n";

            //addStickerToSet
            foreach ($photoList as $poto) {
                $common = "curl --location --request POST 'https://api.telegram.org/bot687866318:AAGe4zkO---677eXnFtctP2Dg_dfvQxItnc/addStickerToSet' \\";
                $common .= "--form 'png_sticker=@{$poto}' \\";
                $common .= "--form 'name={$name}' \\";
                $common .= "--form 'user_id=311768984' \\";
                $common .= "--form 'emojis=😀'";
                echo $common."\r\n";
                system($common);
                echo "\r\n";
                sleep(1);
            }
        } catch (Exception $e) {
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

    function generateRandomString($length = 10) {
        $characters       = 'abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString     = '';
        for ($i = 0 ; $i < $length ; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}

$tmp = new Maker('send');

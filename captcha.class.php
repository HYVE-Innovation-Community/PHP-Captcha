<?php

class Captcha {

    private $captchaDecoded;
    private $salt;
    public $captchaLength;
    public $width;
    public $height;
    public $developement = true;
    public $font;

    public function __construct($font = '', $length = 4, $height = 31, $width = 90) {
        $this->salt = substr(md5($_SERVER['REMOTE_ADDR']), 10, 0);
        $this->captchaLength = $length;
        $this->height = $height;
        $this->width = $width;
        if (!empty($font)) {
            $this->setFont($font);
        }
        if(isset($_SESSION['captcha-value'])){
            $this->captchaDecoded = $_SESSION['captcha-value'];
        }
    }

    public function generateImage($backgroundImage = '', $font = '') {
        if (!empty($font)) {
            $this->font = $this->setFont($font);
        }
        $error = false;
        $captchaPlain = $this->generateRandomCaptcha();
        $this->captchaDecoded = $_SESSION['captcha-value'] = $this->decodeCaptcha($captchaPlain);

        header("Cache-Control: no-cache, must-revalidate");
        ob_clean();
        ob_start();
        if (!empty($backgroundImage) && is_file($backgroundImage)) {
            $im = imagecreatefromjpeg($backgroundImage);
            $text_color = imagecolorallocate($im, 0, 0, 0);
        } else {
            $im = imagecreatetruecolor($this->width, $this->height);
            $text_color = imagecolorallocate($im, 233, 233, 233);
        }

        for ($i = 0; $i < $this->captchaLength; $i++) {
            if (!empty($this->font) && is_file($this->font)) {
                $font = $this->font;
            } else {
                $error = true;
                echo 'Font-File not found. || Font-Datei nicht gefunden.';
                break;
            }
            $font_size = 20;
            //\\
            $random_x = mt_rand(10, 20) + (20 * $i) - 10;
            $random_y = mt_rand(21, 31);
            $random_angle = mt_rand(-20, 20);
            
            imagettftext($im, $font_size, $random_angle, $random_x, $random_y, $text_color, $font, $captchaPlain[$i]);
        }

        if (!$error) {
            imagejpeg($im);
            imagedestroy($im);
        }

        $imageCode = ob_get_clean();

        if (!strstr(strtolower($imageCode), 'warning') && !$this->developement)
            header('Content-type: image/png');

        echo $imageCode;
    }

    public function compareCaptcha($enteredCaptcha) {
        return ($this->decodeCaptcha(strtoupper($enteredCaptcha)) == $this->captchaDecoded);
    }

    private function generateRandomCaptcha() {
        return strtoupper(substr(
                                md5(microtime()), rand(0, intval(32 - $this->captchaLength)), $this->captchaLength
                        ));
    }

    private function decodeCaptcha($captcha) {
        return sha1($captcha . $this->salt);
    }

    private function setFont($fontFile) {
        if (!empty($fontFile)) {
            if (substr($fontFile, 0, 1) != '/') {
                $fontFile = dirname($_SERVER['SCRIPT_FILENAME']) . '/' . $fontFile;
            }
            $this->font = $fontFile;
        }
    }

}

?>
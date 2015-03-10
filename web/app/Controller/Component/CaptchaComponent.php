<?php
App::uses('Component', 'Controller');
class CaptchaComponent extends Component {

    /***
     * Captcha Component class
     *
     *
     * PHP versions 5.1.4
     * @author     Arvind K Thakur
     * @link       http://www.smartdatainc.net/
     * @copyright  Copyright � 2009 Smartdata
     * @version 0.0.1
     *   - Initial release
     */

    public function initialize(Controller $controller, $settings = array()) {
        $this->controller = $controller;
    }

    public function create() {

        $alphabet = '0123456789abcdefghijklmnopqrstuvwxyz'; # do not change without changing font files!

        # symbols used to draw CAPTCHA
        //$allowedSymbols = '0123456789'; #digits
        $allowedSymbols = '2345679abcdehkmnpqsuvxyz'; #alphabet without similar symbols (o=0, 1=l, i=j, t=f)

        # folder with fonts
        $fontsdir = 'fonts';

        # CAPTCHA string length
        $length = mt_rand(5, 6); # random 5 or 6
        //$length = 6;

        # CAPTCHA image size (you do not need to change it, whis parameters is optimal)
        $width = 200;
        $height = 58;

        # symbol's vertical fluctuation amplitude divided by 2
        $fluctuationAmplitude = 6;

        # increase safety by prevention of spaces between symbols
        $noSpaces = false;

        # show credits
        $showCredits = true; # set to false to remove credits line. Credits adds 12 pixels to image height
        $credits = 'GH Manager'; # if empty, HTTP_HOST will be shown

        # CAPTCHA image colors (RGB, 0-255)
        //$foregroundColor = array(0, 0, 0);
        //$backgroundColor = array(220, 230, 255);
        $foregroundColor = array(mt_rand(0, 100), mt_rand(0, 100), mt_rand(0, 100));
        $backgroundColor = array(mt_rand(160, 255), mt_rand(160, 255), mt_rand(160, 255));

        //$foregroundColor = array(100, 100, 100);
        //$backgroundColor = array(255, 255, 220);

        # JPEG quality of CAPTCHA image (bigger is better quality, but larger file size)
        $jpegQuality = 85;

        $fontsdirAbsolute = $fontsdir;

        if ($handle = opendir($fontsdirAbsolute)) {
            while (false !== ($file = readdir($handle))) {
                if (preg_match('/\.png$/i', $file)) {
                    $fonts[] = $fontsdirAbsolute . '/' . $file;
                }
            }
            closedir($handle);
        }

        //pr($fonts);die;

        $alphabetLength = strlen($alphabet);

        while (true) {
            // generating random keystring
            while (true) {
                $keystring = '';
                for ($i = 0; $i < $length; $i++) {
                    $keystring .= $allowedSymbols{mt_rand(0, strlen($allowedSymbols) - 1)};
                }
                if (!preg_match('/cp|cb|ck|c6|c9|rn|rm|mm|co|do|cl|db|qp|qb|dp/', $keystring)) {
                    break;
                }

            }

            $fontFile = $fonts[mt_rand(0, count($fonts) - 1)];
            $font = imagecreatefrompng($fontFile);
            imagealphablending($font, true);
            $fontfileWidth = imagesx($font);
            $fontfileHeight = imagesy($font) - 1;
            $fontMetrics = array();
            $symbol = 0;
            $readingSymbol = false;

            // loading font
            for ($i = 0; $i < $fontfileWidth && $symbol < $alphabetLength; $i++) {
                $transparent = (imagecolorat($font, $i, 0) >> 24) == 127;

                if (!$readingSymbol && !$transparent) {
                    $fontMetrics[$alphabet{$symbol}] = array('start' => $i);
                    $readingSymbol = true;
                    continue;
                }

                if ($readingSymbol && $transparent) {
                    $fontMetrics[$alphabet{$symbol}]['end'] = $i;
                    $readingSymbol = false;
                    $symbol++;
                    continue;
                }
            }

            $img = imagecreatetruecolor($width, $height);
            imagealphablending($img, true);
            $white = imagecolorallocate($img, 255, 255, 255);
            $black = imagecolorallocate($img, 0, 0, 0);

            imagefilledrectangle($img, 0, 0, $width - 1, $height - 1, $white);

            // draw text
            $x = 1;
            for ($i = 0; $i < $length; $i++) {
                $m = $fontMetrics[$keystring{$i}];

                $y = mt_rand(-$fluctuationAmplitude, $fluctuationAmplitude) + ($height - $fontfileHeight) / 2 + 2;

                if ($noSpaces) {
                    $shift = 0;
                    if ($i > 0) {
                        $shift = 1000;
                        for ($sy = 7; $sy < $fontfileHeight - 20; $sy += 1) {
                            //for ($sx=$m['start']-1;$sx<$m['end'];$sx+=1) {
                            for ($sx = $m['start'] - 1; $sx < $m['end']; $sx += 1) {
                                $rgb = imagecolorat($font, $sx, $sy);
                                $opacity = $rgb >> 24;
                                if ($opacity < 127) {
                                    $left = $sx - $m['start'] + $x;
                                    $py = $sy + $y;
                                    if ($py > $height) {
                                        break;
                                    }

                                    for ($px = min($left, $width - 1); $px > $left - 12 && $px >= 0; $px -= 1) {
                                        $color = imagecolorat($img, $px, $py) & 0xff;
                                        if ($color + $opacity < 190) {
                                            if ($shift > $left - $px) {
                                                $shift = $left - $px;
                                            }
                                            break;
                                        }
                                    }
                                    break;
                                }
                            }
                        }
                        if ($shift == 1000) {
                            $shift = mt_rand(4, 6);
                        }

                    }
                } else {
                    $shift = -10;
                }
                imagecopy($img, $font, $x - $shift, $y, $m['start'], 1, $m['end'] - $m['start'], $fontfileHeight);
                $x += $m['end'] - $m['start'] - $shift;
            }
            if ($x < $width - 10) {
                break;
            }
            // fit in canvas

        }
        $center = $x / 2;

        // credits. To remove, see configuration file
        $img2 = imagecreatetruecolor($width, $height + ($showCredits ? 12 : 0));
        $foreground = imagecolorallocate($img2, $foregroundColor[0], $foregroundColor[1], $foregroundColor[2]);
        $background = imagecolorallocate($img2, $backgroundColor[0], $backgroundColor[1], $backgroundColor[2]);
        imagefilledrectangle($img2, 0, $height, $width - 1, $height + 12, $foreground);
        $credits = empty($credits) ? $_SERVER['HTTP_HOST'] : $credits;
        imagestring($img2, 2, $width / 2 - ImageFontWidth(2) * strlen($credits) / 2, $height - 2, $credits, $background);

        // periods
        $rand1 = mt_rand(750000, 1200000) / 10000000;
        $rand2 = mt_rand(750000, 1200000) / 10000000;
        $rand3 = mt_rand(750000, 1200000) / 10000000;
        $rand4 = mt_rand(750000, 1200000) / 10000000;
        // phases
        $rand5 = mt_rand(0, 3141592) / 500000;
        $rand6 = mt_rand(0, 3141592) / 500000;
        $rand7 = mt_rand(0, 3141592) / 500000;
        $rand8 = mt_rand(0, 3141592) / 500000;
        // amplitudes
        $rand9 = mt_rand(330, 420) / 110;
        $rand10 = mt_rand(330, 450) / 110;

        //wave distortion
        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $sx = $x + (sin($x * $rand1 + $rand5) + sin($y * $rand3 + $rand6)) * $rand9 - $width / 2 + $center + 1;
                $sy = $y + (sin($x * $rand2 + $rand7) + sin($y * $rand4 + $rand8)) * $rand10;

                if ($sx < 0 || $sy < 0 || $sx >= $width - 1 || $sy >= $height - 1) {
                    $color = 255;
                    $color_x = 255;
                    $color_y = 255;
                    $color_xy = 255;
                } else {
                    $color = imagecolorat($img, $sx, $sy) & 0xFF;
                    $color_x = imagecolorat($img, $sx + 1, $sy) & 0xFF;
                    $color_y = imagecolorat($img, $sx, $sy + 1) & 0xFF;
                    $color_xy = imagecolorat($img, $sx + 1, $sy + 1) & 0xFF;
                }

                if ($color == 0 && $color_x == 0 && $color_y == 0 && $color_xy == 0) {
                    $newred = $foregroundColor[0];
                    $newgreen = $foregroundColor[1];
                    $newblue = $foregroundColor[2];
                } elseif ($color == 255 && $color_x == 255 && $color_y == 255 && $color_xy == 255) {
                    $newred = $backgroundColor[0];
                    $newgreen = $backgroundColor[1];
                    $newblue = $backgroundColor[2];
                } else {
                    $frsx = $sx - floor($sx);
                    $frsy = $sy - floor($sy);
                    $frsx1 = 1 - $frsx;
                    $frsy1 = 1 - $frsy;

                    $newcolor = (
                        $color * $frsx1 * $frsy1 +
                        $color_x * $frsx * $frsy1 +
                        $color_y * $frsx1 * $frsy +
                        $color_xy * $frsx * $frsy);

                    if ($newcolor > 255) {
                        $newcolor = 255;
                    }

                    $newcolor = $newcolor / 255;
                    $newcolor0 = 1 - $newcolor;

                    $newred = $newcolor0 * $foregroundColor[0] + $newcolor * $backgroundColor[0];
                    $newgreen = $newcolor0 * $foregroundColor[1] + $newcolor * $backgroundColor[1];
                    $newblue = $newcolor0 * $foregroundColor[2] + $newcolor * $backgroundColor[2];
                }

                imagesetpixel($img2, $x, $y, imagecolorallocate($img2, $newred, $newgreen, $newblue));
            }
        }

        $this->controller->Session->write('ver_code', $keystring);
        ob_start();
        if (function_exists("imagejpeg")) {
//             header("Content-Type: image/jpeg");
            imagejpeg($img2, null, $jpegQuality);
        } elseif (function_exists("imagegif")) {
//             header("Content-Type: image/gif");
            imagegif($img2);
        } elseif (function_exists("imagepng")) {
//             header("Content-Type: image/x-png");
            imagepng($img2);
        }
        $image = ob_get_clean();

        $filenamePrefix = md5($this->genPass() . $this->RandPass());
        $C_file = $filenamePrefix . '.jpg';

        if ($this->controller->Session->check('oldcaptcha')) {
            $oldcaptcha = $this->controller->Session->read('oldcaptcha');
        } else {
            $oldcaptcha = NULL;
        }

        if (isset($oldcaptcha)) {
            if (file_exists('img/captcha/' . $oldcaptcha)) {
                unlink('img/captcha/' . $oldcaptcha);
            }
        }

        clearstatcache();
        $fh = @fopen("img/captcha/" . $C_file, "w+");
        @chmod("img/captcha/" . $C_file, 0777);
        @fwrite($fh, $image);
        $this->controller->Session->write('oldcaptcha', $C_file);

        fclose($fh);

        return $C_file;
    }

    public function genPass() {

        $vocales = 'AaEeIiOoUu13580';

        $consonantes = 'BbCcDdFfGgHhJjKkLlMmNnPpQqRrSsTtVvWwXxYyZz24679';
        $r = '';
        for ($i = 0; $i < 4; $i++) {
            if ($i % 2) {
                $r .= $vocales{rand(0, strlen($vocales) - 1)};
            } else {
                $r .= $consonantes{rand(0, strlen($consonantes) - 1)};
            }
        }
        return $r;
    }

    public function RandPass() {

        $randomPassword = '';

        for ($i = 0; $i < 5; $i++) {

            $randnumber = mt_rand(48, 120);

            while (($randnumber >= 58 && $randnumber <= 64) || ($randnumber >= 91 && $randnumber <= 96)) {
                $randnumber = mt_rand(48, 120);
            }

            $randomPassword .= chr($randnumber);
        }
        return $randomPassword;
    }

}
?>

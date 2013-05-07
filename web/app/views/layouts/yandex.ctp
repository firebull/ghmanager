<?php
/*
 * Created on 22.12.2010
 *
 * Made for project TeamServer(Git)
 * by bulaev 2005-01-21T13:20:10+03:00
 */
 // Дебильный яндкес хочет кодировку 1251, потому
 // ставлю дебильный костыль, мля!!!
 // За функцию эту спасибо http://ru2.php.net/manual/en/function.convert-cyr-string.php
 function Utf8ToWin($fcontents) {
    $out = $c1 = '';
    $byte2 = false;
    for ($c = 0;$c < strlen($fcontents);$c++) {
        $i = ord($fcontents[$c]);
        if ($i <= 127) {
            $out .= $fcontents[$c];
        }
        if ($byte2) {
            $new_c2 = ($c1 & 3) * 64 + ($i & 63);
            $new_c1 = ($c1 >> 2) & 5;
            $new_i = $new_c1 * 256 + $new_c2;
            if ($new_i == 1025) {
                $out_i = 168;
            } else {
                if ($new_i == 1105) {
                    $out_i = 184;
                } else {
                    $out_i = $new_i - 848;
                }
            }
            // UKRAINIAN fix
            switch ($out_i){
                case 262: $out_i=179;break;// і
                case 182: $out_i=178;break;// І 
                case 260: $out_i=186;break;// є
                case 180: $out_i=170;break;// Є
                case 263: $out_i=191;break;// ї
                case 183: $out_i=175;break;// Ї
                case 321: $out_i=180;break;// ґ
                case 320: $out_i=165;break;// Ґ
            }
            $out .= chr($out_i);
            
            $byte2 = false;
        }
        if ( ( $i >> 5) == 6) {
            $c1 = $i;
            $byte2 = true;
        }
    }
    return $out;
}
 
?>
<?xml version = "1.0" encoding="windows-1251"?>
<response performedDatetime="<?php echo date('Y-m-d\TH:i:sP')?>">
	<?php echo Utf8ToWin($content_for_layout); ?>
</response>

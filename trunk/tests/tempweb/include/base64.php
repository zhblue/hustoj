<?php
/**
* base64编码
* @author :xia0ji233 
*/
function decode64($input) {
    $keyStr = "3WiPZ+xr/yKf5OdQ6UATHYItebL8B0njk192cJRNagGm7hoECvFVpqw4DsMlzuXS=";//换表
    $output = "";
    $chr1="";
    $chr2="";
    $chr3="";
    $enc1=$enc2=$enc3=$enc4="";
    $i = 0;
    if (strlen($input) % 4 != 0) {
        return "";
    }
    $len=strlen($input);
    do {
        $enc1 = strpos($keyStr,$input[$i++]);
        $enc2 = strpos($keyStr,$input[$i++]);
        $enc3 = strpos($keyStr,$input[$i++]);
        $enc4 = strpos($keyStr,$input[$i++]);
        $chr1 = ($enc1 << 2) | ($enc2 >> 4);
        $chr2 = (($enc2 & 15) << 4) | ($enc3 >> 2);
        $chr3 = (($enc3 & 3) << 6) | $enc4;
        $output = $output.chr($chr1);

        if ($enc3 != 64) {
            $output .= chr($chr2);
        }
        if ($enc4 != 64) {
            $output .= chr($chr3);
        }
        $chr1 = $chr2 = $chr3 = "";
        $enc1 = $enc2 = $enc3 = $enc4 = "";
    } while ($i < $len);
    return $output;
}
?>

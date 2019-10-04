<?php
class simple_form {

    function filter($data) { // step2...
        $data = strip_tags($data);
        $data = trim(htmlentities($data, ENT_QUOTES, "UTF-8"));
        if (get_magic_quotes_gpc())
            $data = stripslashes($data);
        return $data;
    }

    function checkMail($str) {
        $mailaddress_array = explode('@', $str);
        if (preg_match("/^[\.!#%&\-_0-9a-zA-Z\?\/\+]+\@[!#%&\-_0-9a-z]+(\.[!#%&\-_0-9a-z]+)+$/", "$str") && count($mailaddress_array) == 2) {
            return true;
        } else {
            return false;
        }
    }

    function h($string) {
        global $encode;
        return htmlspecialchars($string, ENT_QUOTES, $encode);
    }

    function sanitize($arr) {
        if (is_array($arr)) {
            return array_map('sanitize', $arr);
        }
        return str_replace("\0", "", $arr);
    }

    /** Shift-JISの場合に誤変換文字の置換関数  * */
    function sjisReplace($arr, $encode) {
        foreach ($arr as $key => $val) {
            $key = str_replace('＼', 'ー', $key);
            $resArray[$key] = $val;
        }
        return $resArray;
    }

    /** 送信メールにPOSTデータをセットする関数  ** */
    function postToMail($arr, $Lable_key) {
        $resArray = '';
        foreach ($arr as $key => $val) {
            $out = '';
            if (is_array($val)) {
                foreach ($val as $item) {
                    $out .= $item . ', ';
                }
                $out = rtrim($out, ', ');
            } else {
                $out = $val;
            }
            if (get_magic_quotes_gpc()) {
                $out = stripslashes($out);
            }
            if ($out != "confirm_submit" && $key != "httpReferer") {
                //if (!empty($Lable_key[$key])) {
                    $resArray .= "【 " . $Lable_key[$key] . " 】\n" . $out . "\n\n";
                //}
            }
        }
        return $resArray;
    }

    /** 送信メールにPOSTデータをセットする関数  ** */
    function postToMail2($arr, $Lable_key) {
        $resArray = '';
        foreach ($Lable_key as $key => $val) {
            $out = $arr[$key];
            if (get_magic_quotes_gpc()) {
                $out = stripslashes($out);
            }
            //if ($out != "") {
                $resArray .= "【 " . $val . " 】 "."\n". $out . "\n\n";
            //}
        }
        return $resArray;
    }

    /*   * 確認画面の入力内容出力用関数  * */

    function confirmOutput($arr) {
        $html = '';
        foreach ($arr as $key => $val) {
            $out = '';
            if (is_array($val)) {
                foreach ($val as $item) {
                    $out .= $item . ', ';
                }
                $out = rtrim($out, ', ');
            } else {
                $out = $val;
            }//チェックボックス（配列）追記ここまで
            if (get_magic_quotes_gpc()) {
                $out = stripslashes($out);
            }
            $out = nl2br(h($out)); //※追記 改行コードを<br>タグに変換
            $key = h($key);

            $html .= "<tr><th>" . $key . "</th><td>" . $out;
            $html .= '<input type="hidden" name="' . $key . '" value="' . str_replace(array("<br />", "<br>"), "", $out) . '" />';
            $html .= "</td></tr>\n";
        }
        return $html;
    }

    /** 管理者宛送信メールヘッダ * */
    function adminHeader($userMail, $post_mail, $BccMail, $to) {
        $header = '';
        if ($userMail == 1 && !empty($post_mail)) {
            $header = "From: $post_mail\n";
            if ($BccMail != '') {
                $header.="Bcc: $BccMail\n";
            }
            $header.="Reply-To: " . $post_mail . "\n";
        } else {
            if ($BccMail != '') {
                $header = "Bcc: $BccMail\n";
            }
            $header.="Reply-To: " . $to . "\n";
        }
        $header.="Content-Type:text/plain;charset=UTF-8\nX-Mailer: PHP/" . phpversion();
        return $header;
    }

    /* 管理者宛送信メールボディ */

    function mailToAdmin($arr, $subject, $mailFooterDsp, $mailSignature, $encode, $confirmDsp, $Lable_key) {
        $adminBody= $subject . "\n\n";   
		//$adminBody.= "－－－－－－－－－－ \n";		
        $adminBody.= $this->postToMail2($arr, $Lable_key); //POSTデータを関数からセット      
        //$adminBody.="送信された日時：" . date("Y/m/d (D) H:i:s", time()) . "\n";
        if ($confirmDsp == 1) {
            $adminBody.="送信者のIPアドレス：" . @$_SERVER["REMOTE_ADDR"] . "\n";
            $adminBody.="送信者のホスト名：" . getHostByAddr(getenv('REMOTE_ADDR')) . "\n";
            $adminBody.="問い合わせのページURL：" . @$_SERVER['HTTP_REFERER'] . "\n";
        }

        if ($mailFooterDsp == 1)
            $adminBody.= $mailSignature;
        return $adminBody;
        //return mb_convert_encoding($adminBody,"JIS",$encode);
    }

    /*   * ユーザ宛送信メールヘッダ  * */

    function userHeader($refrom_name, $to, $encode) {
        $reheader = "From: ";
        if (!empty($refrom_name)) {
            $default_internal_encode = mb_internal_encoding();
            if ($default_internal_encode != $encode) {
                mb_internal_encoding($encode);
            }
            $reheader .= mb_encode_mimeheader($refrom_name) . " <" . $to . ">\nReply-To: " . $to;
        } else {
            $reheader .= "$to\nReply-To: " . $to;
        }
        $reheader .= "\nContent-Type: text/plain;charset=UTF-8\nX-Mailer: PHP/" . phpversion();
        return $reheader;
    }

    /*   * ユーザ宛送信メールボディ * */

    function mailToUser($arr, $dsp_name, $remail_text, $mailFooterDsp, $mailSignature, $encode, $Lable_key) {
        $userBody = '';
        if (isset($arr[$dsp_name]))
            $userBody = $this->h($arr[$dsp_name]) . " 様\n";
        $userBody.= $remail_text;       
        $userBody.= $this->postToMail2($arr, $Lable_key); //POSTデータを関数からセット
        //$userBody.="送信日時：" . date("Y/m/d (D) H:i:s", time()) . "\n";
        if ($mailFooterDsp == 1)
            $userBody.= $mailSignature;

        return $userBody;
        //return mb_convert_encoding($userBody,"JIS",$encode);
    }

    /** 必須チェック関数  */
    function requireCheck($require) { 
        $res['errm'] = '';
        $res['empty_flag'] = 0;
        foreach ($require as $key_r => $requireVal) {
            $existsFalg = '';
            foreach ($_POST as $key => $val) {
                if ($key == $key_r && empty($val)) {
                    $res['errm'] .= "<p style='color: red;' class=\"error_messe\">【" . $requireVal . "】は必須入力項目です。</p>\n";
                    $res['empty_flag'] = 1;
                    $existsFalg = 1;
                    break;
                } elseif ($key_r == $key) {
                    $existsFalg = 1;
                    break;
                }
            }
            if ($existsFalg != 1) {
                $res['errm'] .= "<p style='color: red;' class=\"error_messe\">【" . $requireVal . "】が未選択です。</p>\n";
                $res['empty_flag'] = 1;
            }
        }

        return $res; //連想配列で値を返す
    }

    /** リファラチェック  */
    function refererCheck($Referer_check, $Referer_check_domain) {
        if ($Referer_check == 1 && !empty($Referer_check_domain)) {
            if (strpos($_SERVER['HTTP_REFERER'], $Referer_check_domain) === false) {
                return exit('<p align="center">リファラチェックエラー。フォームページのドメインとこのファイルのドメインが一致しません</p>');
            }
        }
    }
	
	function checked($pattern, $value) {
		if(in_array($value,$pattern))
			return true;
		return false;
	}
}

?>
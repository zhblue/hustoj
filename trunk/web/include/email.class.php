<?php
class smtp {
    /* Public Variables */
    var $smtp_port;
    var $time_out;
    var $host_name;
    var $log_file;
    var $relay_host;
    var $debug;
    var $auth;
    var $user;
    var $pass;
    /* Private Variables */
    var $sock;
    /* Constractor */
    function __construct($relay_host = "", $smtp_port = 25, $auth = false, $user, $pass) {
        $this->debug = FALSE;
        $this->smtp_port = $smtp_port;
        $this->relay_host = $relay_host;
        $this->time_out = 30; //is used in fsockopen()
        #
        $this->auth = $auth; //auth
        $this->user = $user;
        $this->pass = $pass;
        #
        $this->host_name = "HUSTOJ"; //is used in HELO command
        $this->log_file = ""; //"/home/judge/log/mail.log";
        $this->sock = FALSE;
    }
    /* Main Function */
    function sendmail($to, $from, $subject = "", $body = "", $mailtype, $cc = "", $bcc = "", $additional_headers = "") {
        $mail_from = $this->get_address($this->strip_comment($from));
        $body = preg_replace("/(^|(\r\n))(\.)/", "\1.\3", $body);
        $header = "MIME-Version:1.0\r\n";
        if ($mailtype == "HTML") {
            $header.= "Content-Type:text/html\r\n";
        }
        $header.= "To: " . $to . "\r\n";
        if ($cc != "") {
            $header.= "Cc: " . $cc . "\r\n";
        }
        $header.= "From: $from<" . $from . ">\r\n";
        $header.= "Subject: " . $subject . "\r\n";
        $header.= $additional_headers;
        $header.= "Date: " . date("r") . "\r\n";
        $header.= "X-Mailer: HUSTOJ password recovery\r\n";
        list($msec, $sec) = explode(" ", microtime());
        $header.= "Message-ID: <" . date("YmdHis", $sec) . "." . ($msec * 1000000) . "." . $mail_from . ">\r\n";
        $TO = explode(",", $this->strip_comment($to));
        if ($cc != "") {
            $TO = array_merge($TO, explode(",", $this->strip_comment($cc)));
        }
        if ($bcc != "") {
            $TO = array_merge($TO, explode(",", $this->strip_comment($bcc)));
        }
        $sent = TRUE;
        foreach ($TO as $rcpt_to) {
            $rcpt_to = $this->get_address($rcpt_to);
            if (!$this->smtp_sockopen($rcpt_to)) {
                $this->log_write("Error: Cannot send email to " . $rcpt_to . "\n");
                $sent = FALSE;
                continue;
            }
            if ($this->smtp_send($this->host_name, $mail_from, $rcpt_to, $header, $body)) {
                $this->log_write("E-mail has been sent to <" . $rcpt_to . ">\n");
            } else {
                $this->log_write("Error: Cannot send email to <" . $rcpt_to . ">\n");
                $sent = FALSE;
            }
            fclose($this->sock);
            $this->log_write("Disconnected from remote host\n");
        }
        return $sent;
    }
    /* Private Functions */
    function smtp_send($helo, $from, $to, $header, $body = "") {
        if (!$this->smtp_putcmd("HELO", $helo)) {
            return $this->smtp_error("sending HELO command");
        }
        if ($this->relay_host == "smtp.qq.com") $this->smtp_ok(); // QQ mail need this line
        #auth
        if ($this->auth) {
            if (!$this->smtp_putcmd("AUTH LOGIN", base64_encode($this->user))) {
                return $this->smtp_error("sending HELO command");
            }
            if (!$this->smtp_putcmd("", base64_encode($this->pass))) {
                return $this->smtp_error("sending HELO command");
            }
        }
        #
        if (!$this->smtp_putcmd("MAIL", "FROM:<" . $from . ">")) {
            return $this->smtp_error("sending MAIL FROM command");
        }
        if (!$this->smtp_putcmd("RCPT", "TO:<" . $to . ">")) {
            return $this->smtp_error("sending RCPT TO command");
        }
        if (!$this->smtp_putcmd("DATA")) {
            return $this->smtp_error("sending DATA command");
        }
        if (!$this->smtp_message($header, $body)) {
            return $this->smtp_error("sending message");
        }
        if (!$this->smtp_eom()) {
            return $this->smtp_error("sending <CR><LF>.<CR><LF> [EOM]");
        }
        if (!$this->smtp_putcmd("QUIT")) {
            return $this->smtp_error("sending QUIT command");
        }
        return TRUE;
    }
    function smtp_sockopen($address) {
        if ($this->relay_host == "") {
            return $this->smtp_sockopen_mx($address);
        } else {
            return $this->smtp_sockopen_relay();
        }
    }
    function smtp_sockopen_relay() {
        $this->log_write("Trying to " . $this->relay_host . ":" . $this->smtp_port . "\n");
        $this->sock = @fsockopen($this->relay_host, $this->smtp_port, $errno, $errstr, $this->time_out);
        if (!($this->sock && $this->smtp_ok())) {
            $this->log_write("Error: Cannot connenct to relay host " . $this->relay_host . "\n");
            $this->log_write("Error: " . $errstr . " (" . $errno . ")\n");
            return FALSE;
        }
        $this->log_write("Connected to relay host " . $this->relay_host . "\n");
        return TRUE;;
    }
    function smtp_sockopen_mx($address) {
        $domain = preg_replace("/^.+@([^@]+)$/", "\1", $address);
        if (!@getmxrr($domain, $MXHOSTS)) {
            $this->log_write("Error: Cannot resolve MX \"" . $domain . "\"\n");
            return FALSE;
        }
        foreach ($MXHOSTS as $host) {
            $this->log_write("Trying to " . $host . ":" . $this->smtp_port . "\n");
            $this->sock = @fsockopen($host, $this->smtp_port, $errno, $errstr, $this->time_out);
            if (!($this->sock && $this->smtp_ok())) {
                $this->log_write("Warning: Cannot connect to mx host " . $host . "\n");
                $this->log_write("Error: " . $errstr . " (" . $errno . ")\n");
                continue;
            }
            $this->log_write("Connected to mx host " . $host . "\n");
            return TRUE;
        }
        $this->log_write("Error: Cannot connect to any mx hosts (" . implode(", ", $MXHOSTS) . ")\n");
        return FALSE;
    }
    function smtp_message($header, $body) {
        fputs($this->sock, $header . "\r\n" . $body);
        $this->smtp_debug("> " . str_replace("\r\n", "\n" . "> ", $header . "\n> " . $body . "\n> "));
        return TRUE;
    }
    function smtp_eom() {
        fputs($this->sock, "\r\n.\r\n");
        $this->smtp_debug(". [EOM]\n");
        return $this->smtp_ok();
    }
    function smtp_ok() {
        $response = str_replace("\r\n", "", fgets($this->sock, 512)); /// "socket_read" maybe better than "fgets" here , some smtp server give more lines like QQ mail
        $this->log_write("Info  : Remote host returned \"" . $response . "\"\n");
        $this->smtp_debug($response . "\n");
        if (!preg_match("/^[23]/", $response)) {
            fputs($this->sock, "QUIT\r\n");
            fgets($this->sock, 512);
            $this->log_write("Error: Remote host returned \"" . $response . "\"\n");
            return FALSE;
        }
        return TRUE;
    }
    function smtp_putcmd($cmd, $arg = "") {
        if ($arg != "") {
            if ($cmd == "") $cmd = $arg;
            else $cmd = $cmd . " " . $arg;
        }
        fputs($this->sock, $cmd . "\r\n");
        $this->smtp_debug("> " . $cmd . "\n");
        return $this->smtp_ok();
    }
    function smtp_error($string) {
        $this->log_write("Error: Error occurred while " . $string . ".\n");
        return FALSE;
    }
    function log_write($message) {
        $this->smtp_debug($message);
        if ($this->log_file == "") {
            return TRUE;
        }
        $message = date("M d H:i:s ") . get_current_user() . "[" . getmypid() . "]: " . $message;
        if (!@file_exists($this->log_file) || !($fp = @fopen($this->log_file, "a"))) {
            $this->smtp_debug("Warning: Cannot open log file \"" . $this->log_file . "\"\n");
            return FALSE;;
        }
        flock($fp, LOCK_EX);
        fputs($fp, $message);
        fclose($fp);
        return TRUE;
    }
    /**
     * @param $address
     * @return mixed
     */
    function strip_comment($address) {
        $comment = "/\([^()]*\)/";
        while (preg_match($comment, $address)) {
            $address = preg_replace($comment, "", $address);
        }
        return $address;
    }
    function get_address($address) {
        $address = preg_replace("/([ \t\r\n])+/", "", $address);
        $address = preg_replace("/^.*<(.+)>.*$/", "\1", $address);
        return $address;
    }
    function smtp_debug($message) {
        if ($this->debug) {
            echo $message;
        }
    }
}
function email($address,$mailtitle,$mailcontent){

        //******************** 配置信息 ********************************
        return false;   //确认下面的账户信息配置正确后，注释本行，否则mail相关功能不会生效。
        $smtpserver = "smtp.qiye.aliyun.com";           //SMTP服务器，通常在邮箱的smtp/pop3设置中可以查询到，推荐用企业邮箱发信，避免被识别为垃圾邮件
        $smtpserverport =25;                           //SMTP服务器端口，通常是25，有的服务器支持80、465以适应不同的网络防火墙配置
        $smtpusermail = "mailer@yourdomain.com";      //SMTP服务器的用户邮箱
        $smtppass = "your_smpt_auth_password";       //SMTP服务器的用户密码或者由邮箱系统生成的口令
        //通常只需修改上面的4个设置。
        $smtpuser = "$smtpusermail";           //SMTP服务器的用户帐号,通常就是邮箱，个别情况服务器支持一个账号多个邮箱地址的可能不同。
        $smtpemailto =$address;               //发送给谁
        $mailtype = "TXT";//邮件格式（HTML/TXT）,TXT为文本邮件
        //************************ 配置信息 ****************************
        $smtp = new smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);//这里面的一个true是表示使用身份验证,否则不使用身份验证.
        $smtp->debug =false;                 //是否显示发送的调试信息，发信界面卡住，发信失败，可打开进行调试
        //未经配置的系统，跳过发信步骤。
        if( $smtpusermail != "mailer@yourdomain.com") $state = $smtp->sendmail($smtpemailto, $smtpusermail, $mailtitle, $mailcontent, $mailtype);
}

?>

<?php
class Logger
{
    private $user;
    private $logfile;
    private $datetime_format;
    private $enabled;
    private $pid_enabled;
    private $user_enabled;
    private $url_enabled;
    private $url_host_enabled;
    private $url_param_enabled;
    private $trace_enabled;
    private $trace_id;

    public function __construct(
        $user,
        $logfile,
        $datetime_format,
        $enabled,
        $pid_enabled,
        $user_enabled,
        $url_enabled,
        $url_host_enabled,
        $url_param_enabled,
        $trace_enabled
    ) {
        $this->user = $user;
        $this->logfile = $logfile;
        $this->datetime_format = $datetime_format;
        $this->enabled = $enabled;
        $this->pid_enabled = $pid_enabled;
        $this->user_enabled = $user_enabled;
        $this->url_enabled = $url_enabled;
        $this->url_host_enabled = $url_host_enabled;
        $this->url_param_enabled = $url_param_enabled;
        $this->trace_enabled = $trace_enabled;
        if ($this->trace_enabled)
            $this->trace_id = uniqid();
    }

    public function info($message = "", array $data = [])
    {
        $this->delegrate_logging("info", $message, $data);
    }

    public function warn($message = "", array $data = [])
    {
        $this->delegrate_logging("warn", $message, $data);
    }

    protected function delegrate_logging($level, $message = "", array $data = [])
    {
        if ($this->enabled) {
            $this->logging($level, $message, $data);
        }
    }

    public function logging($level, $message = "", array $data = [])
    {
        global $ip;
        $datetime = new DateTime();
        $datetime = $datetime->format($this->datetime_format);
        $user = $this->user;
        $trace_id = $this->trace_id;
        $prefix = "$datetime $level ";
        if ($this->pid_enabled) {
            $pid = getmypid();
            $prefix = $prefix . "$pid ";
        }
        if ($this->user_enabled)
            $prefix = $prefix . "[$user @ $ip] ";
        if ($this->trace_enabled)
            $prefix = $prefix . "[$trace_id] ";
        if ($this->url_enabled) {
            $script   = $_SERVER['SCRIPT_NAME'];
            $url      =  $script;
            if ($this->url_host_enabled) {
                $protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']), 'https') === FALSE ? 'http' : 'https';
                $host     = $_SERVER['HTTP_HOST'];
                $url      = $protocol . '://' . $host . $url;
            }
            if ($this->url_param_enabled) {
                $params   = $_SERVER['QUERY_STRING'];
                if (!empty($params))
                    $url = $url . "?" . $params;
            }
            $prefix = $prefix . "$url ";
        }
        if (empty($message))
            $message = $prefix;
        else
            $message = "$prefix --- $message";
        foreach ($data as $key => $val)
            $message = str_replace("%{$key}%", $val, $message);
        $message .= PHP_EOL;
        $handle = fopen($this->logfile, "a");
        fwrite($handle, $message);
        fclose($handle);
    }
}

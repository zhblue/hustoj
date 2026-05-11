<?php
/**
 * openAI API 兼容模式，代理转发程序
 * 接收HUSTOJ的请求，添加合法API Key，转发给千问官方API或Hugging face等其他提供免费token的平台
 */
exit(0);
// 如需使用本功能，请自行完成鉴权操作,或修改文件名为仅自己知道的名称路径，注释前一行的exit(0);
require_once("../include/db_info.inc.php");
require_once("../include/my_func.inc.php");
class QwenProxy
{
    private $targetUrl = 'https://api.xiaomimimo.com/v1/chat/completions';    // 小米家的mimo 免费用到1.20
    private $apiKey = "创建新的API KEY"; //创建新的API KEY
    private $model="mimo-v2-flash";
    private $timeout = 30;
    private $requestBody="";
    private $response=""; 
    
    public function __construct(){
     
    }
    
    /**
     * 处理代理请求
     */
    public function handleProxyRequest()
    {
	    global $ip;
        try {
            // 记录原始请求信息（用于调试）
            $this->logRequest();
            $key=$_SERVER['AUTHORIZATION']??"";
	    if(!empty($key)&&!str_contains($key,"设置为阿里云的API-KEY")) $apiKey= ltrim($key, 'Bearer ');
            // 获取原始请求的方法和内容
            $method = $_SERVER['REQUEST_METHOD'];
            $contentType = $_SERVER['CONTENT_TYPE'] ?? 'application/json';
            $requestBody = file_get_contents('php://input');
            // 验证请求体
            if (empty($requestBody)) {
                $this->sendError('请求体为空');
                return;
            }
            
            // 解析JSON请求体（用于验证和日志）
            $requestData = json_decode($requestBody);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->sendError('无效的JSON请求体: ' . json_last_error_msg());
                return;
            }
            $requestData->model = $this->model ;  // 覆盖默认模型 
            // 构建转发请求头
            $headers = $this->buildForwardHeaders($contentType);
	    $requestBody=json_encode($requestData,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); 
            // 转发到千问API
            $response = $this->forwardToQwen($method, $headers, $requestBody);
            
            // 处理并返回响应
            $this->handleQwenResponse($response);
	    pdo_query("insert into ai_proxy_log(ip,request,response,referer) values(?,?,?,?)",$ip,$requestBody,$response['content'],$_SERVER['HTTP_REFERER']);
            
        } catch (Exception $e) {
            $this->sendError('代理处理错误: ' . $e->getMessage());
        }
    }
    
    /**
     * 构建转发请求头
     */
    private function buildForwardHeaders($contentType)
    {
        $headers = [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: ' . $contentType,
            'User-Agent: Qwen-Proxy/1.0',
            'Accept: application/json',
        ];
        
        // 可选：添加一些调试信息
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $headers[] = 'X-Forwarded-For: ' . $_SERVER['REMOTE_ADDR'];
        }
        if (isset($_SERVER['HTTP_REFERER'])) {
            $headers[] = 'X-Original-Referer: ' . $_SERVER['HTTP_REFERER'];
        }
        
        return $headers;
    }
    
    /**
     * 转发请求到千问API
     */
    private function forwardToQwen($method, $headers, $body)
    {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->targetUrl,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception('千问API请求失败: ' . $error);
        }
        
        curl_close($ch);
        
        return [
            'headers' => substr($response, 0, $headerSize),
            'content' => substr($response, $headerSize),
            'status_code' => $httpCode
        ];
    }
    
    /**
     * 处理千问API响应
     */
    private function handleQwenResponse($response)
    {
        // 设置HTTP状态码
        http_response_code($response['status_code']);
        
        // 解析并设置响应头
        $this->setResponseHeaders($response['headers']);
        
        // 添加代理标识头
        header('X-Proxy-Server: Qwen-Proxy');
        
        // 输出响应内容
        echo $response['content'];
        
        // 记录响应日志
        $this->logResponse($response);
    }
    
    /**
     * 设置响应头
     */
    private function setResponseHeaders($headerString)
    {
        $lines = explode("\r\n", $headerString);
        
        foreach ($lines as $line) {
            if (empty($line)) continue;
            
            // 跳过HTTP状态行
            if (strpos($line, 'HTTP/') === 0) continue;
            
            // 设置其他头
            if (strpos($line, ':') !== false) {
                list($key, $value) = explode(':', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // 跳过一些不需要转发的头
                $skipHeaders = ['Transfer-Encoding', 'Connection'];
                if (!in_array($key, $skipHeaders)) {
                    header("$key: $value");
                }
            }
        }
    }
    
    /**
     * 发送错误响应
     */
    private function sendError($message)
    {
        http_response_code(400);
        header('Content-Type: application/json');
        
        $errorResponse = [
            'error' => [
                'message' => $message,
                'type' => 'proxy_error',
                'code' => 'PROXY_ERROR'
            ]
        ];
        
        echo json_encode($errorResponse);
        
        // 记录错误日志
        error_log("Qwen Proxy Error: " . $message);
    }
    
    /**
     * 记录请求日志（可选）
     */
    private function logRequest()
    {
        // 在实际生产环境中，您可能想要记录请求信息
        // 这里简单记录到error log
        $logData = [
            'time' => date('Y-m-d H:i:s'),
            'method' => $_SERVER['REQUEST_METHOD'],
            'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'unknown',
            'content_length' => $_SERVER['CONTENT_LENGTH'] ?? 0,
            'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ];
        
        error_log("Qwen Proxy Request: " . json_encode($logData));
    }
    
    /**
     * 记录响应日志（可选）
     */
    private function logResponse($response)
    {
        $logData = [
            'time' => date('Y-m-d H:i:s'),
            'status_code' => $response['status_code'],
            'content_length' => strlen($response['content'])
        ];
        
        error_log("Qwen Proxy Response: " . json_encode($logData));
    }
}

// 配置和启动代理
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// 处理预检请求
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 创建代理实例并处理请求
$proxy = new QwenProxy();
$proxy->handleProxyRequest();

<?php
/**
 * 数据库备份还原类（utf8mb4 优化版）
 * 保持接口完全兼容
 */
class DatabaseTool
{
    private $handler;
    private $config = [
        'host'     => 'localhost',
        'port'     => 3306,
        'user'     => 'root',
        'password' => '',
        'database' => 'jol',
        'charset'  => 'utf8mb4',  // ✅ 改为 utf8mb4
        'target'   => 'sql.sql'
    ];
    private $tables = [];
    private $error;
    private $begin;

    public function __construct($config = [])
    {
        $this->begin = microtime(true);
        $this->config = array_merge($this->config, (array)$config);

        try {
            $dsn = "mysql:host={$this->config['host']};port={$this->config['port']};dbname={$this->config['database']};charset={$this->config['charset']}";
            $this->handler = new PDO($dsn, $this->config['user'], $this->config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_NUM,
            ]);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    public function backup($tables = [])
    {
        $this->setTables($tables);
        if (empty($this->tables)) {
            $this->error = '数据库中没有表!';
            return false;
        }

        $fp = @fopen($this->config['target'], 'w');
        if (!$fp) {
            $this->error = '无法写入目标文件';
            return false;
        }

        // 文件头
        $head = "/*\r\nMySQL Database Backup Tool\r\n";
        $head .= "Server: {$this->config['host']}:{$this->config['port']}\r\n";
        $head .= "Database: {$this->config['database']}\r\n";
        $head .= "Charset: {$this->config['charset']}\r\n";
        $head .= "Date: " . date('Y-m-d H:i:s') . "\r\n*/\r\n";
        $head .= "SET NAMES '{$this->config['charset']}';\r\n";
        $head .= "SET FOREIGN_KEY_CHECKS=0;\r\n";
        fwrite($fp, $head);

        foreach ($this->tables as $table) {
            fwrite($fp, "-- ----------------------------\r\n");
            fwrite($fp, "-- Table structure for {$table}\r\n");
            fwrite($fp, "-- ----------------------------\r\n");
            fwrite($fp, "DROP TABLE IF EXISTS `{$table}`;\r\n");
            fwrite($fp, $this->getDDL($table) . "\r\n");
            fwrite($fp, "-- ----------------------------\r\n");
            fwrite($fp, "-- Records of {$table}\r\n");
            fwrite($fp, "-- ----------------------------\r\n");
            $this->dumpData($fp, $table);
        }

        fclose($fp);
        echo 'Backup Finished! Time ' . round((microtime(true) - $this->begin) * 1000, 2) . " ms";
        return true;
    }

    private function setTables($tables = [])
    {
        $this->tables = (!empty($tables) && is_array($tables)) ? $tables : $this->getTables();
    }

    private function query($sql)
    {
        $stmt = $this->handler->query($sql);
        return $stmt ? $stmt->fetchAll() : [];
    }

    private function getTables()
    {
        $list = $this->query('SHOW TABLES');
        return array_column($list, 0);
    }

    private function getDDL($table)
    {
        $ddl = $this->query("SHOW CREATE TABLE `{$table}`");
        return $ddl[0][1] . ';';
    }

    private function dumpData($fp, $table)
    {
        $columns = array_column($this->query("SHOW COLUMNS FROM `{$table}`"), 0);
        $columnList = '`' . implode('`,`', $columns) . '`';
        $stmt = $this->handler->query("SELECT * FROM `{$table}`", PDO::FETCH_NUM);

        while ($row = $stmt->fetch()) {
            $values = array_map([$this->handler, 'quote'], $row);
            $sql = "INSERT INTO `{$table}` ({$columnList}) VALUES (" . implode(',', $values) . ");\r\n";
            fwrite($fp, $sql);
        }
    }

    public function restore($path = '')
    {
        if (!file_exists($path)) {
            $this->error = 'SQL文件不存在!';
            return false;
        }

        $sqlList = $this->parseSQL($path);
        try {
            foreach ($sqlList as $sql) {
                if (trim($sql) !== '') {
                    $this->handler->exec($sql);
                }
            }
            echo '还原成功! 花费时间 ' . round((microtime(true) - $this->begin) * 1000, 2) . " ms";
            return true;
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    private function parseSQL($path)
    {
        $sql = file_get_contents($path);
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
        $sql = preg_replace('/--.*(\r?\n)/', '', $sql);
        $sql = preg_replace('/^\s*#.*$/m', '', $sql);
        $statements = preg_split('/;\s*[\r\n]+/', $sql);
        return array_filter(array_map('trim', $statements));
    }

    public function getError()
    {
        return $this->error;
    }
}

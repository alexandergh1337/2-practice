<?php
class MockDatabase {
    private $data = [];
    public function query($sql) {
        if (strpos($sql, 'INSERT INTO') === 0) {
            preg_match('/VALUES \((.*?)\)/', $sql, $matches);
            $values = explode(',', $matches[1]);
            $this->data[] = [
                'username' => trim($values[0], "'"),
                'password' => trim($values[1], "'"),
                'email' => trim($values[2], "'"),
                'registration_date' => trim($values[3], "'")
            ];
            return true;
        } elseif (strpos($sql, 'SELECT * FROM') === 0) {
            $result = new MockResult();
            foreach ($this->data as $row) {
                if ($row['username'] === 'testuser') {
                    $result->rows[] = $row;
                }
            }
            return $result;
        }
        return true;
    }
    public function prepare($sql) {
        return new MockStatement($this, $sql);
    }
}
class MockResult {
    public $rows = [];
    public $num_rows;
    public function fetch_assoc() {
        return array_shift($this->rows);
    }
    public function __get($name) {
        if ($name === 'num_rows') {
            return count($this->rows);
        }
    }
}
class MockStatement {
    private $db;
    private $sql;
    private $params = [];
    public function __construct($db, $sql) {
        $this->db = $db;
        $this->sql = $sql;
    }
    public function bind_param($types, ...$params) {
        $this->params = $params;
    }
    public function execute() {
        $sql = str_replace('?', "'%s'", $this->sql);
        $sql = vsprintf($sql, $this->params);
        return $this->db->query($sql);
    }
    public function __get($name) {
        if ($name === 'error') {
            return '';
        }
    }
}
?>
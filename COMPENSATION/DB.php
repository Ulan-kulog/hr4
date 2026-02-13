<?php

/**
 * Ultra Simple MySQL Database Connection
 * Edit only the 4 variables below
 */
class Database
{
    private static $pdo = null;

    // ================= EDIT ONLY THESE 4 LINES =================
    private const DB_HOST = 'localhost';     // Your database host
    private const DB_NAME = 'hr4_hr_4';   // Your database name  
    private const DB_USER = '3206_CENTRALIZED_DATABASE';          // Your database username
    private const DB_PASS = '1234';      // Your database password
    // ===========================================================

    /**
     * Get PDO connection
     * @return PDO
     */
    public static function connect(): PDO
    {
        if (self::$pdo === null) {
            try {
                $dsn = 'mysql:host=' . self::DB_HOST . ';dbname=' . self::DB_NAME . ';charset=utf8mb4';

                self::$pdo = new PDO($dsn, self::DB_USER, self::DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]);
            } catch (PDOException $e) {
                die('Database Connection Failed: ' . $e->getMessage());
            }
        }

        return self::$pdo;
    }

    /**
     * Prepare and execute a query with parameters
     * @param string $sql SQL query with placeholders
     * @param array $params Parameters for the query
     * @return PDOStatement
     */
    public static function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::connect()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Execute a query and fetch all results
     * @param string $sql SQL query with placeholders
     * @param array $params Parameters for the query
     * @return array
     */
    public static function fetchAll(string $sql, array $params = []): array
    {
        $stmt = self::query($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Execute a query and fetch a single row
     * @param string $sql SQL query with placeholders
     * @param array $params Parameters for the query
     * @return object|null
     */
    public static function fetch(string $sql, array $params = []): ?object
    {
        $stmt = self::query($sql, $params);
        return $stmt->fetch() ?: null;
    }

    /**
     * Execute a query and fetch a single row as an associative array
     * @param string $sql SQL query with placeholders
     * @param array $params Parameters for the query
     * @return array|null
     */
    public static function fetchRow(string $sql, array $params = []): ?array
    {
        $stmt = self::query($sql, $params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row !== false ? $row : null;
    }

    /**
     * Execute a query and fetch a single column value
     * @param string $sql SQL query with placeholders
     * @param array $params Parameters for the query
     * @param int $column Column index (default: 0)
     * @return mixed
     */
    public static function fetchColumn(string $sql, array $params = [], int $column = 0)
    {
        $stmt = self::query($sql, $params);
        return $stmt->fetchColumn($column);
    }

    /**
     * Execute an INSERT query and return the last insert ID
     * @param string $sql INSERT SQL query
     * @param array $params Parameters for the query
     * @return string Last insert ID
     */
    public static function insert(string $sql, array $params = []): string
    {
        self::query($sql, $params);
        return self::connect()->lastInsertId();
    }

    public static function update($table, $data, $where)
    {
        $fields = "";
        foreach ($data as $key => $value) {
            $fields .= "$key = :$key, ";
        }
        $fields = rtrim($fields, ", ");

        $whereField = array_key_first($where);
        $sql = "UPDATE $table SET $fields WHERE $whereField = :where_$whereField";

        // Merge data and where for execution
        $params = $data;
        $params["where_$whereField"] = $where[$whereField];

        // Use your existing execution logic here
        // return self::execute($sql, $params); 
    }

    public static function delete($table, $where)
    {
        $whereField = array_key_first($where);
        $sql = "DELETE FROM $table WHERE $whereField = :$whereField";
        // return self::execute($sql, $where);
    }

    /**
     * Get last inserted ID
     * @return string
     */
    public static function lastInsertId(): string
    {
        return self::connect()->lastInsertId();
    }

    /**
     * Execute an UPDATE or DELETE query and return the number of affected rows
     * @param string $sql SQL query
     * @param array $params Parameters for the query
     * @return int Number of affected rows
     */
    public static function execute(string $sql, array $params = []): int
    {
        $stmt = self::query($sql, $params);
        return $stmt->rowCount();
    }

    /**
     * Begin a transaction
     * @return bool
     */
    public static function beginTransaction(): bool
    {
        return self::connect()->beginTransaction();
    }

    /**
     * Commit a transaction
     * @return bool
     */
    public static function commit(): bool
    {
        return self::connect()->commit();
    }

    /**
     * Rollback a transaction
     * @return bool
     */
    public static function rollBack(): bool
    {
        return self::connect()->rollBack();
    }

    /**
     * Check if a record exists
     * @param string $sql SQL query with placeholders
     * @param array $params Parameters for the query
     * @return bool
     */
    public static function exists(string $sql, array $params = []): bool
    {
        $result = self::fetchColumn($sql, $params);
        return $result > 0;
    }

    /**
     * Insert data into a table and return the last insert ID
     * @param string $table Table name
     * @param array $data Associative array of column => value
     * @return string Last insert ID
     */
    public static function insertInto(string $table, array $data): string
    {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');

        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        self::query($sql, array_values($data));
        return self::connect()->lastInsertId();
    }

    /**
     * Update records in a table
     * @param string $table Table name
     * @param array $data Associative array of column => value
     * @param string $where WHERE clause (without WHERE keyword)
     * @param array $whereParams Parameters for WHERE clause
     * @return int Number of affected rows
     */
    public static function updateTable(string $table, array $data, string $where, array $whereParams = []): int
    {
        $setClause = implode(' = ?, ', array_keys($data)) . ' = ?';
        $sql = "UPDATE $table SET $setClause WHERE $where";

        $params = array_merge(array_values($data), $whereParams);
        $stmt = self::query($sql, $params);
        return $stmt->rowCount();
    }
}

function dd($data)
{
    echo "<pre>";
    var_dump($data);
    echo "</pre>";
    exit;
}

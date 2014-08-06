<?php

namespace PhpKansai\TodoManager\Helper;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

class TestDatabaseHelper
{
    const TEST_SQLITE_PATH = '/../../var/test.sqlite';
    const DB_SCHEMA_PATH = '/../../schema.sql';

    /**
     * テストデータベース作成
     * @return Connection
     */
    public static function createConnection()
    {
        $path = __DIR__ . self::TEST_SQLITE_PATH;
        $conn = DriverManager::getConnection([
                'driver' => 'pdo_sqlite',
                'path' => $path,
            ]);
        $sql = file_get_contents(__DIR__ . self::DB_SCHEMA_PATH);
        foreach (explode(';', $sql) as $s) {
            $s = trim($s);
            if (!empty($s)) {
                $conn->executeQuery($s);
            }
        }
        return $conn;
    }

    /**
     * テストデータベース削除
     */
    public static function clean()
    {
        $path = __DIR__ . self::TEST_SQLITE_PATH;
        if (file_exists($path)) {
            unlink($path);
        }
    }
}

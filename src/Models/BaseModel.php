<?php

namespace App\Models;

use App\Models\BaseModel;
use PDO;

class BaseModel
{
    protected $db;

    public function __construct()
    {
        // Global Database Connection
        global $conn;
        $this->db = $conn;        
    }

    public function fill($payload)
    {
        foreach ($payload as $key => $value)
        {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    protected static $dbConnection;

    // Assuming this method is already handling DB connection
    public static function getDbConnection()
    {
        if (!self::$dbConnection) {
            self::$dbConnection = new PDO("mysql:host=localhost;dbname=sample_helpdeskdb", "root", "");
            self::$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$dbConnection;
    }
    
}
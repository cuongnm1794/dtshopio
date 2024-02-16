<?php
class Schema
{
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function createTable($tableName, $columns)
    {
        $sql = "CREATE TABLE IF NOT EXISTS $tableName  (";
        $columnDefinitions = [];

        foreach ($columns as $columnName => $columnType) {
            $columnDefinitions[] = "$columnName $columnType";
        }

        $sql .= implode(", ", $columnDefinitions);
        $sql .= ")";


        $this->db->query($sql);
    }

    public function dropTable($tableName)
    {
        $sql = "DROP TABLE $tableName";
        $this->db->query($sql);
    }

    // Các phương thức khác như addColumn(), modifyColumn(), dropColumn(), v.v.

    // ...
}

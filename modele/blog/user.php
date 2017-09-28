<?php
class User{

    // database connection and table name
    private $conn;
    private $table_name = "users";

    // object properties
    public $id;
    public $username;

    public function __construct($db) {
        $this->conn = $db;
    }

    // used by select drop-down list
    function read() {

        $query = "SELECT 
                    id, username 
                FROM
                    " . $this->table_name . "
                ORDER BY username";

        $stmt = $this->conn->prepare( $query );
        $stmt->execute();

        return $stmt;
    }

    // used to read username by its ID
    function readUsername() {
     
        $query = "SELECT username FROM " . $this->table_name . " WHERE id = ? limit 0,1";
 
        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
 
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
     
        $this->username = $row['username'];
    }
}

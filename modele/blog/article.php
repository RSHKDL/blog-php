<?php
class Article {
 
    // database connection and table name
    // utilisation d'une superclasse entity qui gère les attributs communs des entités (comme : created et modified)
    // /!\ manager attribute
    private $conn;
    private $table_name = "articles";
 
    // object properties
    public $id;
    public $title;
    public $header;
    public $content;
    public $category_id;
    public $user_id;
    public $timestamp;
    
    // /!\ manager method
    public function __construct($db){
        $this->conn = $db;
    }

    // method create article
    function create() {
 
        $query = "INSERT INTO
                    " . $this->table_name . "
                SET
                    title=:title, header=:header, content=:content, category_id=:category_id, user_id=:user_id, created=:created";

        $stmt = $this->conn->prepare($query);

        // posted values
        $this->title=htmlspecialchars(strip_tags($this->title));
        $this->header=htmlspecialchars(strip_tags($this->header));
        $this->content=htmlspecialchars(strip_tags($this->content));
        $this->category_id=htmlspecialchars(strip_tags($this->category_id));
        $this->user_id=htmlspecialchars(strip_tags($this->user_id));

        // get timestamp for 'created' field
        $this->timestamp = date('Y-m-d H:i:s');

        // bind values 
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":header", $this->header);
        $stmt->bindParam(":content", $this->content);
        $stmt->bindParam(":category_id", $this->category_id);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":created", $this->timestamp);

        if($stmt->execute()) {
            return true;
        }
        else {
            return false;
        }
    }

    // method for reading all articles
    function readAll($from_record_num, $records_per_page) {
 
        $query = "SELECT
                    id, title, header, category_id, user_id, created
                FROM
                    " . $this->table_name . "
                ORDER BY
                    created ASC
                LIMIT
                    {$from_record_num}, {$records_per_page}";
 
        $stmt = $this->conn->prepare( $query );
        $stmt->execute();
 
        return $stmt;
    }

    // method for paging articles
    public function countAll() {
 
        $query = "SELECT id FROM " . $this->table_name . "";
 
        $stmt = $this->conn->prepare( $query );
        $stmt->execute();
 
        $num = $stmt->rowCount();
 
        return $num;
    }

    // method for reading one article
    function readOne() {
 
        $query = "SELECT
                    id, title, header, content, category_id, user_id, created
                FROM
                    " . $this->table_name . "
                WHERE
                    id = ?
                LIMIT
                    0,1";
 
        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
 
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
 
        $this->title = $row['title'];
        $this->header = $row['header'];
        $this->content = $row['content'];
        $this->category_id = $row['category_id'];
        $this->user_id = $row['user_id'];
        $this->created = $row['created'];
    }

    // method for updating one article
    function update() {
 
        $query = "UPDATE
                    " . $this->table_name . "
                SET
                    title = :title,
                    header = :header,
                    content = :content,
                    category_id  = :category_id
                WHERE
                    id = :id";
     
        $stmt = $this->conn->prepare($query);
     
        // posted values
        $this->title=htmlspecialchars(strip_tags($this->title));
        $this->header=htmlspecialchars(strip_tags($this->header));
        $this->content=htmlspecialchars(strip_tags($this->content));
        $this->category_id=htmlspecialchars(strip_tags($this->category_id));
        $this->id=htmlspecialchars(strip_tags($this->id));
     
        // bind parameters
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':header', $this->header);
        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':category_id', $this->category_id);
        $stmt->bindParam(':id', $this->id);
     
        // execute the query
        if($stmt->execute()) {
            return true;
        }
     
        return false;
     
    }

    // method for deleting one article
    function delete() {

        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
     
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        if($result = $stmt->execute()) {
            return true;
        }
        else {
            return false;
        }
    }
}
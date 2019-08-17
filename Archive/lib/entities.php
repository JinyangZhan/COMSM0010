<?php

class Comment {

    public function __construct($itemID) {
        require_once "connection.php";
        $this->conn = Database::conn();
        $this->itemID = $itemID;
    }

    public function addComment($comment, $posterID) {
        $this->conn->execute(
            "INSERT INTO post(`item_id`,`poster_id`,`content`) VALUES(?,?,?)", 
            array($this->itemID, $posterID, $comment)
        );
        $this->conn->execute("UPDATE item SET reply_count=reply_count+1 WHERE id=?", array($this->itemID));
    }

    public function getComments() {
        return $this->conn->searchAll("SELECT post.*,user.username FROM post LEFT JOIN user ON post.poster_id=user.id WHERE item_id=? AND post.disabled=0 ORDER BY post.post_time DESC", array($this->itemID));
    }

    public function removeComment($id) {
        $this->conn->execute("UPDATE post SET disabled=1 WHERE id=?", array($id));
    }
}

class Item {

    public function __construct() {
        require_once "connection.php";
        $this->conn = Database::conn();
    }

    public function addItem($lof, $itemname, $description, $userID) {
        $this->conn->execute(
            "INSERT INTO item(`lost_or_found`,`item_name`,`description`,`publisher_id`) VALUES (?,?,?,?)",
            array($lof, $itemname, nl2br($description), $userID)
        );
    }

    public function updateItem($id, $lof, $itemname, $description, $status, $userID) {
        $this->conn->execute(
            "UPDATE item SET lost_or_found=?, closed=?, item_name=?, description=? WHERE id=?",
            array($lof, $status, $itemname, nl2br($description), $id)
        );
    }

    public function getItem($id) {
        $this->conn->execute("UPDATE item SET view_count=view_count+1 WHERE id=?", array($id));
        
        $comment = new Comment($id);
        $item = $this->conn->search("SELECT item.*,user.username FROM item LEFT JOIN user ON item.publisher_id=user.id WHERE item.id=? AND item.disabled=0", array($id));

        $package = new stdClass();
        $package->item = $item;
        $package->comment = $item ? $comment->getComments() : array();
        return $package;
    }

    public function removeItem($id) {
        $this->conn->execute("UPDATE item SET disabled=1 WHERE id=?", array($id));
    }
}

class Items {
    
    private $current;
    private $total;

    public function __construct($page = 1) {
        require_once "connection.php";
        $this->conn = Database::conn();
        $this->total = $this->conn->execute("SELECT * FROM item");

        $this->limit = 10;
        $this->page = $page;
    }

    public function getItems($userID = NULL, $search = NULL) {
        $this->current = ($this->page - 1) * $this->limit;
        $extra = "";
        if (isset($userID)) {
            $extra = " AND user.id=$userID ";
        }
        if (isset($search)) {
            $extra = " AND item.item_name LIKE ? ";
        }

        $result = $this->conn->searchAll("SELECT item.*,user.username AS publisher, q.disabled AS qd, q.poster_id, q.post_time, u.username AS replyer FROM item LEFT JOIN user ON item.publisher_id=user.id LEFT JOIN (SELECT p.item_id,p.poster_id,p.post_time,p.disabled FROM (SELECT item_id, disabled, max(post_time) AS post_time FROM post GROUP BY item_id, disabled HAVING disabled=0) AS t INNER JOIN post AS p ON t.item_id=p.item_id AND t.post_time=p.post_time) AS q ON item.id=q.item_id LEFT JOIN user AS u ON q.poster_id=u.id WHERE item.disabled=0 $extra ORDER BY item.publish_time DESC LIMIT $this->current, $this->limit", (isset($search) ? array('%'.$search.'%') : array()));

        $package = new stdClass();
        $package->page = $this->page;
        $package->total = ceil($this->total / $this->limit);
        $package->result = $result;

        return $package;
    }

    public function getNavigator() {
        $firstPage = "<a href=\"?page=1\">First Page</a>";
        
        $previousPageNum = max($this->page - 1, 1);
        $previousPage = "<a href=\"?page=$previousPageNum\">Previous Page</a>";

        $lastPageNum = ceil($this->total / $this->limit);
        
        $nextPageNum = min($this->page + 1, $lastPageNum);
        $nextPage = "<a href=\"?page=$nextPageNum\">Next Page</a>";

        $lastPage = "<a href=\"?page=$lastPageNum\">Last Page</a>";

        return "[ $firstPage | $previousPage | $nextPage | $lastPage ]"; 
    }
}

class Users {
    
    private $current;
    private $total;

    public function __construct($page = 1) {
        require_once "connection.php";
        $this->conn = Database::conn();
        $this->total = $this->conn->execute("SELECT * FROM user");

        $this->limit = 20;
        $this->page = $page;
    }

    public function getUsers() {
        $this->current = ($this->page - 1) * $this->limit;
        $result = $this->conn->searchAll("SELECT * FROM user LIMIT $this->current, $this->limit");

        $package = new stdClass();
        $package->page = $this->page;
        $package->total = ceil($this->total / $this->limit);
        $package->result = $result;

        return $package;
    }

    public function getNavigator() {
        $firstPage = "<a href=\"?page=1\">First Page</a>";
        
        $previousPageNum = max($this->page - 1, 1);
        $previousPage = "<a href=\"?page=$previousPageNum\">Previous Page</a>";

        $lastPageNum = ceil($this->total / $this->limit);
        
        $nextPageNum = min($this->page + 1, $lastPageNum);
        $nextPage = "<a href=\"?page=$nextPageNum\">Next Page</a>";

        $lastPage = "<a href=\"?page=$lastPageNum\">Last Page</a>";

        return "[ $firstPage | $previousPage | $nextPage | $lastPage ]"; 
    }
}
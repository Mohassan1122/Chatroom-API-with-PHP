<?php
class User
{
    private $conn;

    // Table
    private $user_table = "chatroom";
    private $msg_table = "messageroom";

    // Columns
    public $name;
    public $password;
    public $email;
    public $mobile;
    public $address;
    public $created_at;
    public $id;
    public $message;
    public $senderName;
    

    // Db connection
    public function __construct($db)
    {
        $this->conn = $db;

    }

    public function createUser()
    {

        $user_query = "INSERT INTO " . $this->user_table . " SET name = :name, password = :password, email = :email, mobile = :mobile,  address = :address";

        $user_obj = $this->conn->prepare($user_query);

        // sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->mobile = htmlspecialchars(strip_tags($this->mobile));
        $this->address = htmlspecialchars(strip_tags($this->address));
       

        // bind data
        $user_obj->bindParam(":name", $this->name);
        $user_obj->bindParam(":password", $this->password);
        $user_obj->bindParam(":email", $this->email);
        $user_obj->bindParam(":mobile", $this->mobile);
        $user_obj->bindParam(":address", $this->address);
       

        if ($user_obj->execute()) {
            return true;
        }
        return false;

    }

    public function checkEmail()
    {
        $sql = "SELECT * FROM " . $this->user_table . " WHERE email = :email";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(":email", $this->email);

        $stmt->execute();
        
        $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);
            
      return $dataRow;
    }

    public function loginUser()
    {
        $sql = "SELECT * FROM " . $this->user_table . " WHERE email = :email";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(":email", $this->email);

        $stmt->execute();
        
        $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);
            
        return $dataRow;
    }

    public function sendChat()
    {

        $project_query = "INSERT INTO " . $this->msg_table . " SET message = :message, senderName = :senderName";

        $project_obj = $this->conn->prepare($project_query);

        // sanitize
        $this->message = htmlspecialchars(strip_tags($this->message));
        $this->senderName = htmlspecialchars(strip_tags($this->senderName));

        // bind data
        $project_obj->bindParam(":message", $this->message);;
        $project_obj->bindParam(":senderName", $this->senderName);

        if ($project_obj->execute()) {
            return true;
        }
        return false;

    }

    public function chatRoom()
    {
        $project_query_all = "SELECT * FROM " . $this->msg_table . " ORDER BY id DESC";
        
        $stmt = $this->conn->prepare($project_query_all);

        $stmt->execute();

        return $stmt->get_result();
    }

    function deleteMsg(){

        $sqlQuery = "DELETE FROM " . $this->msg_table . " WHERE id = :id AND senderName = :senderName";

        $stmt = $this->conn->prepare($sqlQuery);
    
        $this->senderName=htmlspecialchars(strip_tags($this->senderName));
    
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":senderName", $this->senderName);
    
        if($stmt->execute()){
            return true;
        }
        return false;
    }

}


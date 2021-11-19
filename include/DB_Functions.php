<?php
 
class DB_Functions {
 
    private $conn;
 
    // constructor
    function __construct() {
        require_once 'DB_Connect.php';
        // koneksi ke database
        $db = new Db_Connect();
        $this->conn = $db->connect();
    }
 
    // destructor
    function __destruct() {
         
    }
 
    public function simpanUser($name, $username,  $email, $password) {
        $uuid = uniqid('', true);
        $hash = $this->hashSSHA($password);
        $encrypted_password = $hash["encrypted"]; // encrypted password
        // $salt = $hash["salt"]; // salt
 
        $stmt = $this->conn->prepare("INSERT INTO tbl_user(unique_id, name, username, email, encrypted_password) VALUES(?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $uuid, $name, $username,  $email, $encrypted_password);
        $result = $stmt->execute();
        $stmt->close();
 
        // cek jika sudah sukses
        if ($result) {
            $stmt = $this->conn->prepare("SELECT * FROM tbl_user WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();
 
            return $user;
        } else {
            return false;
        }
    }
 
    /**
     * Get user berdasarkan email dan password
     */
    public function getUserByEmailAndPassword($email, $password) {
 
        $stmt = $this->conn->prepare("SELECT * FROM tbl_user WHERE email = ?");
 
        $stmt->bind_param("s", $email);
 
        if ($stmt->execute()) {
            $userm = $stmt->get_result()->fetch_assoc();
            $stmt->close();
 
            // verifikasi password userm
            $encrypted_password = $userm['encrypted_password'];
            $hash = $this->checkhashSSHA($password);
            // cek password jika sesuai
            if ($encrypted_password == $hash) {
                // autentikasi userm berhasil
                return $userm;
            }
        } else {
            return NULL;
        }
    }
    /**
     * Get user berdasarkan username dan password
     */
    public function getUserByUsernameAndPassword($email, $password) {
 
        $stmt = $this->conn->prepare("SELECT * FROM tbl_user WHERE username = ?");
 
        $stmt->bind_param("s", $email);
 
        if ($stmt->execute()) {
            $useru = $stmt->get_result()->fetch_assoc();
            $stmt->close();
 
            // verifikasi password useru
            $encrypted_password = $useru['encrypted_password'];
            $hash = $this->checkhashSSHA($password);
            // cek password jika sesuai
            if ($encrypted_password == $hash) {
                // autentikasi useru berhasil
                return $useru;
            }
        } else {
            return NULL;
        }
    }
 
    /**
     * Cek User ada atau tidak
     */
    public function isUserExisted($email) {
        $stmt = $this->conn->prepare("SELECT email from tbl_user WHERE email = ?");
 
        $stmt->bind_param("s", $email);
 
        $stmt->execute();
 
        $stmt->store_result();
 
        if ($stmt->num_rows > 0) {
            // user telah ada 
            $stmt->close();
            return true;
        } else {
            // user belum ada 
            $stmt->close();
            return false;
        }
    }

    public function isUsername($username) {
        $un = $this->conn->prepare("SELECT username from tbl_user WHERE username = ?");
 
        $un->bind_param("s", $username);
 
        $un->execute();
 
        $un->store_result();
 
        if ($un->num_rows > 0) {
            // user telah ada 
            $un->close();
            return true;
        } else {
            $un->close();
             return false;
        }
    }
 
    /**
     * Encrypting password
     * @param password
     * returns salt and encrypted password
     */
    public function hashSSHA($password) {
 
        $salt = sha1(rand());
        $salt = substr(0, 10);
        $encrypted = base64_encode(sha1($password, true));
        $hash = array( "encrypted" => $encrypted);
        return $hash;
    }
 
    /**
     * Decrypting password
     * @param salt, password
     * returns hash string
     */
    public function checkhashSSHA($password) {
 
        $hash = base64_encode(sha1($password, true));
 
        return $hash;
    }


    /**
     * edit user name
     */
    // cek username
    public function getUsername($username){
        $stmt = $this->conn->prepare("SELECT username from tbl_user WHERE username = ?");
 
        $stmt->bind_param("s", $username);
 
        $stmt->execute();
 
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // user telah ada 
            $stmt->close();
            return true;
        } else {
            // user belum ada 
            $stmt->close();
            return false;
        }
    }

    // edit username
    function editusername($username, $password){
        $stmt = $this->conn->prepare("UPDATE INTO tbl_user SET username= '".$username."' WHERE email = '".$email."'");

        // $stmt->bindparam("s", $username);
        // $stmt->bindparam("s", $email);
        $result = $stmt->excute();
        $stmt->close();

        if($result){
            $stmt = $this->conn->prepare("SELECT * FROM tbl_user WHERE username=?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            return $user;
        }else{
            return false;
        }

    }
 
}
 
?>
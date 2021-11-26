<?php 
class DbOperation{
    private $con;
 
    function __construct(){
        require_once dirname(__FILE__) . '/DbConnect.php';
        $db = new DbConnect();
        $this->con = $db->connect();
    }
     
    //fungsi untuk mengecek apakah id gambar sudah ada/tidak
    private function cekIdGambar($id){
        $stmt = $this->con->prepare("SELECT id FROM gambar WHERE id = ?");
        $stmt->bind_param("s",$id);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }
     
    //fungsi untuk menyimpan data gambar ke tabel tb_upload
    public function insertGambar($email, $namafile){
            // $stmt = $this->con->prepare("INSERT INTO gambar(gambar) VALUES (?);");
            
            $stmt = $this->con->prepare("UPDATE  tbl_user SET gambar=? WHERE email=?");
            $stmt->bind_param("ss",$namafile , $email);
            if($stmt->execute())
                return 0; //return 0 means success
                return 1;
    }
     
    //fungsi untuk mengambil data di tabel tb_upload
    public function tampilGambar(){
        $stmt = $this->con->prepare("
        SELECT id, nama, CONCAT('localhost/uploadgambar/gambar/',gambar) AS file FROM tb_upload ORDER BY id
        ");
        $stmt->execute(); 
        $result = $stmt->get_result();
        return $result; 
    }
     
} ?>
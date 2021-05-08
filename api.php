<?php
    namespace DB;
    require 'mysqli.php';
    $db = new MySQLi("0.0.0.0","root","pothukuchi","softij11_oc1");
    function getProducts($numRows,$start){
        global $db;
        if($start == null){
            $start = 3647;
        }
        $val = $db->query("SELECT product_id,model,quantity,price,date_available,viewed FROM oc_product WHERE product_id >= $start limit $numRows");
        $res = new Response('ok', 1, $val->rows,$val->num_rows);
        return $res;
    }

    $route = $_GET['route'];
    if($route != null){
        if($route == 'products/limit'){
            $limit = $_GET['limit'];
            $start = $_GET['start'];
            if($limit != null){
                echo json_encode(getProducts($limit,$start));
            }else{
                echo json_encode(new Response('limit is null',-1));
            }
        }else{
            echo json_encode(new Response('route '. $route .'is not defined',-1));
        }
    }else{
        echo json_encode(new Response('route is null',-1));
    }

    class Response{
        public $Msg;
        public $code;
        public $numRows;
        public $result;
        public function __construct($errMsg,$code,$result = null,$numRows = 0){
            $this->Msg = $errMsg;
            $this->code = $code;
            $this->numRows = $numRows;
            $this->result = $result;
        }
    }
?>

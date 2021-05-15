<?php
    namespace DB;
    require 'mysqli.php';
    $db = new MySQLi("0.0.0.0","root","Spacerocket","softij11_oc1");
    function getProducts($numRows,$start){
        global $db;
        if($start == null){
            $start = 3647;
        }
        $p = 'oc_product';
        $d = 'oc_product_description';
        $val = $db->query(
            "SELECT $p.product_id,$d.name,$d.description,$p.model,$p.quantity,$p.price,$p.date_available,$p.viewed 
            FROM oc_product INNER JOIN oc_product_description
            ON $p.product_id = $d.product_id WHERE $p.product_id >= $start limit $numRows");
        $res = new Response('ok', 1, $val->rows,$val->num_rows);
        return $res;
    }

    function getWishlistProducts($customer_id){
        global $db;
        $p = 'oc_product';
        $d = 'oc_product_description';
        $a = 'oc_customer_wishlist';
        $val = $db->query(

            "SELECT oc_product.product_id,name,description,model
            quantity,price,oc_customer_wishlist.date_added AS DateAdded
            FROM oc_product 
            JOIN oc_customer_wishlist 
            ON oc_customer_wishlist.customer_id = $customer_id and 
            oc_customer_wishlist.product_id = oc_product.product_id
            JOIN oc_product_description
            ON oc_product.product_id = oc_product_description.product_id");

        $res = new Response('ok', 1, $val->rows,$val->num_rows);
        return $res;
    }

    // function putWishlistProducts($customer_id,$product_id){
    //     global $db;
    //     $db->query(
    //         "insert into oc_customer_wishlist (customer_id,product_id) values($customer_id,$product_id)"
    //     );
    // }



    // $postRoutes = [
    //     'wishlist'=>function(){
    //         if ($_SERVER['REQUEST_METHOD']=='POST'){
    //             $customer_id = $_POST['customerId'];
    //             $product_id = $_POST['']
    //             if($customer_id != null && $product_id != null ){
    //                 putWishlistProducts($customer_id,$product_id);
    //             }
    //             // else{

    //             // }
    //         }

    //     }
    // ];

    $getRoutes = [
        'products'=>function(){
            if($_SERVER['REQUEST_METHOD'] == 'GET'){
                $limit = $_GET['limit'];
                $start = $_GET['start'];
                if($limit != null){
                    echo json_encode(getProducts($limit,$start));
                }else{
                    echo json_encode(new Response('limit is null',-1));
                }
            }else{
                echo json_encode(new Response('invalid method',-1));
            }            
        },
        'wishlist'=>function(){
            if ($_SERVER['REQUEST_METHOD']=='GET'){
                $customer_id = $_GET['customerId'];
                if($customer_id != null){
                    echo json_encode(getWishlistProducts($customer_id));
                }
            }
        }
    ];
    $getRoute = $_GET['route'];

    // $postRoute = $_POST['route'];
    // if($postRoute != null){
    //     $func = $postRoutes[$postRoute];
    //     if($func != null){
    //         $func();
    //     }else{
    //         echo json_encode(new Response($postRoute.' is not defined',-1));
    //     }
    // }

    if($getRoute != null){
        $func = $getRoutes[$getRoute];
        if($func != null){
            $func();
        }else{
            echo json_encode(new Response($getRoute.' is not defined',-1));
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

<?php
    namespace DB;
    require 'mysqli.php';
    $db = new MySQLi("0.0.0.0","root","pothukuchi","softij11_oc1");
    function getProducts($numRows,$start){
        global $db;
        if($start == null){
            $start = 3647;
        }
        $p = 'oc_product';
        $d = 'oc_product_description';
        $val = $db->query(
            "SELECT $p.product_id,$d.name,$d.description,$p.model,$p.quantity,$p.price,$p.date_added,$p.viewed 
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
            "SELECT oc_product.product_id,name,description,oc_product.model,
            viewed,oc_product.date_added,quantity,price,
            oc_customer_wishlist.date_added AS addedToListOn
            FROM oc_product 
            JOIN oc_customer_wishlist 
            ON oc_customer_wishlist.customer_id = $customer_id and 
            oc_customer_wishlist.product_id = oc_product.product_id
            JOIN oc_product_description
            ON oc_product.product_id = oc_product_description.product_id");
        $res = new Response('ok', 1, $val->rows,$val->num_rows);
        return $res;
    }

    function putWishlistProducts($customer_id,$product_id){
        global $db;
        $val = $db->query(
            "insert into oc_customer_wishlist (customer_id,product_id) values($customer_id,$product_id)"
        );
        if($val){
            return new Response('ok',1);
        }
        return new Response('product is not added to wishlist',-1);
    }

    function getCustomerId($email){
        global $db;
        $val = $db->query(
            "SELECT customer_id,store_id,firstname,lastname,email,address_id,telephone,ip,date_added FROM oc_customer WHERE email = '$email'"
        );
        if($val->num_rows > 0){
            return new Response('ok',1,$val->rows,$val->num_rows);
        }
        return new Response('no user found',-1);
    }

    function updateCustomer($id,$email,$firstname,$lastname,$phone){
        global $db;
        $customer = getCustomerId($email);
        if($customer->code < 0 || $customer->result[0]['customer_id'] == $id){
            $val = $db->query(
                "UPDATE oc_customer SET email='$email',firstname='$firstname',lastname='$lastname',telephone='$phone' WHERE customer_id = '$id'"
            );
            if($val){
                return new Response('ok',1);
            }
            return new Response('update unsuccessful',-1);
        }else{
            return new Response("$email is associated with another account",-1);
        }

    }

    $postRoutes = [
        'wishlist'=>function(){
            if ($_SERVER['REQUEST_METHOD']=='POST'){
                $customer_id = $_POST['customerId'];
                $product_id = $_POST['productId'];
                if($customer_id != null && $product_id != null){
                    echo json_encode(putWishlistProducts($customer_id,$product_id));
                }
                else{
                    echo json_encode(new Response('customerId and productId cannot be null',-1));
                }
            }else{
                echo json_encode(new Response('Invalid method',-1));
            }
        },
        'account/edit'=>function(){
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $email = $_POST['email'];
                $firstname = $_POST['firstname'];
                $lastname = $_POST['lastname'];
                $phone = $_POST['phone'];
                $id = $_POST['customerId'];
                if($id != null && $email != null && $firstname != null && $lastname != null && $phone != null){
                    echo json_encode(updateCustomer($id,$email,$firstname,$lastname,$phone));
                }else{
                    echo json_encode(new Response('firstname,lastname,email and phone cannot be null',-1));
                }
            }else{
                echo json_encode(new Response('Invalid method',-1));
            }
        }
    ];

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
            if ($_SERVER['REQUEST_METHOD'] == 'GET'){
                $customer_id = $_GET['customerId'];
                if($customer_id != null){
                    echo json_encode(getWishlistProducts($customer_id));
                }else{
                    echo json_encode(new Response('customerId is null',-1));
                }
            }else{
                echo json_encode(new Response('invalid method',-1));
            }
        },
        'customerId'=>function(){
            if($_SERVER['REQUEST_METHOD'] == 'GET'){
                $email = $_GET['email'];
                if($email != null){
                    echo json_encode(getCustomerId($email));
                }else{
                    echo json_encode(new Response('email is null',-1));
                }
            }else{
                echo json_encode(new Response('invalid method',-1));
            }
        }
    ];

    $getRoute = $_GET['route'];
    $postRoute = $_POST['route'];

    if($_SERVER['REQUEST_METHOD'] == 'GET'){
        $func = $getRoutes[$getRoute];
        if($func != null){
            $func();
        }else{
            echo json_encode(new Response('Route '.$getRoute.' is not defined',-1));
        }
    }elseif($_SERVER['REQUEST_METHOD'] == 'POST'){
        $func = $postRoutes[$postRoute];
        if($func != null){
            $func();
        }else{
            echo json_encode(new Response('Route '.$postRoute.' is not defined',-1));
        }
    }else{
        echo json_encode(new Response('Invalid Http Method',-1));
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

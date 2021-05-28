<?php
    namespace DB;
    require 'mysqli.php';
    $db = new MySQLi("0.0.0.0","root","pothukuchi","softij11_oc1");
    // ===================================================================================================
    // GET DB Functions

    function getProducts($numRows,$start){
        global $db;
        if($start == null){
            $start = 3647;
        }
        $p = 'oc_product';
        $d = 'oc_product_description';
        $val = $db->query(
            "SELECT oc_product.image,$p.product_id,$d.name,$d.description,$p.model,$p.quantity,$p.price,$p.date_added,$p.viewed 
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
            "SELECT oc_product.image,oc_product.product_id,name,description,oc_product.model,
            viewed,oc_product.date_added,quantity,price,
            oc_customer_wishlist.date_added AS addedToListOn
            FROM oc_product 
            JOIN oc_customer_wishlist 
            ON oc_customer_wishlist.customer_id = $customer_id and 
            oc_customer_wishlist.product_id = oc_product.product_id
            JOIN oc_product_description
            ON oc_product.product_id = oc_product_description.product_id");
        $res = new Response('ok ', 1, $val->rows,$val->num_rows);
        return $res;
    }

    function getCurrentOrders($id){
        global $db;
        $val = $db->query(
            "SELECT oc_product.product_id,oc_product.image,oc_product_description.name,oc_product_description.description,
            oc_order_product.model,oc_order_product.quantity,oc_order_product.price,oc_order.total,oc_order.order_status_id,oc_order.date_added
            FROM oc_order 
            JOIN oc_order_product ON oc_order.customer_id = $id AND oc_order_product.order_id = oc_order.order_id
            JOIN oc_product_description ON oc_order_product.product_id = oc_product_description.product_id
            JOIN oc_product ON oc_order_product.product_id = oc_product.product_id "
        );
        $res = new Response('ok', 1, $val->rows,$val->num_rows);
        return $res;

    }

    function count($query){
        global $db;
        $val = $db->query(
            "SELECT COUNT(product_id) AS listSize FROM oc_product_description WHERE `name` LIKE '%$query%'"
        );
        return new Response('ok', 1, $val->rows,$val->num_rows);
    }

    function search($query,$limit,$start){
        global $db;
        $p = 'oc_product';
        $d = 'oc_product_description';
        $start = $start == null ? 1:$start;
        $val = $db->query(
            "SELECT oc_product.image,$p.product_id,$d.name,$d.description,$p.model,$p.quantity,$p.price,$p.date_added,$p.viewed 
            FROM oc_product INNER JOIN oc_product_description
            ON $p.product_id = $d.product_id WHERE $d.name LIKE '%$query%' AND oc_product.product_id >= $start ORDER BY $p.product_id LIMIT $limit"
        );
        return new Response('ok', 1, $val->rows,$val->num_rows);
    }

    function getZoneIds(){
        global $db;
        $val = $db->query(
            " SELECT * FROM `oc_zone` WHERE country_id=99 "
        );
        $res = new Response('ok', 1, $val->rows,$val->num_rows);
        return $res;
    }

    function getAddress($customerId){
        global $db;
        $val = $db->query(
            "SELECT * FROM oc_address JOIN oc_zone ON customer_id = $customerId AND oc_zone.zone_id = 4231;"
        );
        return new Response('ok',1,$val->rows,$val->num_rows);
    }

    function getAddressById($id){
        global $db;
        $val = $db->query(
            "SELECT * FROM oc_address WHERE address_id = $id"
        );
        if($val->num_rows > 0){
            return new Response('ok',1,$val->rows,$val->num_rows);
        }
        return new Response('invlalid Address Id',-1);
    }  

    function inWishlist($productId,$customerId){
        global $db;
        $val = $db->query(
            "SELECT * FROM oc_customer_wishlist WHERE customer_id = $customerId AND product_id = $productId"
        );
        if($val->num_rows > 0){
            return new Response('ok',1,$val->rows,$val->num_rows);
        }
        return new Response('no product found',-1);
    }

    function getCustomerId($email){
        global $db;
        $val = $db->query(
            "SELECT customer_id,store_id,firstname,lastname,email,address_id,telephone,ip,fax,date_added,newsletter FROM oc_customer WHERE email = '$email'"
        );
        if($val->num_rows > 0){
            return new Response('ok',1,$val->rows,$val->num_rows);
        }
        return new Response('no user found',-1);
    }

    function getAccInfo($id){
        global $db;
        $val = $db->query(
            "SELECT customer_id,store_id,firstname,lastname,email,address_id,telephone,ip,fax,date_added,newsletter FROM oc_customer WHERE customer_id = '$id'"
        );
        if($val->num_rows > 0){
            return new Response('ok',1,$val->rows,$val->num_rows);
        }
        return new Response("$id is invalid",-1);
    }

// =======================================================================================================
// POST DB Functions

    function putWishlistProducts($customer_id,$product_id){
        global $db;
        $val = $db->query(
            "insert into oc_customer_wishlist (customer_id,product_id) values($customer_id,$product_id)"
        );
        if($val){
            return new Response('OK added to wishlist',1);
        }
        return new Response('product is not added to wishlist',-1);
    }

    function removefromWishList($customer_id,$product_id){
        global $db;
        $val = $db->query(
            "DELETE FROM oc_customer_wishlist WHERE customer_id = $customer_id AND product_id = $product_id"
        );
        if($val){
            return new Response("OK removed from wishlist",1);
        }
        return new Response("product not removed from wishlist",-1);
    }


    function deleteAddress($addressId){
        global $db;
        $val = $db->query(
            "DELETE FROM oc_address WHERE address_id = $addressId"
        );
        if($val){
            return new Response("OK Address Removed",1);
        }
        return new Response("Address not removed",-1);
    }

    function editAddress($addressId,$first,$last,$address1,$address2,$company,$city,$postcode,$zoneId){
        global $db;
        $val = $db->query(
            "UPDATE oc_address 
            SET firstname = '$first', lastname = '$last',address_1 = '$address1',address_2 ='$address2',company = '$company',city = '$city',postcode = '$postcode',zone_id = '$zoneId'
            WHERE address_id = $addressId"
        );
        if($val){
            return new Response("OK Address Updated",1);
        }
        return new Response("Edit unsuccessful",-1);
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

    function putInCart($customerId,$productId,$quantity){
        global $db;
        $val = $db->query(
            "INSERT INTO oc_cart(`api_id`,`customer_id`,`session_id`,`product_id`,`recurring_id`,`option`,`quantity`)
            VALUES('0','$customerId','0','$productId','0','[]','$quantity');"
        );
        if($val){
            return new Response("product added to cart",1);
        }
        return new Reponse("failed to add product to cart",-1);
    }
    
    function updateSub($customerId){
        global $db;
        $stat = getAccInfo($customerId)->result[0]['newsletter'];
        $state = $stat == "0" ? 1:0;
        $val = $db->query(
            "UPDATE oc_customer SET newsletter = $state WHERE customer_id = '$customerId'"
        );
        if($val){
            return new Response("OK",1);
        }
        return new Reponse("failed to change the state",-1);
    }

    function updateView($productId){
        global $db;
        $val = $db->query(
            "SELECT viewed from oc_product WHERE product_id = $productId"
        );
        if($val->num_rows > 0){
            $views = $val->row['viewed'] + 1;
            $update = $db->query(
                "UPDATE oc_product SET viewed = $views WHERE product_id = $productId"
            );
            if($update){
                return new Response("OK",1,['viewed'=>$views]);
            }
            return new Reponse("failed to update view count",-1);
        }
        return new Reponse("Invlaid productId",-1);
    }

    function putAddress($customer_id,$first,$last,$address1,$address2,$company,$city,$postcode,$zoneId){
        global $db;
        $customer = getAccInfo($customer_id);
        if($customer->code > 0){
            $val = $db->query(
                "INSERT INTO oc_address (customer_id,firstname,lastname,company,address_1,address_2,city,postcode,country_id,zone_id)
                VALUES('$customer_id','$first','$last','$company','$address1','$address2','$city','$postcode','99','$zoneId');
                "
            );
            if($val){
                if($customer->result[0]['address_id'] == 0){
                    $addressId = getAddress($customer_id)->result[0]['address_id'];
                    $db->query(
                        "UPDATE oc_customer SET address_id = $addressId WHERE customer_id = $customer_id"
                    );
                }
                return new Response("address added",1);
            }
            return new Response("unable to add address",-1);
        }
        return $customer;
    }


    // ===================================================================================================

    $postRoutes = [
        'account/wishlist'=>function(){
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
        'account/wishlist/remove'=>function(){
            if ($_SERVER['REQUEST_METHOD']=='POST'){
                $customer_id = $_POST['customerId'];
                $product_id = $_POST['productId'];
                if($customer_id != null && $product_id != null){
                    echo json_encode(removefromWishList($customer_id,$product_id));
                }
                else{
                    echo json_encode(new Response('customerId and productId cannot be null',-1));
                }
            }else{
                echo json_encode(new Response('Invalid method',-1));
            }
        },
        'account/update/sub'=>function(){
            if ($_SERVER['REQUEST_METHOD']=='POST'){
                $customer_id = $_POST['customerId'];
                if($customer_id != null){
                    echo json_encode(updateSub($customer_id));
                }
                else{
                    echo json_encode(new Response('customerId cannot be null',-1));
                }
            }else{
                echo json_encode(new Response('Invalid method',-1));
            }
        },
        'update/views'=>function(){
            if ($_SERVER['REQUEST_METHOD']=='POST'){
                $product_id = $_POST['productId'];
                if($product_id != null){
                    echo json_encode(updateView($product_id));
                }
                else{
                    echo json_encode(new Response('productId cannot be null',-1));
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
        },
        'account/address'=>function(){
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $id = $_POST['customerId'];
                $first = $_POST["firstname"];
                $last = $_POST["lastname"];
                $address1 = $_POST['address1'];
                $address2 = $_POST['address2'];
                $city = $_POST['city'];
                $postcode = $_POST['postcode'];
                $zoneId = $_POST['zoneId'];
                $company = $_POST['company'];
                $company = $company == null ? "Empty" : $company;
                if($id != null && $address1 != null && $address2 != null && $city != null && $postcode != null && $zoneId != null){
                    echo json_encode(putAddress($id,$first,$last,$address1,$address2,$company,$city,$postcode,$zoneId));
                }else{
                    echo json_encode(new Response("Empty form data",-1));
                }
            }else{
                echo json_encode(new Response('Invalid method',-1));
            }
        },
        'account/address/edit' =>function(){
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $id = $_POST['addressId'];
                $address1 = $_POST['address1'];
                $address2 = $_POST['address2'];
                $first = $_POST["firstname"];
                $last = $_POST["lastname"];
                $city = $_POST['city'];
                $postcode = $_POST['postcode'];
                $zoneId = $_POST['zoneId'];
                $company = $_POST['company'];
                $company = $company == null ? "Empty" : $company;
                if($id != null && $address1 != null && $address2 != null && $city != null && $postcode != null && $zoneId != null){
                    echo json_encode(editAddress($id,$first,$last,$address1,$address2,$company,$city,$postcode,$zoneId));
                }else{
                    echo json_encode(new Response("Empty form data",-1));
                }
            }else{
                echo json_encode(new Response('Invalid method',-1));
            }
        },
        'account/address/delete'=>function(){
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $id = $_POST['addressId'];
                if($id != null){
                    echo json_encode(deleteAddress($id));
                }else{
                    echo json_encode(new Response("Empty form data",-1));
                }
            }else{
                echo json_encode(new Response('Invalid method',-1));
            }
        },
        'checkout'=>function(){
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $customerId = $_POST['customerId'];
                $productId = $_POST['productId'];
                $quantity = $_POST['quantity'];
                if($customerId != null && $productId != null && $quantity != null ){
                    echo json_encode(putInCart($customerId,$productId,$quantity));
                }else{
                    echo json_encode(new Response("Empty form data",-1));
                }
            }else{
                echo json_encode(new Response('Invalid method',-1));
            }
        }
    ];

    $getRoutes = [
        'list/products'=>function(){
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
        'search'=>function(){
            if($_SERVER['REQUEST_METHOD'] == 'GET'){
                $query = $_GET['query'];
                $start = $_GET['start'];
                $limit = $_GET['limit'];
                if($query != null && $limit != null){
                    echo json_encode(search($query,$limit,$start));
                }else{
                    echo json_encode(new Response('query is null or limit is null',-1));
                }
            }else{
                echo json_encode(new Response('invalid method',-1));
            }            
        },
        'list/count'=>function(){
            if($_SERVER['REQUEST_METHOD'] == 'GET'){
                $query = $_GET['query'];
                if($query != null){
                    echo json_encode(count($query));
                }else{
                    echo json_encode(new Response('query is null',-1));
                }
            }else{
                echo json_encode(new Response('invalid method',-1));
            } 
        },
        'account/wishlist'=>function(){
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
        'account/customerId'=>function(){
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
        },
        'account/info'=>function(){
            if($_SERVER['REQUEST_METHOD'] == 'GET'){
                $id = $_GET['customerId'];
                if($id != null){
                    echo json_encode(getAccInfo($id));
                }else{
                    echo json_encode(new Response('customerId is null',-1));
                }
            }else{
                echo json_encode(new Response('invalid method',-1));
            }
        },
        'account/address'=>function(){
            if($_SERVER['REQUEST_METHOD'] == 'GET'){
                $id = $_GET['customerId'];
                if($id != null){
                    echo json_encode(getAddress($id));
                }else{
                    echo json_encode(new Response('CustomerId is null',-1));
                }
            }else{
                echo json_encode(new Response('invalid method',-1));
            }
        },
        'account/orders'=>function(){
            if($_SERVER['REQUEST_METHOD'] == 'GET'){
                $id = $_GET['customerId'];
                if($id != null){
                    echo json_encode(getCurrentOrders($id));
                }else{
                    echo json_encode(new Response('CustomerId is null',-1));
                }
            }else{
                echo json_encode(new Response('invalid method',-1));
            }
        },
        'list/zoneId'=>function(){
            if($_SERVER['REQUEST_METHOD'] == 'GET'){
                echo json_encode(getZoneIds());
            }else{
                echo json_encode(new Response('invalid method',-1));
            }
        },
        'account/wishlist/id'=>function(){
            if($_SERVER['REQUEST_METHOD'] == 'GET'){
                $customerId = $_GET['customerId'];
                $productId = $_GET['productId'];
                if($customerId != null && $productId != null){
                    echo json_encode(inWishlist($productId,$customerId));
                }else{
                    echo json_encode(new Response('customerId is null or productId',-1));
                }
            }else{
                echo json_encode(new Response('invalid method',-1));
            }
        }
        // 'account/pastOrders'=>function(){
        //     if($_SERVER['REQUEST_METHOD'] == 'GET'){
        //         $id = $_GET['customerId']
        //         if($id != null){
        //             echo json_encode(getPastOrders($id) );
        //         }else{
        //             echo json_encode(new Response('CustomerId is null',-1));
        //         }
        //     }else{
        //         echo json_encode(new Response('invalid method',-1));
        //     }
        // }
    ];

    $getRoute = $_GET['route'];
    $postRoute = $_POST['route'];
    try{
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
    }catch(Exception $e){
        echo json_encode(new Response("Server Error",-500,$e,-1));
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

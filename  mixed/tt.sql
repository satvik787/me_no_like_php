SELECT oc_product.product_id,name,description,model
quantity,price,oc_customer_wishlist.date_added AS DateAdded
FROM oc_product 
JOIN oc_customer_wishlist 
ON oc_customer_wishlist.customer_id = 2 and oc_customer_wishlist.product_id = oc_product.product_id
JOIN oc_product_description
ON oc_product.product_id = oc_product_description.product_id;

SELECT oc_product_description.name,oc_product_description.description,
oc_order_product.model,oc_order_product.quantity,oc_order_product.price,oc_order.total
FROM oc_order 
JOIN oc_order_product ON oc_order.customer_id = 31 AND oc_order_product.order_id = oc_order.order_id
JOIN oc_product_description ON oc_order_product.product_id = oc_product_description.product_id

INSERT INTO oc_cart(api_id,customer_id,session_id,product_id,recurring_id,quantity)
VALUES ('0','2','0','5033','0','1');
<?php

class shopExtrapostPlugin extends shopPlugin
{
	public function extrapost ($params) {
		
		$order = $this->getOrder($params['order_id']);
        
        if ($order['params']['shipping_id'] == '215') {
	        	$json = $this->jsonCreate($order);
				$token = '6f8bc3d2d29e58272b3c77a78c27a5e2';
				$result = $this->postCurl($json, $token);
        }
		
		// waLog::dump($order, 'shop/testplug/order-actions/create.log');
	}

	public function postCurl ($json, $token) {

		 $ch = curl_init('https://xp.extrapost.ru/api/v1/orders');
		 curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		 curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		 curl_setopt($ch, CURLOPT_HTTPHEADER,
		   array( 'Content-Type: application/json',
		          'Authorization: Token token="' . $token . '"'));

		 
		 $result = curl_exec($ch);
		 $result1 = json_decode($result, true);
		 
		 return $result1;
	
	}

	public function getOrder ($id) {
		$model = new shopOrderModel;
		$order = $model->getOrder($id);

		return $order;
	}

	public function jsonCreate ($order) {
		$items = array();
		foreach ($order['items'] as $item) {
			$items[] = array (
				'product_sku' 	=> 'vmp' . $item['sku_code'],
				'product_title'	=> $item['name'],
				'price'			=> $item['price'],
				'quantity'		=> $item['quantity'],
			);
		}



		 $json = array (


			'order' => array(
				'identifier' 	=> $order['id'],
				'created_at' 	=> $order['create_datetime'],
				'store_domain'	=> 'store.smazka.ru',
				'zip'			=> $order['params']['shipping_address.zip'],
				'region'		=> $order['params']['shipping_address.region'],
				'town'			=> $order['params']['shipping_address.city'],
				'street'		=> $order['params']['shipping_address.street'],
				'name'			=> $order['contact']['name'],
				'email'			=> $order['contact']['email'],
				'phone'			=> $order['contact']['phone'],
				'comment'		=> $order['comment'],
				'price'			=> $order['total'],
				'prepaid'		=> false,
				'line_items'	=> $items,

			)
		);



		return json_encode($json);
	}
}

<?php
//
// A class to provide the cURL utilities for parcels and similar things.
//
// Copyright 2015 InPost UK Ltd. All right reserved.
//
// Please note that this software is provided 'as is' and we make no promises
// about it.
//

class InpostParcelsTools extends ObjectModel
{
	public static function connectInpostparcels($params = array())
	{
		$params = array_merge(
			array(
				'url' => $params['url'],
				'token' => Configuration::get('IP_UK_API_KEY'),
				'ds' => '?',
				'methodType' => $params['methodType'],
				'params' => $params['params']
			),
			$params
		);

		$ch = curl_init();

		// Switch cURL to not worry about SSL certificate checking.
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		switch($params['methodType'])
		{
			case 'GET':
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-HTTP-Method-Override: GET') );
			$getParams = null;
			if(!empty($params['params'])){
			foreach($params['params'] as $field_name => $field_value){
			$getParams .= $field_name.'='.urlencode($field_value).'&';
			}
			curl_setopt($ch, CURLOPT_URL, $params['url'].$params['ds'].'token='.$params['token'].'&'.$getParams);
			}else{
			curl_setopt($ch, CURLOPT_URL, $params['url'].$params['ds'].'token='.$params['token']);
			}
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
			break;

			case 'POST':
			$string = json_encode($params['params']);
			#$string = $params['params'];
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-HTTP-Method-Override: POST') );
			curl_setopt($ch, CURLOPT_URL, $params['url'].$params['ds'].'token='.$params['token']);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $string);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($string))
			);
			break;

			case 'PUT':
			$string = json_encode($params['params']);
			#$string = $params['params'];
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-HTTP-Method-Override: PUT') );
			curl_setopt($ch, CURLOPT_URL, $params['url'].$params['ds'].'token='.$params['token']);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $string);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
                        'Content-Length: ' . strlen($string))
			);
			break;
		}

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$ret = curl_exec($ch);
		$ret = json_decode($ret);

		return array(
		'result' => $ret,
		'info' => curl_getinfo($ch),
		'errno' => curl_errno($ch),
		'error' => curl_error($ch)
		);
	}

	public static function generate($type = 1, $length)
	{
		$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";

		if($type == 1)
		{
			# AZaz09
			$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
		}
		elseif($type == 2)
		{
			# az09
			$chars = "abcdefghijklmnopqrstuvwxyz1234567890";
		}
		elseif($type == 3)
		{
			# AZ
			$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		}
		elseif($type == 4)
		{
			# 09
			$chars = "0123456789";
		}

		$token = "";
		for ($i = 0; $i < $length; $i++)
		{
			$j = rand(0, strlen($chars) - 1);
			if($i==0 && $j == 0){
			$j = rand(2,9);
			}
			$token .= $chars[$j];
		}
		return $token;
	}

	public function getParcelStatus()
	{
		return array(
		'Created' => 'Created',
		'Prepared' => 'Prepared'
		);
	}

	public function calculateDimensions($product_dimensions = array(), $config = array())
	{
		$parcelSize = 'A';
		$is_dimension = true;

		if(!empty($product_dimensions))
		{
			$maxDimensionFromConfigSizeA = explode('x', strtolower(trim($config['MAX_DIMENSION_A'])));
			$maxWidthFromConfigSizeA = (float)trim(@$maxDimensionFromConfigSizeA[0]);
			$maxHeightFromConfigSizeA = (float)trim(@$maxDimensionFromConfigSizeA[1]);
			$maxDepthFromConfigSizeA = (float)trim(@$maxDimensionFromConfigSizeA[2]);
			// flattening to one dimension
			$maxSumDimensionFromConfigSizeA = $maxWidthFromConfigSizeA + $maxHeightFromConfigSizeA + $maxDepthFromConfigSizeA;

			$maxDimensionFromConfigSizeB = explode('x', strtolower(trim($config['MAX_DIMENSION_B'])));
			$maxWidthFromConfigSizeB = (float)trim(@$maxDimensionFromConfigSizeB[0]);
			$maxHeightFromConfigSizeB = (float)trim(@$maxDimensionFromConfigSizeB[1]);
			$maxDepthFromConfigSizeB = (float)trim(@$maxDimensionFromConfigSizeB[2]);
			// flattening to one dimension
			$maxSumDimensionFromConfigSizeB = $maxWidthFromConfigSizeB + $maxHeightFromConfigSizeB + $maxDepthFromConfigSizeB;

			$maxDimensionFromConfigSizeC = explode('x', strtolower(trim($config['MAX_DIMENSION_C'])));
			$maxWidthFromConfigSizeC = (float)trim(@$maxDimensionFromConfigSizeC[0]);
			$maxHeightFromConfigSizeC = (float)trim(@$maxDimensionFromConfigSizeC[1]);
			$maxDepthFromConfigSizeC = (float)trim(@$maxDimensionFromConfigSizeC[2]);

			if($maxWidthFromConfigSizeC == 0 || $maxHeightFromConfigSizeC == 0 || $maxDepthFromConfigSizeC == 0)
			{
				// bad format in admin configuration
				$is_dimension = false;
			}
			// flattening to one dimension
			$maxSumDimensionFromConfigSizeC = $maxWidthFromConfigSizeC + $maxHeightFromConfigSizeC + $maxDepthFromConfigSizeC;
			$maxSumDimensionsFromProducts = 0;
			foreach($product_dimensions as $product_dimension)
			{
				$dimension = explode('x', $product_dimension);
				$width = trim(@$dimension[0]);
				$height = trim(@$dimension[1]);
				$depth = trim(@$dimension[2]);
				if($width == 0 || $height == 0 || $depth)
				{
				// empty dimension for product
				continue;
				}

                if(
                    $width > $maxWidthFromConfigSizeC ||
                    $height > $maxHeightFromConfigSizeC ||
                    $depth > $maxDepthFromConfigSizeC
                ){
                    $is_dimension = false;
                }

                $maxSumDimensionsFromProducts = $maxSumDimensionsFromProducts + $width + $height + $depth;
                if($maxSumDimensionsFromProducts > $maxSumDimensionFromConfigSizeC){
                    $is_dimension = false;
                }
            }
			if($maxSumDimensionsFromProducts <= $maxDimensionFromConfigSizeA)
			{
                $parcelSize = 'A';
            }elseif($maxSumDimensionsFromProducts <= $maxDimensionFromConfigSizeB){
                $parcelSize = 'B';
            }elseif($maxSumDimensionsFromProducts <= $maxDimensionFromConfigSizeC){
				$parcelSize = 'C';
			}
		}

		return array(
			'parcelSize' => $parcelSize,
			'isDimension' => $is_dimension
		);
	}

	///
	// getCurrentApi
	//
	public static function getCurrentApi()
	{
		$currentApi = 'UK';

		return $currentApi;
	}

	///
	// getGeowidgetUrl
	//
	public static function getGeowidgetUrl()
	{
		switch(self::getCurrentApi())
		{
			case 'UK':
				return 'https://geowidget.inpost.co.uk/dropdown.php?field_to_update=name&field_to_update2=address&user_function=user_function';
				break;
			case 'PL':
				return 'https://geowidget.inpost.pl/dropdown.php?field_to_update=name&field_to_update2=address&user_function=user_function';
				break;
		}
	}

	///
	// get_machine_address
	//
	// @param The locker machine to lookup.
	// @brief Get the machines address.
	//
	// @return A mixed array with the address details in it.
	//
	public static function get_machine_address($id)
	{
		$params = array(
			'url' => Configuration::get('IP_UK_API_URL').'machines/'.$id,
			'methodType' => 'GET',
			'params'     => array(),
		);

		$ret = InpostParcelsTools::connectInpostparcels($params);

		$info = $ret['info'];

		$return['street']          = '';
		$return['city']            = '';
		$return['building_number'] = '';

		if ($info['http_code'] == 200)
		{
			// Get the address details of the machine.
			$return['street'] = $ret['result']->address->street;
			$return['city']   = $ret['result']->address->city;
			if(isset($ret['result']->address->building_number))
			{
				$return['building_number'] = $ret['result']->address->building_number;
			}
		}

		return $return;
	}

	///
	// create_new_parcel
	//
	// @param The details of the parcel to be created, array.
	//
	// @return True if processed OK and false otherwise
	//
	public static function create_new_parcel(&$parcel_details, $error)
	{
		$error = '';

		$parcel_details['parcel_tmp_id'] = self::generate(4, 15);

		$params = array(
			'url' => Configuration::get('IP_UK_API_URL').'parcels',
			'methodType' => 'POST',
			'params'     => array(
				'description' => $parcel_details['parcel_description'],
				'receiver'    => array(
					'email' => $parcel_details['parcel_receiver_email'],
					'phone' => $parcel_details['parcel_receiver_phone']
				),
				'size'        => $parcel_details['parcel_size'],
				'tmp_id'      => $parcel_details['parcel_tmp_id'],
				'target_machine' => $parcel_details['parcel_target_machine_id'],
			)
		);

		$ret = InpostParcelsTools::connectInpostparcels($params);

		$info = $ret['info'];

		if ($info['http_code'] != 201)
		{
			// An error has occured.
			$error = 'Error code : '.$info['http_code'].' '.$ret['result'];

			return false;
		}

		$parcel_details['parcel_id'] = $ret['result']->id;

		// TODO
		// PAY for / confirm the parcel.
		//
		fred;
		return true;
	}

	///
	// inpost_update_parcel
	//
	// @param The details of the parcel to be updated, array.
	//
	// @return True if processed OK and false otherwise
	//
	public static function inpost_update_parcel(&$parcel_details, $error)
	{
		$error = '';

		$params = array(
			'url' => Configuration::get('IP_UK_API_URL').'parcels',
			'methodType' => 'PUT',
			'params'     => array(
				'id'          => $parcel_details['parcel_id'],
				'description' => $parcel_details['parcel_description'],
				'size'        => $parcel_details['parcel_size'],
				'status'      => $parcel_details['parcel_status'],
			)
		);

		$ret = InpostParcelsTools::connectInpostparcels($params);

		$info = $ret['info'];
d($ret);
		if ($info['http_code'] != 204)
		{
			// An error has occured.
			$error = 'Error code : '.$info['http_code'].' '.$ret['result'];

			return false;
		}

		return true;
	}

}

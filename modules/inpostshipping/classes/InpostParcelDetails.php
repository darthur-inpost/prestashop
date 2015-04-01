<?php
/*
*/

class InpostParcelDetails extends ObjectModel
{
	/// @var 
	public $id_inpostshipping;
	public $order_id;
	public $parcel_id;
	public $parcel_status;
	public $parcel_description;
	public $parcel_receiver_email;
	public $parcel_receiver_phone;
	public $parcel_size;
	public $parcel_tmp_id;
	public $parcel_target_machine_id;
	public $parcel_target_machine_detail;
	public $parcel_target_machine_town;
	public $parcel_target_machine_street;
	public $parcel_target_machine_building;
	public $sticker_creation_date;
	public $creation_date;
	public $api_source;
	public $variables;
	public $return_parcel_id;
	public $return_parcel_expiry;
	public $return_parcel_created;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table'   => 'inpostshipping',
		'primary' => 'id_inpostshipping',
		'fields'  => array(
			'order_id' => array(
				'type'     => self::TYPE_INT,
				'validate' => 'isUnsignedId',
				'required' => true
			),
			'parcel_id' => array(
				'type'     => self::TYPE_STRING,
				'size' => 200,
			),
			'parcel_status' => array(
				'type' => self::TYPE_STRING,
				'validate' => 'isName',
				'size' => 200,
			),
			'parcel_description' => array(
				'type' => self::TYPE_STRING,
				'validate' => 'isString',
				'size' => 200,
			),
			'parcel_receiver_email' => array(
				'type' => self::TYPE_STRING,
				'validate' => 'isEmail',
				'size' => 200,
			),
			'parcel_receiver_phone' => array(
				'type' => self::TYPE_STRING,
				'validate' => 'isPhoneNumber',
				'size' => 30,
			),
			'parcel_size' => array(
				'type' => self::TYPE_STRING,
				'validate' => 'isString',
				'size' => 2,
			),
			'parcel_tmp_id' => array(
				'type' => self::TYPE_STRING,
				'validate' => 'isString',
				'size' => 100,
			),
			'parcel_target_machine_id' => array(
				'type' => self::TYPE_STRING,
				'validate' => 'isString',
				'required' => true,
				'size' => 200
			),
			'parcel_target_machine_town' => array(
				'type' => self::TYPE_STRING,
				'validate' => 'isString',
				'size' => 100
			),
			'parcel_target_machine_street' => array(
				'type' => self::TYPE_STRING,
				'validate' => 'isString',
				'size' => 100
			),
			'parcel_target_machine_building' => array(
				'type' => self::TYPE_STRING,
				'validate' => 'isString',
				'size' => 100
			),
			'sticker_creation_date' => array(
				'type' => self::TYPE_DATE,
				'validate' => 'isDate'
			),
			'creation_date' => array(
				'type' => self::TYPE_DATE,
				'validate' => 'isDate'
			),
			'api_source' =>	array(
				'type' => self::TYPE_STRING,
				'size' => 200
			),
			'variables' => array(
				'type' => self::TYPE_STRING,
			),
			'return_parcel_id' => array(
				'type' => self::TYPE_STRING,
				'size' => 50
			),
			'return_parcel_expiry' => array(
				'type' => self::TYPE_DATE,
				'validate' => 'isDate'
			),
			'return_parcel_created' => array(
				'type' => self::TYPE_DATE,
				'validate' => 'isDate'
			),
		),
	);

	public function __construct($id = null)
	{
		parent::__construct($id);
	}

	///
	// check_inpost_cart_order_exists
	//
	// @param The cart ID to search for
	// @brief Check that the possible order exists for the cart id
	//
	public static function check_inpost_cart_order_exists($id)
	{
		$ids = array();

		$sub_query = new DbQuery();
		$sub_query->select('i.`id_inpostshipping`');
		$sub_query->from('inpostshipping', 'i');
		$sub_query->where('i.`parcel_tmp_id` = \'' . $id . '\'');
		$sub_query->where('i.`parcel_status` = \'Possible\'');

		if ($result = Db::getInstance()->getRow($sub_query))
		{
			$ids[] = $result['id_inpostshipping'];
		}

		return $ids;
	}

	///
	// update
	//
	// @brief Update the central system if needed.
	//
	public function update($null_values = false)
	{
		//d($this->variables);

		return parent::update($null_values);
	}

	///
	// update
	//
	// @brief Update the central system is needed.
	//
	public function my_update($data_values)
	{
		//d($this->parcel_status);

		$sql = 'UPDATE `'._DB_PREFIX_.'inpostshipping`
			SET `parcel_id` = \''.(string)$data_values['parcel_id'].'\'
			, `parcel_description` = \''.(string)$data_values['parcel_description'].'\'
			, `parcel_receiver_email` = \''.(string)$data_values['parcel_receiver_email'].'\'
			, `parcel_receiver_phone` = \''.(string)$data_values['parcel_receiver_phone'].'\'
			, `parcel_size` = \''.(string)$data_values['parcel_size'].'\'
			, `parcel_tmp_id` = \''.(string)$data_values['parcel_tmp_id'].'\'
			, `parcel_target_machine_id` = \''.(string)$data_values['parcel_target_machine_id'].'\'
			WHERE `id_inpostshipping` = '.(int)$data_values['id_inpostshipping'];

		$return = Db::getInstance()->execute($sql);

		return $return;
	}

}

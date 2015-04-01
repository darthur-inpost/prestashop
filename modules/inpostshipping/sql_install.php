<?php
//
// 2015 InPost UK Ltd.
//
// NOTICE OF LICENSE
//
// This source file is created and maintained by InPost. It is provided 'as is'
// with no promises or guarentees.
//
// DISCLAIMER
//
// Do not edit or add to this file if you wish to upgrade.
//
// @copyright 2015 InPost UK Ltd.
//

$sql = array();
$sql[_DB_PREFIX_.'inpostshipping'] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'inpostshipping` (
		`id_inpostshipping` int(11) unsigned NOT NULL AUTO_INCREMENT,
		`order_id` int(11) NOT NULL,
		`parcel_id` varchar(200) NOT NULL DEFAULT \'\',
		`parcel_status` varchar(200) NOT NULL DEFAULT \'\',
		`parcel_description` varchar(200) NOT NULL DEFAULT \'\',
		`parcel_receiver_email` varchar(200) NOT NULL DEFAULT \'\',
		`parcel_receiver_phone` varchar(30) NOT NULL DEFAULT \'\',
		`parcel_size` varchar(2) NOT NULL DEFAULT \'\',
		`parcel_tmp_id` varchar(100) NOT NULL DEFAULT \'\',
		`parcel_target_machine_id` varchar(200) NOT NULL DEFAULT \'\',
		`parcel_target_machine_town` varchar(100) NOT NULL DEFAULT \'\',
		`parcel_target_machine_street` varchar(100) NOT NULL DEFAULT \'\',
		`parcel_target_machine_building` varchar(100) NOT NULL DEFAULT \'\',
		`sticker_creation_date` TIMESTAMP NULL DEFAULT NULL,
		`creation_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`api_source` varchar(3) NOT NULL DEFAULT \'\',
		`variables` text NOT NULL DEFAULT \'\',
		`return_parcel_id` varchar(50) NOT NULL,
		`return_parcel_expiry` TIMESTAMP NOT NULL,
		`return_parcel_created` TIMESTAMP NOT NULL,
		PRIMARY KEY (`id_inpostshipping`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';


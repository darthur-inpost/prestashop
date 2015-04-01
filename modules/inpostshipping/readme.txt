Array
(
    [newOrderStatus] => OrderState Object
    (
            [name] => Awaiting check payment
            [template] => cheque
            [send_email] => 1
            [module_name] => cheque
            [invoice] => 0
            [color] => #4169E1
            [unremovable] => 1
            [logable] => 0
            [delivery] => 0
            [hidden] => 0
            [shipped] => 0
            [paid] => 0
            [pdf_invoice] => 0
            [pdf_delivery] => 0
            [deleted] => 0
            [webserviceParameters:protected] => Array
                (
                    [fields] => Array
                        (
                            [unremovable] => Array () 
                            [delivery] => Array () 
                            [hidden] => Array () 
                        ) 
                ) 
            [id] => 1
            [id_lang:protected] => 1
            [id_shop:protected] => 
            [id_shop_list] => 
            [get_shop_from_context:protected] => 1
            [table:protected] => order_state
            [identifier:protected] => id_order_state
            [fieldsRequired:protected] => Array () 
            [fieldsSize:protected] => Array () 
            [fieldsValidate:protected] => Array
                (
                    [send_email] => isBool
                    [module_name] => isModuleName
                    [invoice] => isBool
                    [color] => isColor
                    [logable] => isBool
                    [shipped] => isBool
                    [unremovable] => isBool
                    [delivery] => isBool
                    [hidden] => isBool
                    [paid] => isBool
                    [pdf_delivery] => isBool
                    [pdf_invoice] => isBool
                    [deleted] => isBool
                )
            [fieldsRequiredLang:protected] => Array ( [0] => name)
            [fieldsSizeLang:protected] => Array
                (
                    [name] => 64
                    [template] => 64
                )
            [fieldsValidateLang:protected] => Array
                (
                    [name] => isGenericName
                    [template] => isTplName
                ) 
            [tables:protected] => Array () 
            [image_dir:protected] => 
            [image_format:protected] => jpg
            [def:protected] => Array
                (
                    [table] => order_state
                    [primary] => id_order_state
                    [multilang] => 1
                    [fields] => Array
                        (
                            [send_email] => Array
                                (
                                    [type] => 2
                                    [validate] => isBool
                                )
                            [module_name] => Array
                                (
                                    [type] => 3
                                    [validate] => isModuleName
                                )
                            [invoice] => Array
                                (
                                    [type] => 2
                                    [validate] => isBool
                                )
                            [color] => Array
                                (
                                    [type] => 3
                                    [validate] => isColor
                                )
                            [logable] => Array
                                (
                                    [type] => 2
                                    [validate] => isBool
                                )
                            [shipped] => Array
                                (
                                    [type] => 2
                                    [validate] => isBool
                                )
                            [unremovable] => Array
                                (
                                    [type] => 2
                                    [validate] => isBool
                                )
                            [delivery] => Array
                                (
                                    [type] => 2
                                    [validate] => isBool
                                )
                            [hidden] => Array
                                (
                                    [type] => 2
                                    [validate] => isBool
                                )
                            [paid] => Array
                                (
                                    [type] => 2
                                    [validate] => isBool
                                )
                            [pdf_delivery] => Array
                                (
                                    [type] => 2
                                    [validate] => isBool
                                )
                            [pdf_invoice] => Array
                                (
                                    [type] => 2
                                    [validate] => isBool
                                )
                            [deleted] => Array
                                (
                                    [type] => 2
                                    [validate] => isBool
                                )
                            [name] => Array
                                (
                                    [type] => 3
                                    [lang] => 1
                                    [validate] => isGenericName
                                    [required] => 1
                                    [size] => 64
                                )
                            [template] => Array
                                (
                                    [type] => 3
                                    [lang] => 1
                                    [validate] => isTplName
                                    [size] => 64
                                )
                        )
                    [classname] => OrderState
                    [associations] => Array
                        (
                            [l] => Array
                                (
                                    [type] => 2
                                    [field] => id_order_state
                                    [foreign_field] => id_order_state
                                )
                        )
                )
            [update_fields:protected] => 
            [force_id] => 
        )
    [id_order] => 18
    [cookie] => Cookie Object
        (
            [_content:protected] => Array
                (
                    [date_add] => 2015-03-18 10:03:09
                    [id_lang] => 1
                    [id_currency] => 1
                    [last_visited_category] => 10
                    [check_cgv] => 1
                    [id_guest] => 2
                    [id_connections] => 9
                    [id_compare] => 0
                    [id_customer] => 2
                    [customer_lastname] => Arthur
                    [customer_firstname] => David
                    [logged] => 1
                    [is_guest] => 
                    [passwd] => ae3a761302c9d389056841678f203f7e
                    [email] => darthur@inpost.co.uk
                    [viewed] => 1
                    [id_cart] => 19
                    [checksum] => -1065272634
                )

            [_name:protected] => PrestaShop-42d71ccdbde431a9af735bab5d1ba278
            [_expire:protected] => 1429458451
            [_domain:protected] => 
            [_path:protected] => /presta/
            [_cipherTool:protected] => Rijndael Object
                (
                    [_key:protected] => NT6NsEVVsPeiUwe7NrW3hGkVdnaVykG7
                    [_iv:protected] => `R????)??bUa?P?
                )

            [_modified:protected] => 
            [_allow_writing:protected] => 1
            [_salt:protected] => ImmaQLQT
            [_standalone:protected] => 
            [_secure:protected] => 
        )

    [cart] => Cart Object
        (
            [id] => 19
            [id_shop_group] => 1
            [id_shop] => 1
            [id_address_delivery] => 5
            [id_address_invoice] => 5
            [id_currency] => 1
            [id_customer] => 2
            [id_guest] => 2
            [id_lang] => 1
            [recyclable] => 0
            [gift] => 0
            [gift_message] => 
            [mobile_theme] => 0
            [date_add] => 2015-03-30 16:46:53
            [secure_key] => b981d159f43db0f0492ba2d99aaf460d
            [id_carrier] => 15
            [date_upd] => 2015-03-30 16:47:20
            [checkedTos] => 
            [pictures] => 
            [textFields] => 
            [delivery_option] => a:1:{i:5;s:3:"15,";}
            [allow_seperated_package] => 0
            [_products:protected] => Array
                (
                    [0] => Array
                        (
                            [id_product_attribute] => 16
                            [id_product] => 4
                            [cart_quantity] => 1
                            [id_shop] => 1
                            [name] => Printed Dress
                            [is_virtual] => 0
                            [description_short] => <p>Printed evening dress with straight sleeves with black thin waist belt and ruffled linings.</p>
                            [available_now] => In stock
                            [available_later] => 
                            [id_category_default] => 10
                            [id_supplier] => 1
                            [id_manufacturer] => 1
                            [on_sale] => 0
                            [ecotax] => 0.000000
                            [additional_shipping_cost] => 0.00
                            [available_for_order] => 1
                            [price] => 50.994153
                            [active] => 1
                            [unity] => 
                            [unit_price_ratio] => 0.000000
                            [quantity_available] => 298
                            [width] => 0.000000
                            [height] => 0.000000
                            [depth] => 0.000000
                            [out_of_stock] => 2
                            [weight] => 0
                            [date_add] => 2015-03-18 09:58:44
                            [date_upd] => 2015-03-18 09:58:44
                            [quantity] => 1
                            [link_rewrite] => printed-dress
                            [category] => evening-dresses
                            [unique_id] => 000000000400000000165
                            [id_address_delivery] => 5
                            [advanced_stock_management] => 0
                            [supplier_reference] => 
                            [reduction_type] => 0
                            [customization_quantity] => 
                            [id_customization] => 
                            [price_attribute] => 0.000000
                            [ecotax_attr] => 0.000000
                            [reference] => demo_4
                            [weight_attribute] => 0.000000
                            [ean13] => 0
                            [upc] => 
                            [pai_id_image] => 10
                            [pai_legend] => 
                            [minimal_quantity] => 1
                            [wholesale_price] => 15.300000
                            [stock_quantity] => 298
                            [total] => 50.99
                            [total_wt] => 61.19
                            [price_wt] => 61.1929836
                            [id_image] => 4-10
                            [legend] => 
                            [reduction_applies] => 
                            [quantity_discount_applies] => 
                            [allow_oosp] => 0
                            [features] => Array
                                (
                                    [0] => Array
                                        (
                                            [id_feature] => 5
                                            [id_product] => 4
                                            [id_feature_value] => 3
                                        )

                                    [1] => Array
                                        (
                                            [id_feature] => 6
                                            [id_product] => 4
                                            [id_feature_value] => 16
                                        )

                                    [2] => Array
                                        (
                                            [id_feature] => 7
                                            [id_product] => 4
                                            [id_feature_value] => 19
                                        )

                                )

                            [attributes] => Size : S, Color : Beige
                            [attributes_small] => S, Beige
                            [rate] => 20
                            [tax_name] => VAT UK 20%
                        )

                )

            [_taxCalculationMethod:protected] => 0
            [webserviceParameters:protected] => Array (
                    [fields] => Array
                        (
                            [id_address_delivery] => Array ( [xlink_resource] => addresses) 
                            [id_address_invoice] => Array ( [xlink_resource] => addresses) 
                            [id_currency] => Array ( [xlink_resource] => currencies) 
                            [id_customer] => Array ( [xlink_resource] => customers) 
                            [id_guest] => Array ( [xlink_resource] => guests) 
                            [id_lang] => Array ( [xlink_resource] => languages) 
                        )

                    [associations] => Array
                        (
                            [cart_rows] => Array
                                (
                                    [resource] => cart_row
                                    [virtual_entity] => 1
                                    [fields] => Array
                                        (
                                            [id_product] => Array
                                                (
                                                    [required] => 1
                                                    [xlink_resource] => products
                                                )

                                            [id_product_attribute] => Array
                                                (
                                                    [required] => 1
                                                    [xlink_resource] => combinations
                                                )

                                            [id_address_delivery] => Array
                                                (
                                                    [required] => 1
                                                    [xlink_resource] => addresses
                                                )

                                            [quantity] => Array ( [required] => 1)

                                        )

                                )

                        )

                )

            [id_shop_list] => 
            [get_shop_from_context:protected] => 1
            [table:protected] => cart
            [identifier:protected] => id_cart
            [fieldsRequired:protected] => Array
                (
                    [0] => id_currency
                    [1] => id_lang
                )

            [fieldsSize:protected] => Array ( [secure_key] => 32) 
            [fieldsValidate:protected] => Array
                (
                    [id_shop_group] => isUnsignedId
                    [id_shop] => isUnsignedId
                    [id_address_delivery] => isUnsignedId
                    [id_address_invoice] => isUnsignedId
                    [id_carrier] => isUnsignedId
                    [id_currency] => isUnsignedId
                    [id_customer] => isUnsignedId
                    [id_guest] => isUnsignedId
                    [id_lang] => isUnsignedId
                    [recyclable] => isBool
                    [gift] => isBool
                    [gift_message] => isMessage
                    [mobile_theme] => isBool
                    [allow_seperated_package] => isBool
                    [date_add] => isDateFormat
                    [date_upd] => isDateFormat
                )

            [fieldsRequiredLang:protected] => Array () 
            [fieldsSizeLang:protected] => Array () 
            [fieldsValidateLang:protected] => Array () 
            [tables:protected] => Array () 
            [image_dir:protected] => 
            [image_format:protected] => jpg
            [def:protected] => Array
                (
                    [table] => cart
                    [primary] => id_cart
                    [fields] => Array
                        (
                            [id_shop_group] => Array
                                (
                                    [type] => 1
                                    [validate] => isUnsignedId
                                )
                            [id_shop] => Array
                                (
                                    [type] => 1
                                    [validate] => isUnsignedId
                                )
                            [id_address_delivery] => Array
                                (
                                    [type] => 1
                                    [validate] => isUnsignedId
                                )
                            [id_address_invoice] => Array
                                (
                                    [type] => 1
                                    [validate] => isUnsignedId
                                )
                            [id_carrier] => Array
                                (
                                    [type] => 1
                                    [validate] => isUnsignedId
                                )
                            [id_currency] => Array
                                (
                                    [type] => 1
                                    [validate] => isUnsignedId
                                    [required] => 1
                                )
                            [id_customer] => Array
                                (
                                    [type] => 1
                                    [validate] => isUnsignedId
                                )
                            [id_guest] => Array
                                (
                                    [type] => 1
                                    [validate] => isUnsignedId
                                )
                            [id_lang] => Array
                                (
                                    [type] => 1
                                    [validate] => isUnsignedId
                                    [required] => 1
                                )
                            [recyclable] => Array
                                (
                                    [type] => 2
                                    [validate] => isBool
                                )
                            [gift] => Array
                                (
                                    [type] => 2
                                    [validate] => isBool
                                )
                            [gift_message] => Array
                                (
                                    [type] => 3
                                    [validate] => isMessage
                                )
                            [mobile_theme] => Array
                                (
                                    [type] => 2
                                    [validate] => isBool
                                )
                            [delivery_option] => Array
                                (
                                    [type] => 3
                                )
                            [secure_key] => Array
                                (
                                    [type] => 3
                                    [size] => 32
                                )
                            [allow_seperated_package] => Array
                                (
                                    [type] => 2
                                    [validate] => isBool
                                )
                            [date_add] => Array
                                (
                                    [type] => 5
                                    [validate] => isDateFormat
                                )
                            [date_upd] => Array
                                (
                                    [type] => 5
                                    [validate] => isDateFormat
                                )
                        )
                    [classname] => Cart
                )
            [update_fields:protected] => 
            [force_id] => 
        )
    [altern] => 3
)


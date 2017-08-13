<?php

  global $rkvoters_config;

  // this varies depending on the environment...
  $cwd = getcwd();

  if($cwd == "/var/www/html"){
    $rkvoters_config = array(
      "api_url" => "http://174.138.68.14/api/app.php"
    );
  }
  else {
    $rkvoters_config = array(
      "api_url" => "http://localhost/biz/_rkvoter/data-api/api/app.php"
    );
  }

  if(function_exists("register_field_group")){
	register_field_group(array (
		'id' => 'acf_users_rkvoters',
		'title' => 'users_rkvoters',
		'fields' => array (
			array (
				'key' => 'field_598357fa4a2da',
				'label' => 'RK Voters - Access Token',
				'name' => 'rkvoters_access_token',
				'type' => 'text',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'ef_user',
					'operator' => '==',
					'value' => 'all',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'no_box',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
}

 ?>

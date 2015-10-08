<?php
ini_set('max_execution_time',2000);
require_once('../../../wp-load.php');
global $wpdb;

if(!empty($_POST['action']) && $_POST['action']=='new'){
	$wpdb->update($wpdb->prefix . HELIOS_TABLE_CATEGORY, 
	array(
		'category_name' => $_POST['category_name'],
		'status' =>'Y'
         ), 
		 array(
                'category_id' => $_POST['id']
          )
	);
	
	
	 $wpdb->insert($wpdb->prefix . HELIOS_TABLE_CATEGORY, array(
                'category_name' => $_POST['category_name'],
                'status' => 'Y'
                )
            );
	echo '[{"msg":"Sync was successful."}]';
	
	
	
	
	
}elseif(!empty($_POST['action']) && $_POST['action']=='edit') {
	
		$wpdb->update($wpdb->prefix . HELIOS_TABLE_CATEGORY, 
		array(
		'category_name' => $_POST['category_name'],
         ), 
		 array(
                'category_id' => $_POST['id']
          )
	);
	echo '[{"msg":"Sync was successful."}]';
}
elseif(!empty($_POST['action']) && $_POST['action']=='delete') {
	$wpdb->delete($wpdb->prefix . HELIOS_TABLE_CATEGORY, array('category_id' => $_POST['id']));
}

?>
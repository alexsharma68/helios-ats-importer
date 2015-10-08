<?php
global $wpdb;
$page = isset($_REQUEST ['page']) ? $_REQUEST ['page'] : '';
$mode = isset($_REQUEST ['mode']) ? $_REQUEST ['mode'] : '';

if (!empty($_POST['Save']) && wp_verify_nonce($_POST ['ps_nonce'], plugin_basename(__FILE__))) {
    /*$error = false;

    foreach ($_POST as $v) {
        if (empty($v)) {
            $error = true;
            $error_msg = 'Missing some fields.';
        }
    }*/

    /*if (!$error) {*/
	if(isset($_POST['category'])){
		$_POST['category']=implode(',',$_POST['category']);
	}else{
		$_POST['category']='';
	}
        if ($mode == 'edit') {
            $wpdb->update($wpdb->prefix . HELIOS_TABLE, array(
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'category' => $_POST['category'],
				'location' => $_POST['location']
                    ), array(
                'id' => $_REQUEST ['pid']
            ));
            $db_status = 'update';
        } else {
            $wpdb->insert($wpdb->prefix . HELIOS_TABLE, array(
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'category' => $_POST['category'],
				'location' => $_POST['location']
                )
            );
            $db_status = 'add';
        }
        /*echo '<script> window.location="admin.php?page=dashboard&status=' . $db_status . '" </script>';*/
        //wp_redirect(admin_url("admin.php?page=dashboard&status=$db_status"));
        //exit;
    }
//}

if ($mode == 'edit') {
    $result = $wpdb->get_row("select * from " . $wpdb->prefix . HELIOS_TABLE. " where id=$_REQUEST[pid]");
}
?>
<div class="wrap">
    <h2>
        <?php
        if ($mode == 'edit')
            echo 'Edit Job';
        else
            echo 'Add Job';
        ?> 
    </h2>
</div>



<div class="wrap">
    <?php if ($error) { ?>
        <div class="updated below-h2" id="message">
            <p><?php echo $error_msg; ?></p>
        </div>
    <?php } ?>
    <form id="jobfrm" name="jobfrm" action="" method="post">
        <table class="widefat fixed comments">
            <tr>
                <td width="12%">Title:</td>
                <td width="88%"><input type="text" style="width: 250px;" name="title" value="<?php if ($mode == 'edit') echo $result->title; ?>" /></td>
            </tr>
            <tr>
                <td>Description:</td>
                <td><?php wp_editor( $result->description, 'description', array('media_buttons'=>false,'textarea_name'=>'description','textarea_rows'=>10));?></td>
            </tr>
            <tr>
                <td width="12%">Category:</td>
                <td width="88%">
                <?php
                $results = $wpdb->get_results("select category_name, category_id from " . $wpdb->prefix . HELIOS_TABLE_CATEGORY, OBJECT);
				?>
                <select name="category[]" multiple="multiple" style="height:100px; width:250px;">
                <?php
				foreach($results as $values){
					$ary=array();
					if ($mode == 'edit'){
						$ary=explode(',',$result->category);
					}
					$sel=in_array($values->category_id,$ary)?'selected="selected"':'';
				?>
                	<option <?php echo $sel; ?> value="<?php echo $values->category_id; ?>"><?php echo $values->category_name; ?></option>
                    <?php
				}
					?>
                </select>
                <!--<input type="text" style="width: 250px;" name="category" value="<?php if ($mode == 'edit') echo $result->category; ?>" />
                -->
                
                </td>
            </tr>
            <tr>
                <td width="12%">Location:</td>
                <td width="88%"><input type="text" style="width: 250px;" name="location" value="<?php if ($mode == 'edit') echo $result->location; ?>" /></td>
            </tr>
            <tr>
                <td colspan="2"><input type="submit" accesskey="p" value="Save" class="button button-primary button-large" id="save" name="Save" /></td>
            </tr>
            <?php
            wp_nonce_field(plugin_basename(__FILE__), 'ps_nonce');
            ?>
        </table>
    </form>

</div>


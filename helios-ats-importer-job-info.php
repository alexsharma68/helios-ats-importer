<?php 
include_once('../../../wp-load.php');

global $wpdb;

$job_id = $_REQUEST['job_id'];
$row = $wpdb->get_row("select * from ".$wpdb->prefix. HELIOS_TABLE . " where id='$job_id'",OBJECT);
?>
<style type="text/css">
.order-cart {
	padding:3px;
}
.order-cart table th {
	padding:3px;
}
.order-cart table td {
	font-weight:bold;
}
</style>
<div class="order-cart">
	<?php if(count($row) == 1 ) { ?>
    <table width="100%">
    	<tr>
    		<th bgcolor="#99CC66" colspan="4">Job Detail</th>
        </tr>
        <tr>
        	<td width="14%">Title:</td>
            <td width="86%" colspan="3" align="left"><?php echo stripslashes($row->title);?></td>
        </tr>
        <tr>
        	<td width="14%">Description:</td>
            <td width="86%" colspan="3" align="left"><?php echo stripslashes($row->description);?></td>
        </tr>
        <tr>
        	<td width="14%">Category:</td>
            <td width="86%" colspan="3" align="left"><?php 

			$str=explode(',',$row->category);
						$e=array();
						foreach($str as $vals){
							$catres = $wpdb->get_row("select category_name from " . $wpdb->prefix . HELIOS_TABLE_CATEGORY. " WHERE category_id = $vals", OBJECT);
							$e[]=$catres->category_name;
						
						}
						echo implode(', ',$e);
						
					
			
			?></td>
        </tr>
        <tr>
        	<td width="14%">Publish Date:</td>
            <td width="86%" colspan="3" align="left"><?php echo date('jS F Y \a\t h:i A', strtotime($row->pubDate));?></td>
        </tr>
        <tr>
        	<td width="14%">Location:</td>
            <td width="86%" colspan="3" align="left"><?php echo $row->location;?></td>
        </tr>
        <tr>
        	<td width="14%">Country:</td>
            <td width="86%" colspan="3" align="left"><?php echo !empty($row->locationCountry)?$row->locationCountry:'Not Available.';?></td>
        </tr>
        <tr>
        	<td width="14%">State:</td>
            <td width="86%" colspan="3" align="left"><?php echo !empty($row->locationState)?$row->locationState:'Not Available.';?></td>
        </tr>
        <tr>
        	<td width="14%">City:</td>
            <td width="86%" colspan="3" align="left"><?php echo !empty($row->locationCity)?$row->locationCity:'Not Available.';?></td>
        </tr>
    </table>
    <?php } else {?>
    <?php echo "No ordered was found."; }?> 
</div>
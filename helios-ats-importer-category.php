<?php
global $wpdb;

if ($mode == 'delete') {
    ////$wpdb->query("delete from " . $wpdb->prefix . HELIOS_TABLE. " where id=$_REQUEST[pid]");
    echo '<script> window.location="admin.php?page=dashboard"</script>';
}

if (isset($_REQUEST['order']) && $_REQUEST['order'] != '') {
  update_option('short_helios',$_REQUEST['order']);
}

$order=(get_option('short_helios')=='')?'category ASC ':' category '. get_option('short_helios');

?>
<div class="wrap">
    <h2> Helios ATS Importer <a href="javascript:void(0)" class="button add">Add New</a></h2>
    <?php
    if (isset($_REQUEST['status'])) :
        ?>
        <div id="message" class="updated" style="margin-left: 0;">
            <?php
            if (isset($_REQUEST['status']) && $_REQUEST['status'] == 'add') {
                echo '<p>Job successfully added.</p>';
            }
            ?>  
            <?php
            if (isset($_REQUEST['status']) && $_REQUEST['status'] == 'update') {
                echo '<p>Job successfully updated.</p>';
            }
            ?> 
        <?php
        if (isset($_REQUEST['status']) && $_REQUEST['status'] == 'changeStatus') {
            echo '<p>' . $status_change_message . '</p>';
        }
        ?>  	
        </div>

        <?php
    endif;
    ?>
    
    
    

</div>


<div class="wrap">
    <table class="widefat fixed comments">
        <thead>
            <tr>
                <th width="5%">ID</th>
                <th width="88%" class="sorted <?php echo (get_option('short_helios')=='')?'asc':get_option('short_helios'); ?>">Category Name</th>
               <!-- <th width="15%">Status</th> -->
                <th width="7%">Option</th>
            </tr>
        </thead>

        <tbody>

<?php
$result = $wpdb->get_results("select category_name, category_id from " . $wpdb->prefix . HELIOS_TABLE_CATEGORY, OBJECT);
if ($result) {
    $count = $start + 1;
    foreach ($result as $entry) {
        ?>

                    <tr>
                        <td><?php echo $count++; ?></td>
                        <td data-id="<?php echo $entry->category_id; ?>"><?php echo $entry->category_name; ?></td>
                       
                        <td>
                            <a href="javascript:void(0);" data-id="<?php echo $entry->category_id; ?>" class="delete"><img src="<?php echo HELIOS__PLUGIN_URL.'/images/trash.png';?>" alt="Delete" /></a>
                        </td>
                    </tr> 
                    <?php
                }
                ?>

<?php } else { ?>
                <tr>
                    <td colspan="5">No record found.</td>
                </tr>
<?php } ?>
        </tbody>

        <tfoot>
           <tr>
                <th width="5%">ID</th>
                <th width="15%">Category Name</th>
               <!-- <th width="10%">Status</th>-->
                <th width="15%">Option</th>
            </tr>
        </tfoot>

    </table>
</div>
<script type="text/javascript">
jQuery(function($) {
    $("body").on('click','.delete',function(){
		var conf=confirm('Are you sure you want to delete?');
		if(conf){
			$(this).parents('tr').remove();
			$.ajax({
				type: "POST",
				url: "<?php echo HELIOS__PLUGIN_URL . 'helios-ajax-category.php'; ?>",
				dataType: "json",
				data: {'action':'delete','id':$(this).data('id')},
			}).done(function(data) {
				
			})
		}
	})
	$("body").on('click','.add',function(){
		
		var seconds = parseInt(new Date().getTime() / 1000);
		
								var e='<tr>'+
										'<td>'+($(".widefat > tbody>tr").length)+'</td>'+
										'<td data-id="'+seconds+'"><input type="text" data-action="new" name="category_name" data-id="'+seconds+'" class="category_name" value="New Category"/></td>'+
										
										//'<td>'+
										
											//'<a href="<?php echo $_SERVER["PHP_SELF"] ?>?page=category&pid="'+seconds+'"&status=changeStatus&mode=changeStatus" onclick="return confirm(\'Are you sure you want to <?php echo $status_title_change; ?>?\')" title="<?php echo $item_title; ?>">'+
											  // '<img src="<?php echo HELIOS__PLUGIN_URL.'/images/published.png';?>">'+
											//'</a>'+
									   //' </td>'+
									   ' <td>'+
											'<a href="javascript:void(0);" data-id="'+seconds+'" class="delete"><img src="<?php echo HELIOS__PLUGIN_URL.'/images/trash.png';?>" alt="Delete" /></a>'+
										'</td>'+
									'</tr>';
									$(".widefat > tbody").append(e);
	})
	$("body").on('blur',".category_name",function(){
		var id=$(this).data('id');
		var action=$(this).data('action');
		var name=$(this).val();
		$(this).parent().html(name);
		$(this).remove();
		$.ajax({
			type: "POST",
			url: "<?php echo HELIOS__PLUGIN_URL . 'helios-ajax-category.php'; ?>",
			dataType: "json",
			data: {'action':action,'id':id,'category_name':name},
			}).done(function(data) {
				
			})
	});
	$("body").on('keydown','.category_name',function(e){
			var code = (e.keyCode ? e.keyCode : e.which);
			if(code == 13){
				$('.category_name').trigger('blur');
			}
	});
	$("body").on('dblclick','.widefat > tbody>tr>td:nth-child(2)',function(){
		var old=$(this).html();
		$(this).empty().append("<input type='text' data-action='edit' name='category_name' data-id='"+$(this).data('id')+"' class='category_name' value='"+old+"' />")
	})
});
</script>
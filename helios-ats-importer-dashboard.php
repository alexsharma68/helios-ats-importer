<?php
global $wpdb;
global $subquery;
$page = isset($_REQUEST ['page']) ? $_REQUEST ['page'] : '';
$mode = isset($_REQUEST ['mode']) ? $_REQUEST ['mode'] : '';
$subquery = '';
$search_query = '';
if (!empty($_REQUEST['s'])) {
    $subquery .= " and p.title like '%" . $_REQUEST['s'] . "%'";
    $search_query .= '&s=' . $_REQUEST['s'];
}
if(!empty($_REQUEST['list_items'])){
	$limit = $_REQUEST['list_items'];
} else {
	$limit = 10;
}
$adjacents = 2;
$targetpage = get_bloginfo('url') . '/wp-admin/admin.php?page=dashboard';
$condition = "";
$total_records = count_records();

$paged = $_GET ['paged'];
if ($paged && $paged > 0)
    $start = ($paged - 1) * $limit; // first item to display on this page
else
    $start = 0;

if ($mode == 'delete') {
    $wpdb->query("delete from " . $wpdb->prefix . HELIOS_TABLE. " where id=$_REQUEST[pid]");
    echo '<script> window.location="admin.php?page=dashboard"</script>';
}

if (isset($_REQUEST['order']) && $_REQUEST['order'] != '') {
  update_option('short_helios',$_REQUEST['order']);
}
       
if ($mode == 'changeStatus') {
    $result = $wpdb->get_row("select publishedStatus from " . $wpdb->prefix . HELIOS_TABLE. " WHERE id = $_REQUEST[pid]", OBJECT);
    if ($result->publishedStatus == 'Y') {
        $status_change_to = 'N';
        $status_change_message = 'Job successfully expired.';
    } else {
        $status_change_to = 'Y';
        $status_change_message = 'Job successfully unexpired.';
    }
    $wpdb->update($wpdb->prefix .HELIOS_TABLE, array('publishedStatus' => $status_change_to), array('id' => $_REQUEST ['pid']));
}
//$order=(get_option('short_helios')=='')?'h.pubDate DESC ':' c.category_name '. get_option('short_helios');
if(get_option('short_helios')==''){
	update_option('short_helios','ASC');
}
if(!isset($_GET['sortby']) || $_GET['sortby']==''){
	$_GET['sortby']='h.title';
}
$order=$_GET['sortby'].' '. get_option('short_helios');

$result = $wpdb->get_results("select  
h.id, 
h.location,
h.category, 
c.category_name, 
h.publishedStatus,
SUBSTRING_INDEX(SUBSTRING_INDEX(h.category, ',',1), ',',-1) as cas,
h.pubDate,  
h.title 
from " . $wpdb->prefix . HELIOS_TABLE. " h
LEFT JOIN " . $wpdb->prefix . HELIOS_TABLE_CATEGORY. " c ON SUBSTRING_INDEX(SUBSTRING_INDEX(h.category, ',',1), ',',-1)=c.category_id WHERE 
h.publishedStatus='Y' 
$subquery order by ".$order." limit $start, $limit");

if($wpdb->last_error) {
 echo $wpdb->last_error;
}
?>
<div class="wrap">
    <h2> Helios ATS Importer <?php /* ?><a href="<?php echo $_SERVER["PHP_SELF"]?>?page=add_edit_xml" class="button">Add New</a> <?php */ ?></h2>
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
    
    <form method="get" action="">
      <p class="search-box">
      <label for="post-search-input" class="screen-reader-text">Search Job:</label>
      <input type="hidden" name="page" value="dashboard">
      <input type="search" name="s" value="<?php echo isset($_GET['s'])?$_GET['s']:'';?>">
      <input type="submit" value="Search Job" class="button" id="search-submit">
      </p>
    </form>
    <div class="tablenav top">
        <div class="alignleft actions bulkactions">
            <form name="frm" method="get" action="">
            	<input type="hidden" name="page" value="dashboard">
                <select name="list_items" >
                    <option value="">View All Jobs</option>
                    <option value="15" <?php if(!empty($_REQUEST['list_items']) && $_REQUEST['list_items']==15) echo 'selected="selected"';?>>15 items</option>
                    <option value="25" <?php if(!empty($_REQUEST['list_items']) && $_REQUEST['list_items']==25) echo 'selected="selected"';?>>25 items</option>
                    <option value="50" <?php if(!empty($_REQUEST['list_items']) && $_REQUEST['list_items']==50) echo 'selected="selected"';?>>50 items</option>
                    <option value="100" <?php if(!empty($_REQUEST['list_items']) && $_REQUEST['list_items']==100) echo 'selected="selected"';?>>100 items</option>
                </select>
                <input type="submit" name="" id="post-query-submit" class="button" value="Show"> 
            </form>
        </div>
        <div class="tablenav-pages">
			<?php
            require_once HELIOS__PLUGIN_DIR . '/helios-ats-importer-pagination.php';
            echo $pagination;
            ?>
        </div>
    </div>

</div>


<div class="wrap">
    <table class="widefat fixed comments">
        <thead>
            <tr>
                <th width="5%">ID</th>
                <th width="25%" class="sorted <?php echo (isset($_GET['sortby']) && $_GET['sortby']!='h.title')?'asc':get_option('short_helios'); ?>">
                <a href="?page=dashboard&order=<?php echo (get_option('short_helios')=='desc')?'asc':'desc'; ?><?php echo isset($_GET['list_items'])?'&list_items='.$_GET['list_items']:''; ?><?php echo isset($_GET['s'])?'&s='.$_GET['s']:''; ?>&sortby=h.title"><span>
                Title
                </span><span class="sorting-indicator"></span></a>
                </th>
                <th width="15%" class="sorted <?php echo (isset($_GET['sortby']) && $_GET['sortby']!='c.category_name')?'asc':get_option('short_helios'); ?>"><a href="?page=dashboard&order=<?php echo (get_option('short_helios')=='desc')?'asc':'desc'; ?><?php echo isset($_GET['list_items'])?'&list_items='.$_GET['list_items']:''; ?><?php echo isset($_GET['s'])?'&s='.$_GET['s']:''; ?>&sortby=c.category_name"><span>Category</span><span class="sorting-indicator"></span></a></th>
                <th width="15%" class="sorted <?php echo (isset($_GET['sortby']) && $_GET['sortby']!='h.pubDate')?'asc':get_option('short_helios'); ?>">
                <a href="?page=dashboard&order=<?php echo (get_option('short_helios')=='desc')?'asc':'desc'; ?><?php echo isset($_GET['list_items'])?'&list_items='.$_GET['list_items']:''; ?><?php echo isset($_GET['s'])?'&s='.$_GET['s']:''; ?>&sortby=h.pubDate"><span>
                Published Date
                </span><span class="sorting-indicator"></span></a>
                </th>
                <th width="15%" class="sorted <?php echo (isset($_GET['sortby']) && $_GET['sortby']!='h.title')?'asc':get_option('short_helios'); ?>">
                <a href="?page=dashboard&order=<?php echo (get_option('short_helios')=='desc')?'asc':'desc'; ?><?php echo isset($_GET['list_items'])?'&list_items='.$_GET['list_items']:''; ?><?php echo isset($_GET['s'])?'&s='.$_GET['s']:''; ?>&sortby=h.location"><span>
                Location
                </span><span class="sorting-indicator"></span></a>
                </th>
                <th width="10%">Status</th>
                <th width="15%">Option</th>
            </tr>
        </thead>

        <tbody>

<?php
if ($result) {
    $count = $start + 1;
    foreach ($result as $entry) {
        ?>

                    <tr>
                        <td><?php echo $count++; ?></td>
                        <td><?php echo $entry->title; ?></td>
                        <td><?php 
						$str=explode(',',$entry->category);
						$e=array();
						foreach($str as $vals){
							$catres = $wpdb->get_row("select category_name from " . $wpdb->prefix . HELIOS_TABLE_CATEGORY. " WHERE category_id = $vals", OBJECT);
							$e[]=$catres->category_name;
						
						}
						echo implode(', ',$e);
						
						?></td>
                        <td><?php echo date('jS F Y \a\t h:i A', strtotime($entry->pubDate)); ?></td>
                        <td><?php echo $entry->location; ?></td>
                        <td>
						<?php 
                            if ($entry->publishedStatus=='Y') {
                                $icon_status = 'published.png';
                                $item_title = 'Click to expired';
                                $status_title_change = 'expired';
                            } else {
                                $icon_status = 'unpublished.png';
                                $item_title = 'Click to unexpired';
                                $status_title_change = 'unexpired';
                            }
                            ?>
                            <a href="<?php echo $_SERVER["PHP_SELF"] ?>?page=<?php echo $page; ?>&pid=<?php echo $entry->id; ?>&status=changeStatus&mode=changeStatus"
                               onclick="return confirm('Are you sure you want to <?php echo $status_title_change; ?>?')" title="<?php echo $item_title; ?>">
                               <img src="<?php echo HELIOS__PLUGIN_URL.'/images/'.$icon_status;?>"
                            </a>
                        </td>
                        <td>
                        	<a href="<?php echo HELIOS__PLUGIN_URL . '/helios-ats-importer-job-info.php?job_id='.$entry->id.'&keepThis=true&height=500&amp;width=700';?>"  class='thickbox' title='Click to view Detail'><img src="<?php echo HELIOS__PLUGIN_URL.'/images/detail.png';?>" alt="View Detail" /></a>
                            &nbsp;&nbsp;&nbsp;
                            <a href="<?php echo $_SERVER["PHP_SELF"] ?>?page=edit_item&pid=<?php echo $entry->id; ?>&mode=edit" title="Edit"><img src="<?php echo HELIOS__PLUGIN_URL.'/images/edit.png';?>" alt="Edit" /></a>
                            &nbsp;&nbsp;&nbsp;
                            <a href="<?php echo $_SERVER["PHP_SELF"] ?>?page=<?php echo $page; ?>&pid=<?php echo $entry->id; ?>&mode=delete" onclick="return confirm('Are you sure you want to delete?')" title="Delete"><img src="<?php echo HELIOS__PLUGIN_URL.'/images/trash.png';?>" alt="Delete" /></a>
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
                <th width="25%">Title</th>
                <th width="15%">Category</th>
                <th width="15%">Published Date</th>
                <th width="15%">Location</th>
                <th width="10%">Status</th>
                <th width="15%">Option</th>
            </tr>
        </tfoot>

    </table>
</div>
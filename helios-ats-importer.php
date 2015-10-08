<?php
/*
  Plugin Name: Helios ATS Importer
  Plugin URI: http://www.yokoco.com
  Description: A Helios ATS Importer Plugin
  Version: 1.0
  Author: Yoko Co
  Author URI: http://www.yokoco.com
  .
 */
define('HELIOS', '1.0.0');
define('HELIOS_TABLE', 'helios_ats');
define('HELIOS_TABLE_CATEGORY', 'helios_ats_category');
define('HELIOS__PLUGIN_URL', plugin_dir_url(__FILE__));
define('HELIOS__PLUGIN_DIR', plugin_dir_path(__FILE__));
$upload_dir = wp_upload_dir();
require_once( HELIOS__PLUGIN_DIR . 'class.helios-ats-importer-widget.php' );
require_once( HELIOS__PLUGIN_DIR . 'helios-ats-helper.php' );

/* ========== Custom Table Activation and Deactivation Code Start ====== */
register_activation_hook(__FILE__, 'helios_table_install');
register_uninstall_hook(__FILE__, 'helios_table_uninstall');

//$table = $wpdb->prefix.HELIOS_TABLE;
//$wpdb->query("DROP TABLE IF EXISTS $table");
/* funcion for register custom table */
function helios_table_install() {
    global $wpdb;
    $tables 	= $wpdb->get_results("SHOW TABLES FROM " . DB_NAME);
    $tbl_name 	= 'Tables_in_' . DB_NAME;
    $tbl 		= $wpdb->prefix . HELIOS_TABLE;
	$cat 		= $wpdb->prefix . HELIOS_TABLE_CATEGORY;
    /* * Execute the sql statement to create or update the custom table * */

    foreach ($tables as $table) {
        $tbls[] = $table->$tbl_name;
    }
	
    if (!in_array($tbl, $tbls)) {
        $sql = "CREATE TABLE " . $tbl . " (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			`description` text COLLATE utf8_unicode_ci NOT NULL,
			`category` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			`pubDate` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			`location` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			`locationCountry` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			`locationState` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			`locationCity` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			`reqId` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			`publishedStatus` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
			PRIMARY KEY (`id`),
			UNIQUE KEY `title` (`title`,`reqId`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
    }
	if (!in_array($cat, $tbls)) {
        $sql1 = "CREATE TABLE " . $cat . " (
			`category_id` int(11) NOT NULL AUTO_INCREMENT,
			`category_name` text COLLATE utf8_unicode_ci NOT NULL,
			`status` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
			PRIMARY KEY (`category_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql1);
    }
	
	
    
    
	
    add_option("helios_feed_link", "https://ch.tbe.taleo.net/CH05/ats/servlet/Rss?org=HELIOSHR&cws=1&WebPage=SRCHR&WebVersion=0&_rss_version=2");
    add_option('helios_admin_email', 'admin@helios.com');
    add_option('helios_file_type', 'doc,docx,pdf,rtf,txt');
	
	
    $upload_dir = wp_upload_dir();
	
    if (!file_exists($upload_dir['basedir'] . '/application')) {
		$oldumask = umask(0);
        mkdir($upload_dir['basedir'] . '/application', '0777', true);
        umask($oldumask);
    }
}

function helios_table_uninstall() {
    delete_option("helios_feed_link");
    delete_option("helios_admin_email");
    delete_option('helios_file_type');
}

function helios_ats_importer() {
    add_menu_page('Helios ATS', 'Helios ATS', 'manage_options', 'dashboard', 'helios_ats_importer_dashboard', HELIOS__PLUGIN_URL . '/images/ats.png');
    add_submenu_page('dashboard', 'Dashboard', 'Dashboard', 'manage_options', 'dashboard', 'helios_ats_importer_dashboard');
	add_submenu_page('dashboard', 'Manage Category', 'Manage Category', 'manage_options', 'category', 'helios_ats_importer_category');
    add_submenu_page('dashboard', 'Configuration', 'Configuration', 'manage_options', 'configuration', 'helios_ats_importer_configuration');
    add_submenu_page('options.php', 'Edit Item', 'Edit Item', 'manage_options', 'edit_item', 'helios_ats_importer_edit_item');
    add_submenu_page('dashboard', 'Expired Item', 'Expired Item', 'manage_options', 'expired_item', 'helios_ats_importer_expired_item');
}

function helios_ats_importer_dashboard() {
    include_once('helios-ats-importer-dashboard.php');
}

function helios_ats_importer_configuration() {
    include_once('helios-ats-importer-configuration.php');
}
function helios_ats_importer_category() {
    include_once('helios-ats-importer-category.php');
}
function helios_ats_importer_edit_item() {
    include_once('helios-ats-importer-edit-item.php');
}

function helios_ats_importer_expired_item() {
    include_once('helios-ats-importer-expired-item.php');
}

add_action('admin_menu', 'helios_ats_importer');

/**
 * Register style sheet.
 */
function helios_ats_styles() {
    wp_register_style('helois', HELIOS__PLUGIN_URL . '/css/helios.css');
    wp_enqueue_style('helois');
}

add_action('admin_init', 'helios_ats_styles');

function runScripts() {
    wp_enqueue_script('thickbox');
    wp_enqueue_style('thickbox');
}

add_action('admin_init', 'runScripts');
function metatag() {
	if(get_last()!=''){
		echo '<meta name="description" content="'.get_metadesc().'" />';
	}
}
add_action('wp_head', 'metatag');
/* Helios Shortcode */
function helios_shortcode($atts) {
    global $wpdb,$post,$wp_query;
    $html = '';
    extract(shortcode_atts(array(
        'limit' => '5',
        'max_words' => '100',
        'max_chars' => '150',
        'class' => 'helios-joblist'
	), $atts));
	
    if (get_query_var("jobs_id")=='') { 
        return get_list($limit, $max_words, $max_chars);   
    } else {
        return jobs_detail(get_last());
    }
}

//$entry = $wpdb->query("delete from " . $wpdb->prefix ."options WHERE option_name = 'rewrite_rules'", OBJECT);
//$entry = $wpdb->get_row("select * from " . $wpdb->prefix ."options WHERE option_name = 'rewrite_rules'", OBJECT);
// print_r($entry);
add_shortcode('jobs', 'helios_shortcode');

/* Helios Widgets */
function returnurl(){
    if(get_option('helios_url')==''){
    global $post;
    $permalink = get_permalink($post->ID);
    return str_replace(get_site_url()."/",'',$permalink);
    }else{
        $permalink = get_permalink(get_option('helios_url'));
        return str_replace(get_site_url()."/",'',$permalink);  
    }
}

function get_id_page(){
    if(get_option('helios_url')==''){
    global $post;
    return $post->ID;
    }else{
        return get_option('helios_url');
    }
}

$opt=get_option('rewrite_rules');
if(is_array($opt)){
   
    
}
add_filter('init', 'kill_rewrite_rules' );
add_filter( 'query_vars', 'wpa5413_query_vars' );
add_filter('init','flushRules');

function kill_rewrite_rules($rules){
   if(returnurl()!=''){
   $type=(is_page())?'page_id':'p';
   add_rewrite_rule(returnurl().'([^/]+)/([^/]+)/?',
		'index.php?pagename='.returnurl().'&jobs_id=$matches[1]&city=$matches[2]',
		'top'
	);
	//print_r($rules);
   //add_rewrite_tag('%jobs_id%','([^&]+)');
	//add_rewrite_tag('%city%','([^&]+)');
   	//flush_rewrite_rules(true);

   }

	
}
function wpa5413_query_vars($query_vars){ 
	array_push($query_vars, 'jobs_id');
	array_push($query_vars, 'city');
	return $query_vars;
}
function flushRules(){
    global $wp_rewrite;
    //$wp_rewrite->flush_rules();
}




add_action('widgets_init', 'helios_widget');
function helios_widget() {
    register_widget('Helios_Widget');
}
add_filter('wp_title', "get_title_load");
//if (get_last()!='') { 
add_filter('the_title', 'change_title_of_page');
//}

function get_last(){
	$sinments 		= explode("#",$_SERVER["SERVER_NAME"].$_SERVER['REQUEST_URI']);
	$sinments1 		= explode("?",$sinments[0]);
	$sinments_prm 	= explode("/",$sinments1[0]);
	$filtered		= array_filter($sinments_prm);
	$url			= explode('-', $filtered[sizeof($filtered)-1]);
	return $data	= (int)$url[sizeof($url)-1];
}
function get_metadesc(){
	global $wpdb;
	$job_id = get_last(); //echo $title;
	if(($job_id!=0) || ($job_id!='')){
	$entry = $wpdb->get_row("select * from " . $wpdb->prefix . HELIOS_TABLE . " WHERE id = '$job_id' and publishedStatus = 'Y'", OBJECT);
	if ($entry) {
		return truncate(strip_tags($entry->description),100,160);
	}
	}
	      
}
function get_title_load($title){
	global $wpdb;
	$job_id = get_last(); //echo $title;
	if(($job_id!=0) || ($job_id!='')){
	$entry = $wpdb->get_row("select * from " . $wpdb->prefix . HELIOS_TABLE . " WHERE id = '$job_id' and publishedStatus = 'Y'", OBJECT);
	if ($entry) {
		return $entry->title." - ".get_bloginfo("name");
	}
	}else{
		return $title;
	}      
}

function change_title_of_page($title) {
	if(in_the_loop()){
	   global $wpdb;
	   $job_id = get_last();
	   if (($job_id!=0) || ($job_id!='')){
			$entry = $wpdb->get_row("select * from " . $wpdb->prefix . HELIOS_TABLE . " WHERE id = '$job_id' and publishedStatus = 'Y'", OBJECT);
			//return ($entry->title!='')?$entry->title:$title;
return $title;
		}else{
			return $title; 
		}
	}else{
		return $title; 
	}
}

function create_slug( $string ) {
	$result = preg_replace("/[^a-zA-Z0-9]+/", "-",  $string);
	return strtolower($result);
}

function get_list($limit, $max_words, $max_chars) { 
    ob_start();
    global $wpdb, $post;

    $permalink 					= get_permalink(get_id_page());
    $opt						= get_option('rewrite_rules');
    $url						= '';
    $detail_url_for_moderewrite	= '';
	
    if(is_array($opt)){
        $url=$permalink."?";
        $detail_url_for_moderewrite=$permalink;
    }else{
        $url=$permalink;
        if(isset($_GET['page_id'])){
            //$url.="&page_id=".$_GET['page_id'];
        }elseif(isset($_GET['p'])){
            //$url.="";//"&p=".$_GET['p']; 
        }
    }
    $detail_page=$url;
   
    if (!empty($_REQUEST['per_page'])) {
        $limit = (int) $_REQUEST['per_page'];
    } else {
        $limit = $limit; //15;
    }
    
    $adjacents = 2;
    $condition = "";
    $total_records = count_records();

    $page_num = (int) $_GET ['page_num'];
    if ($page_num && $page_num > 0)
        $start = ($page_num - 1) * $limit; // first item to display on this page
    else
        $start = 0;
		//$page_num,$adjacents; 
    ?>
	
    <?php /* Job List: Start */ ?>
    <link rel="stylesheet" href="<?php echo plugins_url('css/jllistpage.css', __FILE__); ?>">
    <script>
        jQuery(function($) {
            $('form.list-item').change(function() {
                var per_page = $('form.list-item select').val();
                window.location = '<?php echo $url; ?>' + '&per_page=' + per_page;
            });
        });
    </script>
	
    <?php $url.='&per_page='.$limit; 
	
	
	
	?>
	
    <div class="joblist listpage">
	
		<p><a href="https://careers-helioshr.icims.com/jobs/login?back=intro&hashed=-435739667" target="_blank">If you would like to be considered for new positions as they become available, please feel free to submit your resume by completing a general application for future consideration.</a></p>

		<?php /* Limiter Top: Start */ ?>
        <div class="jllimitavail top">
            <div class="jllimiter">
                <span class="jllimitlabel">Show</span>
                <form class="list-item">
                    <select name="per_page" class="jllimitselect select">
                        <option value="15" <?php if (!empty($_REQUEST['per_page']) && $_REQUEST['per_page'] == 15) echo 'selected="selected"'; ?>>15</option>
                        <option value="25" <?php if (!empty($_REQUEST['per_page']) && $_REQUEST['per_page'] == 25) echo 'selected="selected"'; ?>>25</option>
                        <option value="50" <?php if (!empty($_REQUEST['per_page']) && $_REQUEST['per_page'] == 50) echo 'selected="selected"'; ?>>50</option>
                        <option value="100" <?php if (!empty($_REQUEST['per_page']) && $_REQUEST['per_page'] == 100) echo 'selected="selected"'; ?>>100</option>
                    </select>
                </form>
				
                <span class="jllimitlabel">jobs per page</span>

                <div class="clear"></div>
            </div>
            <div class="jlavailable">
                <span class="jllimitlabel"><?php echo $total_records; ?> items available</span>

                <div class="clear"></div>
            </div>

            <div class="clear"></div>
        </div>
		<?php /* Limiter Top: End */ ?>

        <?php
		
		//echo "select * from " . $wpdb->prefix . HELIOS_TABLE . " WHERE 1= 1 and publishedStatus='Y' $subquery order by ".$order."  limit $start, $limit";
		if(!isset($_GET['category'])){
			$order=(get_option('short_helios')=='')?'h.pubDate DESC ':' h.pubDate DESC ';
			$sql="select 
			h.id, 
			h.location,
			h.locationCity,
			h.description,
			h.category, 
			c.category_name, 
			SUBSTRING_INDEX(SUBSTRING_INDEX(h.category, ',',1), ',',-1) as cas,
			h.pubDate,  
			h.title 
			from " . $wpdb->prefix . HELIOS_TABLE. " h
			LEFT JOIN " . $wpdb->prefix . HELIOS_TABLE_CATEGORY. " c ON SUBSTRING_INDEX(SUBSTRING_INDEX(h.category, ',',1), ',',-1)=c.category_id WHERE 
			h.publishedStatus='Y' order by ".$order."  
			limit $start, $limit";
		}else{
			$order='h.pubDate DESC ';
			$sql="select 
			h.id, 
			h.location,
			h.locationCity,
			h.description,
			h.category, 
			h.pubDate,  
			h.title 
			from " . $wpdb->prefix . HELIOS_TABLE. " h WHERE 
			h.publishedStatus='Y' AND h.category LIKE '%".$_GET['category']."%' order by ".$order."  
			limit $start, $limit";
		}
        $result = $wpdb->get_results($sql);
        $record_per_page = count($result);
        ?>
		
        <div class="jblistoutput">
            <ul class="jbunorderedlist">

				<?php /* Job Item 1: Start */ ?>
				<?php
				$i = 1;

				foreach ($result as $entry) {
					$class = ($i % 2 == 0) ? 'even' : 'odd';
					if ($i == 1) {
						$first_last_class = 'first ';
					} elseif ($i == $record_per_page) {
						$first_last_class = 'last ';
					} else {
						$first_last_class = '';
					}
					?>
                    <li class="<?php echo $first_last_class . $class; ?>">
                        <div class="jblistinfotop">
                            <div class="jblistinfotopinside">
								<?php
								if($detail_url_for_moderewrite==''){
									$detail_url=$detail_page.'&job_id=' . $entry->id;
								}else{
									$detail_url=$detail_url_for_moderewrite.create_slug($entry->locationCity).'/'.create_slug($entry->title).'-'.$entry->id.'/';
								}
								?>
								
                                <h3 class="block-title"><a href="<?php echo $detail_url; ?>"><span><?php echo $entry->title; ?></span></a></h3>

                                <div class="clear"></div>
                            </div>

                            <div class="jbpostdate">
                                <span class="label">Posted</span>
                                <span class="jblabelval"><?php echo date('F j, Y', strtotime($entry->pubDate)); ?></span>

                                <div class="clear"></div>
                            </div>

                            <div class="clear"></div>
                        </div>

                        <div class="jbtypelocation">
                            <div class="jbtype">
                                <span class="label">Location:</span>
                                <span class="jblabelval"><?php echo $entry->location; ?></span>

                                <div class="clear"></div>
								<span class="label">Category:</span>
                                <span class="jblabelval">
                                <?php 
								$str=explode(',',$entry->category);
								$e=array();
								foreach($str as $vals){
									$catres = $wpdb->get_row("select category_name,category_id from " . $wpdb->prefix . HELIOS_TABLE_CATEGORY. " WHERE category_id = $vals", OBJECT);
									$e[]='<a href="'.$url.'&category='.$catres->category_id.'">'.$catres->category_name.'</a>';
								}
								echo implode(', ',$e);
								?>
                                </span>
                                <!--
                                <span class="separator">|</span>
								  -->
                                
                            </div>

                            <div class="clear"></div>
                        </div>

                        <div class="jbdesc">
                            <p><?php echo truncate(strip_tags(stripslashes($entry->description),"<strong>"), $max_words, $max_chars); ?></p>
							
                            <div class="clear"></div>
                        </div>

                        <div class="jbviewbttn">
                            <a href="<?php echo $detail_url; ?>" class="bttn"><span>View Now</span></a>

                            <div class="clear"></div>
                        </div>
                    </li>
				<?php
					$i++;
				}
				?>
                <?php /* Job Item 1: End */ ?>

            </ul>

            <div class="clear"></div>
        </div>

		<?php /* Pager: Start */ ?>
        <div class="jblistpager">
            <ul>
				<?php 
				if(isset($_GET['category'])){
		$url.='&category='.$limit; 
	}
				echo pagination($total_records, $limit, $url, $page_num, $adjacents); ?>
            </ul>

            <div class="clear"></div>
        </div>
		<?php /* Pager: End */ ?>

        <?php /* Limiter Top: Start */ ?>
        <div class="jllimitavail bottom">
            <div class="jllimiter">
                <span class="jllimitlabel">Show</span>
                <form class="list-item">
                    <select name="per_page" class="jllimitselect select">
                        <option value="15" <?php if (!empty($_REQUEST['per_page']) && $_REQUEST['per_page'] == 15) echo 'selected="selected"'; ?>>15</option>
                        <option value="25" <?php if (!empty($_REQUEST['per_page']) && $_REQUEST['per_page'] == 25) echo 'selected="selected"'; ?>>25</option>
                        <option value="50" <?php if (!empty($_REQUEST['per_page']) && $_REQUEST['per_page'] == 50) echo 'selected="selected"'; ?>>50</option>
                        <option value="100" <?php if (!empty($_REQUEST['per_page']) && $_REQUEST['per_page'] == 100) echo 'selected="selected"'; ?>>100</option>
                    </select>
                </form>
                <span class="jllimitlabel">jobs per page</span>

                <div class="clear"></div>
            </div>
            <div class="jlavailable">
                <span class="jllimitlabel"><?php echo $total_records; ?> items available</span>

                <div class="clear"></div>
            </div>

            <div class="clear"></div>
        </div>
		<?php /* Limiter Top: End */ ?>

        <div class="clear"></div>
    </div>
    <?php /* Job List: End */ ?>
	
    <?php
    /* PERFORM COMLEX QUERY, ECHO RESULTS, ETC. */
    $page = ob_get_contents();
    ob_end_clean();
    return $page;
}

function jobs_detail($e) {
    ob_start();

    global $wpdb;
    $status = 'yes';
    $a		= get_option('helios_file_type');
    $b		= explode("|", $a);
    $ext	= implode(', ', $b);
    if (!empty($e)) {
        if (isset($_POST['submit']) && wp_verify_nonce($_POST ['ps_nonce'], plugin_basename(__FILE__))) {}
		
        global $wpdb;
        $job_id = $e;
        $entry 	= $wpdb->get_row("select * from " . $wpdb->prefix . HELIOS_TABLE . " WHERE id = '$job_id' and publishedStatus = 'Y'", OBJECT);
		
        if ($entry) {
            ?>
            <link rel="stylesheet" href="<?php echo plugins_url('css/jldescriptionpage.css', __FILE__); ?>">
            <link rel="stylesheet" href="<?php echo plugins_url('css/jquery.fileupload.css', __FILE__); ?>">
            <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,700,600,800,300' rel='stylesheet' type='text/css'>

            <?php /* ?><script src="<?php echo plugins_url( 'js/jquery-1.9.0.js' , __FILE__ ); ?>"></script><?php */ ?>
			
            <script src="<?php echo plugins_url('js/jquery.validate.min.js', __FILE__); ?>"></script>
			
            <!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
            <script src="<?php echo plugins_url('js/jquery.ui.widget.js', __FILE__); ?>"></script>
			
            <!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
            <script src="<?php echo plugins_url('js/jquery.iframe-transport.js', __FILE__); ?>"></script>
			
            <!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
            <script src="<?php echo plugins_url('js/jquery.fileupload.js', __FILE__); ?>"></script>
			
            <!-- Bootstrap JS is not required, but included for the responsive demo navigation -->
            <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
            <script>
                /*jslint unparam: true */
                /*global window, $ */
                var total=0;
                function getFileExtension(filename) {
					var ext = /^.+\.([^.]+)$/.exec(filename);
					return ext == null ? "" : ext[1];
				}
                jQuery(function ($) {
                    'use strict';
                    // Change this to the location of your server-side upload handler:
                    var url ='<?php echo HELIOS__PLUGIN_URL . '/php/'; ?>';
                    $('#fileupload').fileupload({
                        url: url,
                        add: function(e, data) {
                            //console.log(getFileExtension(data.originalFiles[0]['name'])+ " -|- "+ data.originalFiles[0]['type'].length);
                            
							var uploadErrors = [];
							var acceptFileTypes =/^(<?php echo get_option('helios_file_type');?>)$/;
							if(!acceptFileTypes.test(getFileExtension(data.originalFiles[0]['name']))) {
								uploadErrors.push('we accept (<?php echo $ext;?>) files and files size below 2MB');
							}
							var calc=((data.originalFiles[0]['size']/1024)/1024);
							//alert(calc);
							if(calc > 2) {
								uploadErrors.push('Filesize is too big, allowed size is only 2MB');
							}
							if(total>2){
								uploadErrors.push('You cant upload more than 2 file'); 
							}
							if(uploadErrors.length > 0) {
								alert(uploadErrors.join("\n"));
							} else {
								data.submit();
							}
                        },
                        maxNumberOfFiles			: 2,
                        limitMultiFileUploadSize 	: '2050',
                        maxFileSize					: '2000',
                        dataType					: 'json',
                        done						: function (e, data) {
                            total++;
                            if(total<=2){
                            $.each(data.result.files, function (index, file) {
                                $('<input name="uploadedFiles[]" type="hidden" value="'+file.name+'">').appendTo('#files');
                                $('</p>').text(file.name).appendTo('#files');
                            });
                            }else{
								alert('You exceed upload limit'); 
                            }
                        },
                        process: function (e, data) {
                            //
                        },
                        progressall: function (e, data) {
                            var progress = parseInt(data.loaded / data.total * 100, 10);
                            $('#progress .progress-bar').css(
                                'width',
                                progress + '%'
                            );
                        }
                    }).prop('disabled', !$.support.fileInput)
                        .parent().addClass($.support.fileInput ? undefined : 'disabled');
                });
            </script>
            <script>
                jQuery(document).ready(function($) {
                     
                    $("#helios-application-form").validate({
                        rules: {
                            full_name: {
                                required: true
							},
                            user_email: {
                                required: true,
                                email: true
                            },
                            user_phone: {
                                required: true
                            },
                            citystate: {
                                required: true
                            },
                            files: {
                                required: true
                            },
                            anti:{
                                required: true
                            }
                       },
                        messages: {
                            full_name: {
                                required: "Name must be filled out."
							},
                            user_email: {
                                required: "Email must be filled out.",
                                email: "Your email must be valid."
                            },
                            user_phone: {
                                required: "Phone must be filled out."
                            },
                            citystate: {
                                required: "City must be filled out."
                            },
                            files: {
                                required: "File must be required."
                            },
                            anti:{
                                required: "Security code is required."
                            }
                        }
                        ,submitHandler:function(form){
                            $(".jlforminside").fadeTo('slow',0.5);
                            
                            $.ajax({
                                url		: '<?php echo HELIOS__PLUGIN_URL . 'helios-email.php'; ?>',
                                type	: 'POST',
                                data	: $("#helios-application-form").serialize(),
                                success	: function(result) {
                                    $(".jlforminside").fadeTo('slow',1);
                                    
                                    if($.trim(result)=='yes'){
										total=0;
										$("#full_name").val('');
										$("#user_email").val('');
										$("#user_phone").val('');
										$("#citystate").val('');
										$("#files").val('');
										$("#anti").val('');
										$("#files").empty();
										$(".progress-bar").width(0);
										$("#message").val('');
									}
									$("span.succ").fadeIn(200);
                                    var info="You've submitted your information successfully.";
									var info1="Invalid file format.";
									//alert(result);
									if($.trim(result)=='yes'){
										$("span.succ").removeClass('error').html(info);
									}else{
										$("span.succ").addClass("error").html("Anti-spam problem");
									}
									setTimeout(function(){$("span.succ").fadeOut(500);},4200)
                                }
                            });
                        }
                    });
                    // validation ends here
					
                    jQuery(document).ready(function($) {
						$("#page #page-title h1").html("<?php echo $entry->title; ?>");
					})
                });
				document.title="<?php echo $entry->title." - ".get_bloginfo("name"); ?>";
            </script>
            
			<?php /* Job List: Start */ ?>
            <div class="joblist descpage">

				<?php /* Posted Info: Start */ ?>
                <div class="jbpostdate">
                    <div>
                        <span class="label">Posted:</span>
                        <span class="jblabelval"><?php echo date('F j, Y', strtotime($entry->pubDate)); ?></span>
                    </div>
					<?php /*
                    <div>
                        <span class="label">Type:</span>
                        <span class="jblabelval">Administration</span>
                    </div>
					*/ ?>
                    <div>
                        <span class="label">Location:</span>
                        <span class="jblabelval"><?php echo $entry->location; ?></span>
                    </div>

                    <div class="clear"></div>
                </div>
				<?php /* Posted Info: End */ ?>

				<?php /* Job Description: Start */ ?>
                <div class="jllistingdesc">
                    <h2><span>Job Description</span></h2>
					
					<?php echo apply_filters('the_content', $entry->description); ?>
					
                    <div class="clear"></div>
                </div>
                <?php /* Job Description: End */ ?>

				<?php // Form Info: Start ?>
                <div class="jlforminfo">
                    <?php /*
					<h2><span>Apply Now</span></h2>

                    <p>Fill out the form below or send your resume and cover letter to:</p>
					*/ ?>
					
					<h2>Apply for Position</h2>
					
					<?php /*
					<p>You can apply for this position using the following information:</p>

                    <p class="jlcontactinfo" style="font-weight: normal !important; line-height: 18px !important;">
						<strong>Subject:</strong> Helios HR - <?php echo $entry->title; ?><br/>
                        <strong>Email:</strong> <a href="mailto:recruiter@helioshr.com">recruiter@helioshr.com</a></p>
					
					<p>Or click below:</p>
					*/ ?>
					
					<div>
						<a href="<?php echo $entry->reqId; ?>" 
							target="_blank" 
							class="jlformsubmit button send submit" 
							style="float: left; width: auto; padding-left: 30px; padding-right: 30px;">Apply Now</a>
						
						<div class="clear"></div>
					</div>

                    <div class="clear"></div>
                </div>
				<?php // Form Info: End ?>
				
				<?php /* 
				********************************************************************************
				FULLY REMOVE FORM: START *******************************************************
				********************************************************************************
				
				<?php // Apply Form: Start ?>
                <div class="jlapplyform">
                    <form id="helios-application-form" method="post" enctype="multipart/form-data">
                        <div class="jlforminside">
                            <input type="hidden" value="<?php echo $entry->title; ?>" name="job_title" />
							
							<?php // Notice: Start ?>
                            <div class="jlsubmitnotice">
                                <span class="succ" style="display:none;"></span>
                                <div class="clear"></div>
                            </div>
                            <?php // Notice: Start ?>

                            <h3 class="">Request Information or Submit an Application For This Position</h3>

                                <?php // Form Fields: Start ?>
                            <div class="jlformfields">

								<?php // Name/Email: Start ?>
                                <div class="jlformrow double">
                                    <div class="jlformitem left">
                                        <label for="name">Name</label>
                                        <div class="jlforminput">
                                            <input type="text" name="full_name" id="full_name" class="text" />

                                            <div class="clear"></div>
                                        </div>

                                        <div class="clear"></div>
                                    </div>

                                    <div class="jlformitem right">
                                        <label for="email">Email Address</label>
                                        <div class="jlforminput">
                                            <input type="text" name="user_email" id="user_email" class="text" />

                                            <div class="clear"></div>
                                        </div>

                                        <div class="clear"></div>
                                    </div>

                                    <div class="clear"></div>
                                </div>
								<?php // Name/Email: End ?>

								<?php // Phone/City: Start ?>
                                <div class="jlformrow double">
                                    <div class="jlformitem left">
                                        <label for="phone">Phone Number</label>
                                        <div class="jlforminput">
                                            <input type="text" name="user_phone" id="user_phone" class="text" />

                                            <div class="clear"></div>
                                        </div>

                                        <div class="clear"></div>
                                    </div>

                                    <div class="jlformitem right">
                                        <label for="citystate">City and State</label>
                                        <div class="jlforminput">
                                            <input type="text" name="citystate" id="citystate" class="text" />

                                            <div class="clear"></div>
                                        </div>

                                        <div class="clear"></div>
                                    </div>

                                    <div class="clear"></div>
                                </div>
								<?php // Phone/City: End ?>

								<?php // Message: Start ?>
                                <div class="jlformrow">
                                    <div class="jlformitem">
                                        <label for="message">Your Message (or copy and paste your cover letter here)</label>
                                        <div class="jlforminput">
                                            <textarea id="message" name="coverletter" class="textarea" col="25" rows="5" value=""></textarea>

                                            <div class="clear"></div>
                                        </div>

                                        <div class="clear"></div>
                                    </div>

                                    <div class="clear"></div>
                                </div>
								<?php // Message: End ?>

								<?php // Upload: Start ?>
                                <div class="jlformrow">
                                    <div class="jlformitem">
                                        <label>If applying for this position, please attach your resume (File format should be <?php
                                      
                                      echo $ext;
                                        ?>).</label>
                                        <div class="jlforminput">

                                            <div>
                                                <span class="btn btn-success fileinput-button">
                                                    <i class="glyphicon glyphicon-plus"></i>
                                                    <span>Select files...</span>

                                                    <!-- The file input field used as target for the file upload widget -->
                                                    <input id="fileupload" type="file" name="files[]" multiple="" />
                                                </span>

                                                <div class="clear"></div>
                                            </div>

											<?php // Progress Bar: Start ?>
                                            <div id="progress" class="progress">
                                                <div class="progress-bar progress-bar-success"></div>

                                                <div class="clear"></div>
                                            </div>
											<?php // Progress Bar: End ?>

											<?php // Uploaded Files Container: Start ?>
                                            <div id="files" class="files jlitemfiles"></div>
											<?php // Uploaded Files Container: End ?>

                                            <div class="clear"></div>
                                        </div>

                                        <div class="clear"></div>
                                    </div>

                                    <div class="clear"></div>
                                </div>
								<?php // Upload: End ?>

								<?php // Captcha: Start ?>
								<input name="prefix" type="hidden" value="<?php echo $prefix; ?>" />
								
                                <div class="jlformrow captcha">
                                    <div class="jlformitem">
                                        <label for="anti">Please enter the security code below</label>
										<div class="jlforminput captchafld">
											<div class="captchacol top">
												<img src="<?php echo plugins_url(); ?>/helios-ats-importer/image.php" />
												
												<div class="clear"></div>
											</div>
											<div class="captchacol bottom">
												<input id="anti" name="anti" class="text antisecfld" col="25" rows="5" value="" />
												
												<div class="clear"></div>
											</div>
											
											<div class="clear"></div>
                                        </div>

                                        <div class="clear"></div>
                                    </div>

                                    <div class="clear"></div>
                                </div>
								<?php // Captcha: End ?>

								<?php // Submit: Start ?>
                                <div class="jlformrow" style="padding: 0;">
                                    <div class="jlformitem">
                                        <div class="jlforminput">
                                            <input type="submit" id="jlformsubmit" class="jlformsubmit button send submit" value="Send" name="submit" />

                                            <div class="clear"></div>
                                        </div>

                                        <div class="clear"></div>
                                    </div>

                                    <div class="clear"></div>
                                </div>
								<?php // Submit: End ?>

                                <div class="clear"></div>
                            </div>
							<?php // Form Fields: End ?>

                            <div class="clear"></div>
                        </div>
						
						<?php wp_nonce_field(plugin_basename(__FILE__), 'ps_nonce'); ?>
                    </form>

                    <div class="clear"></div>
                </div>
				<?php // Apply Form: End ?>
				
				********************************************************************************
				FULLY REMOVE FORM: END *********************************************************
				********************************************************************************
				*/ ?>

                <div class="clear"></div>
            </div>
            <?php
        } else {
            echo '<div class="joblist descpage">No Job found.</div>';
        }
    } else {
        echo '<div class="joblist descpage">No Job found.</div>';
    }
    ?>
    <?php
    /* Job List: End */
	
    $page = ob_get_contents();
    ob_end_clean();
    return $page;
}


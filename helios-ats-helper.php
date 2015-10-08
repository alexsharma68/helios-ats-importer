<?php
function truncate($input, $maxWords, $maxChars)
{
	$input=str_replace('Overview:','', $input);
	$input=str_replace('Responsibilities:','', $input);
	$input=str_replace('Qualifications:','', $input);
    $words = preg_split('/\s+/', $input);
    $words = array_slice($words, 0, $maxWords);
    $words = array_reverse($words);

    $chars = 0;
    $truncated = array();

    while(count($words) > 0)
    {
        $fragment = trim(array_pop($words));
        $chars += strlen($fragment);

        if($chars > $maxChars) break;

        $truncated[] = $fragment;
    }

    $result = implode($truncated, ' ');

    return $result . ($input == $result ? '' : '...');
}
	
function count_records($pubStatus='Y') {
	global $wpdb;
	global $subquery;
	if(!isset($_GET['category'])){
			$order=(get_option('short_helios')=='')?'h.pubDate DESC ':' c.category_name '. get_option('short_helios');
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
			h.publishedStatus='Y' order by ".$order;
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
			h.publishedStatus='Y' AND h.category LIKE '%".$_GET['category']."%' order by ".$order;
		}
	
	
	
	
	
	
	//$res = $wpdb->get_results("select * from " . $wpdb->prefix . HELIOS_TABLE . " WHERE 1= 1 and publishedStatus = '$pubStatus' $subquery order by id DESC ", OBJECT);
	return count($wpdb->get_results($sql));
}

function get_status($id) {
    global $wpdb;
    $result = $wpdb->get_results("select publishedStatus from " . $wpdb->prefix . HELIOS_TABLE. " WHERE id = '$id' ", OBJECT);
    foreach ($result as $entry) {
        $list_order = $entry->publishedStatus;
    }
    return $list_order;
}

function pagination($total_records,$limit,$targetpage,$page_num,$adjacents){
global $wpdb;

/* Setup page vars for display. */
if ($page_num == 0) $page_num = 1;					//if no page var is given, default to 1.
$prev = $page_num - 1;							//previous page is page - 1
$next = $page_num + 1;							//next page is page + 1
$lastpage = ceil($total_records/$limit);		//lastpage is = total pages / items per page, rounded up.
$lpm1 = $lastpage - 1;	
$pagination = "";
if($lastpage > 1)
	{	
		$pagination .= "<li><span>Page</span></li>";
		
		/*Prev Page*/
		if ($page_num > 1) {
            $pagination.= "<li><a href=\"$targetpage&page_num=$prev\">&laquo;</a>";  
		}
		if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
		{	
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $page_num)
					$pagination.= "<li><a href=\"javascript:;\" class=\"active\">$counter</li>";
				else
					$pagination.= "<li><a href=\"$targetpage&page_num=$counter\">$counter</a></li>";					
			}
		}
		elseif($lastpage > 5 + ($adjacents * 2))	//enough pages to hide some
		{
			//close to beginning; only hide later pages
			if($page_num < 1 + ($adjacents * 2))		
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $page_num)
						$pagination.= "<li><a href=\"javascript:;\" class=\"active\">$counter</li>";
					else
						$pagination.= "<li><a href=\"$targetpage&page_num=$counter\">$counter</a></li>";					
				}
				$pagination.= "...";
				$pagination.= "<li><a href=\"$targetpage&page_num=$lpm1\">$lpm1</a></li>";
				$pagination.= "<li><a href=\"$targetpage&page_num=$lastpage\">$lastpage</a></li>";		
			}
			//in middle; hide some front and some back
			elseif($lastpage - ($adjacents * 2) > $page_num && $page_num > ($adjacents * 2))
			{
				$pagination.= "<li><a href=\"$targetpage&page_num=1\">1</a></li>";
				$pagination.= "<li><a href=\"$targetpage&page_num=2\">2</a></li>";
				$pagination.= "...";
				for ($counter = $page_num - $adjacents; $counter <= $page_num + $adjacents; $counter++)
				{
					if ($counter == $page_num)
						$pagination.= "<li><a href=\"javascript:;\" class=\"active\">$counter</li>";
					else
						$pagination.= "<li><a href=\"$targetpage&page_num=$counter\">$counter</a></li>";					
				}
				$pagination.= "...";
				$pagination.= "<li><a href=\"$targetpage&page_num=$lpm1\">$lpm1</a></li>";
				$pagination.= "<li><a href=\"$targetpage&page_num=$lastpage\">$lastpage</a></li>";		
			}
			//close to end; only hide early pages
			else
			{
				$pagination.= "<li><a href=\"$targetpage&page_num=1\">1</a></li>";
				$pagination.= "<li><a href=\"$targetpage&page_num=2\">2</a></li>";
				$pagination.= "...";
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $page_num)
						$pagination.= "<li><a href=\"javascript:;\" class=\"active\">$counter</li>";
					else
						$pagination.= "<li><a href=\"$targetpage&page_num=$counter\">$counter</a></li>";					
				}
			}
		}
		
		//next button
        if ($page < $counter - 1) {
            $pagination.= "<li><a href=\"$targetpage&page_num=$next\">&raquo;</a>";
		}
		

		//$pagination.= "</div>\n";		
	}
   return $pagination;
	
}

function sendEmail($to,$fromName,$fromEmail,$subject,$message,$cc='',$bcc='',$files=array(),$debug=false)
	{
	// starting of headers
	
		if (strtoupper(substr(PHP_OS,0,3)=='WIN')) { 
				$eol="\r\n"; 
			} elseif (strtoupper(substr(PHP_OS,0,3)=='MAC')) { 
				$eol="\r"; 
			} else { 
				$eol="\n"; 
			}
	
	$headers = 'From: '.$fromName.' <'.$fromEmail.'>';
	if($cc != '')
	$headers .= $eol."Cc: ". $cc;
	if($bcc != '')
	$headers .= $eol."Bcc: ". $bcc;
	// boundary
	$semi_rand = md5(time());
	$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
	// headers for attachment
	$headers .= $eol."MIME-Version: 1.0".$eol . "Content-Type: multipart/mixed;".$eol . " boundary=\"{$mime_boundary}\"";
	// multipart boundary
	$message = "This is a multi-part message in MIME format.".$eol.$eol . "--{$mime_boundary}".$eol . "Content-Type: text/html; charset=\"iso-8859-1\"".$eol . "Content-Transfer-Encoding: 7bit".$eol.$eol . $message . $eol.$eol;
	if(count($files))
	$message .= "--{$mime_boundary}".$eol;
	// preparing attachments
	for($x=0;$x<count($files);$x++)
	{
	$file = fopen($files[$x],"rb");
	$data = fread($file,filesize($files[$x]));
	fclose($file);
	$data = chunk_split(base64_encode($data));
	
	$filename = basename($files[$x]);
	
	$message .= "Content-Type: {\"application/octet-stream\"};".$eol . " name=\"$files[$x]\"".$eol .
	"Content-Disposition: attachment;".$eol . " filename=\"$filename\"".$eol .
	"Content-Transfer-Encoding: base64".$eol.$eol . $data . $eol.$eol;
	if($x < (count($files)-1))
	$message .= "--{$mime_boundary}".$eol;
	else
	$message .= "--{$mime_boundary}--".$eol;
	}
	if($debug)
	{
	echo '$to: '.$to;
	echo '$fromName: '.$fromName;
	echo '$fromEmail: '.$fromEmail;
	echo '$subject: '.$subject;
	echo '$message: '.$message;
	echo '$cc: '.$cc;
	echo '$bcc: '.$bcc;
	echo '$file: '.print_r($file);
	}
	if(mail($to, $subject, $message, $headers))
	{
	return true;
	}
	else
	{
	return false;
	}
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

?>
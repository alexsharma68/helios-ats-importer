<?php
ini_set('max_execution_time',2000);
require_once('../../../wp-load.php');
global $wpdb;
$feed_url = get_option('helios_feed_url');
if(!empty($feed_url)){
	$stream=file_get_contents(get_option('helios_feed_url'));
	$stream=preg_replace("/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i",'<$1$2>', $stream);
	$x=toArray($stream);
	foreach($x['Row'] as $entry) {
		$entry=$entry['Column'];
		$overview=(formatText($entry[3])!='')?'<h3 class="heading overview">Overview:</h3>':'';
		$responsibilities=(formatText($entry[4])!='')?'<h3 class="heading responsibilities">Responsibilities:</h3>':'';
		$qualifications=(formatText($entry[5])!='')?'<h3 class="heading qualifications">Qualifications:</h3>':'';
		$wpdb->insert(
				$wpdb->prefix.HELIOS_TABLE,
				array(
					'title' => $entry[0],
					'location' => !empty($entry[1])?$entry[1].', '.$entry[2]:'',
					'description' =>$overview.formatText($entry[3]).$responsibilities.formatText($entry[4]).$qualifications.formatText($entry[5]),
					'category' =>category($entry[6]),
					'pubDate' => date("Y-m-d H:i:s",strtotime($entry[7])),
					
					'locationCountry' =>'',
					'locationState' => !empty($entry[2])?$entry[2]:'',
					'locationCity' => !empty($entry[1])?$entry[1]:'',
					'reqId' =>$entry[8],
					'publishedStatus' =>'Y'
			)
		);
	}
	echo '[{"msg":"Sync was successful."}]';
} else {
	echo '[{"msg":"An error occurred."}]';
}
function formatText( $string )
{
  $string = str_replace ( '<h1>', '<p><strong>', $string );
  $string = str_replace ( '<h2>', '<p><strong>', $string );
  $string = str_replace ( '<h3>', '<p><strong>', $string );
  $string = str_replace ( '<h4>', '<p><strong>', $string );
  $string = str_replace ( '<h5>', '<p><strong>', $string );
  $string = str_replace ( '<h6>', '<p><strong>', $string );
  $string = str_replace ( '</h1>', '</strong></p>', $string );
  $string = str_replace ( '</h2>', '</strong></p>', $string );
  $string = str_replace ( '</h3>', '</strong></p>', $string );
  $string = str_replace ( '</h4>', '</strong></p>', $string );
  $string = str_replace ( '</h5>', '</strong></p>', $string );
  $string = str_replace ( '</h6>', '</strong></p>', $string );
  $string = preg_replace('#(<[a-z ]*)(style=("|\')(.*?)("|\'))([a-z ]*>)#', '\\1\\6', $string);
  $pattern = "/<p[^>]*><\\/p[^>]*>/"; 
  $string = preg_replace($pattern, '', $string);
  $pattern = "/<span[^>]*><\\/span[^>]*>/"; 
  $string = preg_replace($pattern, '', $string);
  $string = str_replace ( '<span>', '', $string );
  $string = str_replace ( '</span>', '', $string );
  $string =  strip_tags($string,"<p><ul><b><em><li><a><div><strong><h1><h2><h3><h4><h5><h6><ol><table>");
  return $string;
}
function toArray($xml) {
        if ( is_string( $xml ) ) $xml = new SimpleXMLElement( $xml );
        $children = $xml->children();
        if ( !$children ) return (string) $xml;
        $arr = array();
        foreach ( $children as $key => $node ) {
            $node = toArray( $node );

            // support for 'anon' non-associative arrays
            if ( $key == 'anon' ) $key = count( $arr );

            // if the node is already set, put it into an array
            if ( isset( $arr[$key] ) ) {
                if ( !is_array( $arr[$key] ) || $arr[$key][0] == null ) $arr[$key] = array( $arr[$key] );
                $arr[$key][] = $node;
            } else {
                $arr[$key] = $node;
            }
        }
        return $arr;
 }
function category($e){
	global $wpdb;
	$result = $wpdb->get_results("select category_id, category_name from " . $wpdb->prefix . HELIOS_TABLE_CATEGORY." where category_name='".$e."'" , OBJECT);
	if(sizeof($result)>0){
		return $result[0]->category_id;
	}else{
		$wpdb->insert($wpdb->prefix . HELIOS_TABLE_CATEGORY, array(
                'category_name' =>$e,
                'status' => 'Y'
                )
            );
			return mysql_insert_id();
	}
}

?>
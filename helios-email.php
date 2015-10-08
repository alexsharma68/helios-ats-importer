<?php
require_once('../../../wp-load.php');
define('HELIOS__PLUGIN_URL', plugin_dir_url(__FILE__));
$filess='';
            $FileName = '';
            $Full_name = empty($_REQUEST['full_name']) ? "" : $_REQUEST['full_name'];
            $Email = empty($_REQUEST['user_email']) ? "" : $_REQUEST['user_email'];
            $Phone = empty($_REQUEST['user_phone']) ? "" : $_REQUEST['user_phone'];
            $Citystate = empty($_REQUEST['citystate']) ? "" : $_REQUEST['citystate'];
            $CoverLetter = empty($_REQUEST['coverletter']) ? "" : $_REQUEST['coverletter'];
            $job_title = empty($_REQUEST['job_title']) ? "" : $_REQUEST['job_title'];
            // upload files
            $upload_dir = wp_upload_dir();
            if(isset($_POST['uploadedFiles']) && is_array($_POST['uploadedFiles'])){
                foreach($_POST['uploadedFiles'] as $vel){
                    $upload_path = 'php/files/' . $vel;
                    //if(file_exists($upload_path)){
                        $filess[]= $upload_path;
                   // }
                }
            }    
            

            // send email

            $FromName = $Full_name;
            $Subect = 'Job Application for - '.$job_title;
            $Message = '
	<html>
	<head>
	
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	
	<title>Helios HR</title>
																																																																																																			
	<style type="text/css">
		.ReadMsgBody {width: 100%; background-color: #ffffff;}
		.ExternalClass {width: 100%; background-color: #ffffff;}
		body	 {width: 100%; background-color: #ffffff; margin:0; padding:0; -webkit-font-smoothing: antialiased;font-family: Georgia, Times, serif}
		table {border-collapse: collapse;}
		p {margin:0; padding:3px 0; font:bold 11px Arial, Helvetica, sans-serif;}
		
		@media only screen and (max-width: 640px)  {
						body[yahoo] .deviceWidth {width:440px!important; padding:0;}	
						body[yahoo] .center {text-align: center!important;}	 
				}
				
		@media only screen and (max-width: 479px) {
						body[yahoo] .deviceWidth {width:280px!important; padding:0;}	
						body[yahoo] .center {text-align: center!important;}	 
				}
	
	</style>
	</head>
	
	<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" yahoo="fix" style="font-family: Georgia, Times, serif">
	
	<!-- Wrapper -->
	<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
		<tr>
			<td width="100%" valign="top" bgcolor="#ffffff" style="padding-top:20px"></td>
		</tr>
		</table><!-- End Header -->
	
	<div style="height:15px">&nbsp;</div><!-- spacer -->
				
				<table width="580" cellspacing="0" cellpadding="0" border="0" bgcolor="#eeeeed" align="center" class="deviceWidth">
					<tbody>
					<tr>
						<td bgcolor="#eeeeed" style="font-size: 13px; color: #959595; font-weight: normal; text-align: left; font-family: Georgia, Times, serif; line-height: 24px; vertical-align: top; padding:10px 8px 10px 8px">
							
							<table>
								<tbody><tr>
									<td valign="middle" style="padding:0 10px 10px 0"><a style="text-decoration: none; color: #272727; font-size: 16px; color: #272727; font-weight: bold; font-family:Arial, sans-serif " href="#">HR Job Application</a>
									</td>
								</tr>
							</tbody></table>
							<p>The user with following information apply for this job.</p>
                                                        <p>Job Title : ' . $job_title . '</p>
							<p>Name : ' . $Full_name . '</p>
							<p>Email : ' . $Email . '</p>
							<p>Phone : ' . $Phone . '</p>
							<p>State : ' . $Citystate . '</p>
							<p>Cover Letter : ' . nl2br($CoverLetter) . '</p>
						</td>
					</tr>              
				</tbody></table>
			<div style="height:15px">&nbsp;</div><!-- spacer -->
				<!-- 4 Columns -->
				<table width="580" border="0" cellpadding="0" cellspacing="0" align="center" class="deviceWidth">
					<tr>
						<td bgcolor="#363636" style="padding:15px 0">
							<table width="580" border="0" cellpadding="0" cellspacing="0" align="center" class="deviceWidth">
								<tr>
									<td>                    
											<table width="100%" cellpadding="0" cellspacing="0"  border="0" align="left" class="deviceWidth">
												<tr>
													<td valign="top" style="padding:0 10px; font-size: 11px; color: #f1f1f1; font-weight: normal; font-family:Arial, Helvetica, sans-serif; line-height: 26px; vertical-align: top;">
														&copy; Copyright 2009-' . date('Y') . ' All Rights Reserved with Helios HR
													</td>
												</tr>
											</table>
							
									</td>
								</tr>
							</table>                                                              		
						</td>
					</tr>
				</table><!-- End 4 Columns -->
							
			</td>
		</tr>
	</table> <!-- End Wrapper -->
	
	</body>
	</html>';
        $ans=$_REQUEST['anti'];
        require_once( plugin_dir_path(__FILE__) . 'class/captcha.class.php' );
        $c = new Captcha;
        $result = $c->validate($ans);
        if($result){
        add_filter("wp_mail_content_type", "set_html_content_type" );
	$headers = "From: Helios Jobs <$Email>" . "\r\n";
	wp_mail(get_option('helios_admin_email'), 'Job Application from Helios HR', $Message,$headers, $filess);
        echo "yes";
        
        }else{
            
            echo "no";
        }
        function set_html_content_type(){
            return 'text/html';
        }
        ?>
<!DOCTYPE html>
<html>
    <head>
    <title>Era Captcha Test Page</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- CSS ile ilgili kısım -->
    <link rel="stylesheet" href="css/reset.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
</head>
<body>
    
<form name="captcha_test" id="captcha_test" method="post" action="index.php">
	<div class="box">
    	<h1>Test Form :</h1>
        
        <label>
        	<span>Full name</span>
            <input type="text" class="input_text" name="name" id="name"/>
        </label>
        
        <label>
        	<span>Captcha image</span>
            <input type="text" class="input_text" name="captcha" id="captcha"/>
            <img src="image.php" />
        </label>
        <?php
			//print_r($_POST);
			if (isset($_POST['submit']) and $_POST['submit'] == 'Submit Form' and $_POST['name'] != '' and $_POST['captcha'] != '') {
				require_once '../class/captcha.class.php';
				$c = new Captcha;
				$result = $c->validate($_POST['captcha']);
				if ($result)
				{
					echo '<label><code>Hi ' . $_POST['name'] . '; Your captcha code is valid. Thanks! =) </code> &nbsp;</label>';
				} else {
					echo '<label><code>Hi ' . $_POST['name'] . '; Your captcha code is not valid. Please try again! =( </code> &nbsp;</label>';
				}
			}
		?>

        <label>
            <input type="submit" name="submit" class="button" value="Submit Form" />
        </label>
	</div>
</form>

</body>
</html>

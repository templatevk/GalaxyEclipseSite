<?php
session_start();
include ("database/connect.php");
?>
<!DOCTYPE html>
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>Galaxy Eclipse</title>
<link rel="stylesheet" href="styles.css">
</head>
<body id="start" itemscope="" itemtype="http://schema.org/Article" data-twttr-rendered="true"><img id="STTBimg" style="opacity: 0.5; position: fixed; width: 50px; height: auto; display: none; z-index: 2147483647; border: 0px; padding: 0px; top: 20px; right: 20px; margin: 0px; cursor: pointer;" src="chrome-extension://chiikmhgllekggjhdfjhajkfdkcngplp/assets/img/arrows/arrow_blue.png">
	<div id="container">

		<div id="main" role="main">		

  <h1><a href="/" style="color:white;font-size:1.3em;">Galaxy Eclipse</a></h1>

  <div class="panel general-content">
    <header>
		<h2>Space <span>is closer</span> than it seems</h2>
    </header>
    <div class="content">
      <ol>
        <li class="clearfix">
			<h3>Create an account</h3>
			<div class="text"><p>Playing Galaxy Eclipse requires an account. Creating an Galaxy Eclipse account is as simple as filling in a short form, with no payment or home address information needed.</p></div>
			<div class="side signup-form">
            

<div id="input-form" style="margin-top:40px !important;">
<?php

include ("core/players_functions.php");
include ("core/general.php");

if($_GET['activation'] AND $_GET['activation']!='' AND $_GET['activation']!='success' AND $_GET['activation']!='error') 
{
	$activation_hash = $_GET['activation'];
	//Создаем запрос для проверки хеша
	$query = "SELECT * FROM `players_activation_hashes` WHERE activation_hash='".$activation_hash."' LIMIT 1";
	$query_result = mysql_fetch_array(mysql_query($query));
	//echo 'first= '. $query.'<br>';
	
	//Если не пустой запрос, то вытягываем из activation_hash -  ID плеера
	if($query_result['player_id'] != null) 
	{
		$player_id = $query_result['player_id'];
		//echo 'query_result["player_id"]= '. $query_result['player_id'].'<br>';
	}
	else 
	{
		$player_id = null;
	}
	
	if($player_id != null) 
	{
		$query = "UPDATE `players` SET `is_activated`=1 WHERE `player_id`='".$player_id."'";
		$query_result = mysql_query($query);
		if(empty($query_result)== false) {
			echo '<script>window.location="index.php?activation=success"</script>';
		}
		else 
		{
			echo '<script>window.location="index.php?activation=error"</script>';
		}
		
	}
	else 
	{
			echo '<script>window.location="index.php?activation=error"</script>';
	}
}
else if($_GET['activation'] AND $_GET['activation']!='' AND ($_GET['activation']=='success' OR $_GET['activation']=='error')) 
{
	if($_GET['activation'] AND $_GET['activation']=='success') 
	{
		echo '<div style="color:green; font-size:1.2em;">Account has been successfully activated!</br>'
		.'Now you can sign in using your data registering.</div>';
	}
	else if($_GET['activation'] AND $_GET['activation']=='error') 
	{
		echo '<div>Account activation error</div>';
	}
}
else if($_GET['registration'] AND $_GET['registration']!='' AND ($_GET['registration']=='success' OR $_GET['activation']=='error')) 
{
	if($_GET['registration'] AND $_GET['registration']=='success') 
	{
		echo '<div style="color:green;">Account has been successfully registered!</div>'
		.'<div style="color:red;">Please check you mail to activate your account.</div>';
	}
	else if($_GET['registration'] AND $_GET['registration']=='error') 
	{
		echo '<div>Account registration error</div>';
	}
}
else
{

	if(empty($_POST) === false)
	{
		//обязательные поля для регистрации
		$required_fields = array('username', 'password', 'password_again', 'first_name', 'email');
		foreach($_POST as $key=> $value)
		{
			if(empty($value) && in_array($key, $required_fields) === true)
			{
				$errors[] = 'Fields marked with an asteriks are required.';
				break 1;
			}
		}
		
		if(empty($errors) === true)
		{
			if($_POST['sid'] != $_SESSION['uid'])
			{
				$errors[] = 'You entered the worng captcha.';
			}
			if(player_exists($_POST['username']) === true)
			{
				$errors[] = 'Sorry the username \'' . $_POST['username'] . " is already taken.";
			}
			if(nickname_exists($_POST['nickname']) === true)
			{
				$errors[] = 'Sorry the nickname \'' . $_POST['nickname'] . " is already taken.";
			}
			if(preg_match("/\\s/", $_POST['username']) == true)
			{$errors[] = 'Your username must not contain any spaces.';}
			
			if(strlen($_POST['password']) < 6)
			{$errors[] = 'Your password must be least 6 characters.';}
			
			if($_POST['password'] != $_POST['password_again'])
			{$errors[] = 'Your password do not match.';}
			
			if(email_exists($_POST['email']) === true)
			{$errors[] = 'Sorry, the email \'' . $_POST['email'] . '\' is already in use.';}
			
			
			
			
		}
	}

	
	if(empty($errors) === false)
	{
		echo output_errors($errors);
	}
	
	else if(empty($_POST) === false && empty($errors) === true && $_POST['sid'] == $_SESSION['uid'])
	{
		//регистрируем юзера	
		$register_data = array(
			'username' 		=> $_POST['username'],
			'password' 		=> $_POST['password'],
			'nickname' 		=> $_POST['nickname'],
			'email' 		=> $_POST['email']
		);
		
		register_player($register_data);
		
		/******HASH*****/
		$activation_hash = md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'].time());
		/***************/
		
		echo $_POST['username'];
		
		$player_id = get_player_id_from_name($_POST['username']);
		
		echo $player_id;
		
		$query = "INSERT INTO `players_activation_hashes` VALUES(NULL, '$activation_hash', $player_id)";
		$query_result = mysql_query($query) or die("An error was detected when sending a message to a specified email.");
		if($query_result) 
		{
		
			// Для отправки e-mail в виде HTML устанавливаем необходимый mime-тип и кодировку
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=windows-1251' . "\r\n";

			// Откуда пришло
			$headers .= 'From: '.$_SERVER['HTTP_HOST']."\r\n";
			
			$message_path = $_SERVER['HTTP_HOST'].'/index.php?activation='.$activation_hash;
			//Здесь укажите электронный адрес, куда будут уходить сообщения
			$mailto = $_POST['email'];
			$subject = "Confirm the registration of account";
			$message = 'To activate your account, click the link below:<br> <a href="http://'.$message_path.'" target="_blank">http://'.$message_path.'</a><br>';
			$message .= ' or copy the link into the address bar of your browser and press Enter.';
			//Отправляем сообщение
			if(send_mail($mailto, $subject, $message, $headers) !== FALSE) 
			{
				echo '<script>window.location="index.php?registration=success"</script>';
			}
			else 
			{ 
				echo '<script>window.location="index.php?registration=error"</script>';
			}
		}
		else 
		{ 
			echo '<script>window.location="index.php?registration=error"</script>';
		}
	}

	$_SESSION['uid'] = mt_rand(100000,999999);

	echo'
	<form action="" method="POST">
		
		<input type="text" name="username" placeholder="Enter username*:">		
		<input type="text" name="nickname" placeholder="Enter nickname*:">		
		<input type="password" name="password" placeholder="Enter password*:">		
		<input type="password" name="password_again" placeholder="Retype password*:">
		<input type="email" name="email" placeholder="Enter email address*:">	
				<img style="border:1px solid #888888;margin:0px 0px 5px 0px;width:220px;height:30px;border-radius:5px;" src="capcha/capcha.php?sid='.$_SESSION['uid'].'"/><br>
				<input name="sid" type="text" value="" placeholder="Enter captcha*:">
		<input type="submit" class="btn" style="margin:0px !important;border-radius:5px;width:222px !important;" value="CREATE ACCOUNT">
	</form>
	';	
}
?>	
</div>

          </div>
        </li>
        <li class="clearfix">
			<h3>Client Download</h3>
			<div class="text"><p>Once you have an account, the next step is to download and install the game client. Galaxy Eclipse supports Windows and Linux operating systems, and for each the usual installer file type will be downloaded.</p></div>
			<div class="side">
<div class="download-button">
		<a href="/" class="primary-dlbutton">Download <span>Desktop client</span></a>
</div>
</div>
        </li>
      </ol>
    </div>
  </div>


		</div>

	</div>

</div>
</body>
</html>
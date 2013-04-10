<?php


function send_mail($email, $subject, $message, $headers) {
	if(mail($email, $subject, $message, $headers)) 
		return TRUE;
	else 
		return FALSE;
}

function register_player($register_data)
{
	array_walk($register_data, 'array_sanitize');
	$register_data['password'] = md5($register_data['password']);
	
	$fields = '`' . implode('`, `', array_keys($register_data)) . '`';
	$data = '\'' . implode('\', \'', $register_data) . '\'';
	$query = "INSERT INTO players ($fields) VALUES ($data)";
	//print_r($query);
	mysql_query($query);
}

function get_player_id_from_name($username)
{
	$username = sanitize($username);
	$query = mysql_query("SELECT player_id FROM players WHERE username = '$username'");
	return mysql_result($query , 0, 'player_id');
}

function player_exists($username)
{
	$username = sanitize($username);
	$query = mysql_query("SELECT COUNT(player_id) FROM players WHERE username = '$username'");
	return (mysql_result($query, 0) == 1) ? true : false;
}

function nickname_exists($nickname)
{
	$nickname = sanitize($nickname);
	$query = mysql_query("SELECT COUNT(player_id) FROM players WHERE username = '$nickname'");
	return (mysql_result($query, 0) == 1) ? true : false;
}

function email_exists($email)
{
	$email = sanitize($email);
	$query = mysql_query("SELECT COUNT(player_id) FROM players WHERE email = '$email'");
	return (mysql_result($query, 0) == 1) ? true : false;
}

function player_active($username)
{
	$username = sanitize($username);
	$query = mysql_query("SELECT COUNT(player_id) FROM players WHERE username = '$username' AND is_active = '1'");
	return (mysql_result($query, 0) == 1) ? true : false;
}

function player_banned($username)
{
	$username = sanitize($username);
	$query = mysql_query("SELECT COUNT(player_id) FROM players WHERE username = '$username' AND is_banned = '1'");
	return (mysql_result($query, 0) == 1) ? true : false;
}
?>   	
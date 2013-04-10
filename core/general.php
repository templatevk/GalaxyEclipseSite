<?php

function sanitize($data)
{
	return mysql_real_escape_string($data);
}

function array_sanitize(&$item)
{
	$item = mysql_real_escape_string($item);
}

function output_errors($errors)
{
	return '<ul style="color:red;"><li>' . implode('</li><li >', $errors) . '</li></ul>';
}
?>   	
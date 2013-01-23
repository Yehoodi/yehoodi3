<?php

class PasswordManager
{
	const PASS = "micahlewis";
	
	private function __construct()
	{
		// Create Password
		//$this->pass = "mypassword";
	}
	
	public static function getInstance()
	{
		static $instance;
		if(!is_object($instance))
		{
			$instance = new PasswordManager();
		}
		return $instance;
	}
	
	public function createPassword()
	{
		// Set a cookie with an encrypted version of the
		// password for one day
		setcookie("uid", md5(PasswordManager::PASS), time() + 86400, "/","yehoodi.com", false);
	}
	
	public function verifyPassword()
	{
		if($_COOKIE['uid'] == md5(PasswordManager::PASS))
		{
			return true;
		} 
		else
		{
			return false;
		}
	}
}
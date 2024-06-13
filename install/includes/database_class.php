<?php

class Database {

	// Function to the database and tables and fill them with the default data
	function create_database($data)
	{
		// Connect to the database
		$mysqli = new mysqli($data['hostname'],$data['username'],$data['password'],'');

		// Check for errors
		if(mysqli_connect_errno())
			return false;

		// Create the prepared statement
		$mysqli->query("CREATE DATABASE IF NOT EXISTS ".$data['database']);

		// Close the connection
		$mysqli->close();

		return true;
	}

	// Function to create the tables and fill them with the default data
	function create_tables($data)
	{

		// Connect to the database
		$mysqli = new mysqli($data['hostname'],$data['username'],$data['password'],$data['database']);

		// Check for errors
		if(mysqli_connect_errno())
			return false;

		// Open the default SQL file
		$query = file_get_contents('assets/install.sql');

		// Execute a multi query
		$mysqli->multi_query($query);

		sleep(10);

        $mysql = new mysqli($data['hostname'],$data['username'],$data['password'],$data['database']);

		$mysql->query("INSERT INTO `login` (`u_id`,`u_name`, `u_username`, `u_password`, `u_isactive`, `u_email`) VALUES (1,'".$data['adminname']."', '".$data['email']."', '".md5($data['syspassword'])."', '1', '".$data['email']."')");
			
		// Close the connection
		$mysql->close();

		return true;
	}
}
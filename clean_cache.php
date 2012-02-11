<?php

	//include the RainTPL class
	include "inc/rain.tpl.class.php";

	// clean all the compiled templates from cache
	array_map( "unlink", glob( raintpl::$cache_dir . "*.rtpl.php" ) );

    echo "Cache is clean. Nice!";
        
?>
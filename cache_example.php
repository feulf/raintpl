<?php

	//include the RainTPL class
	include "inc/rain.tpl.class.php";
	error_reporting( E_ALL );
	
	//initialize a Rain TPL object
	$tpl = new RainTPL();	

	if( $cache = $tpl->cache('page') )
		echo $cache;
	else{

		//variable assign example
		$variable = "Hello World!";
		$tpl->assign( "variable", $variable );

		//loop example
		$week = array( 'Monday', 'Tuersday', 'Wednesday', 'Friday', 'Saturday', 'Sunday' );
		$tpl->assign( "week", $week );

		//loop example 2
		$user = array(  array( 'name'=>'Jupiter', 'color'=>'yellow'),
						array( 'name'=>'Mars', 'color'=>'red' ),
						array( 'name'=>'Hearth', 'color'=>'blue' ),
		);
		
		$tpl->assign( "user", $user );
		$tpl->assign( "empty_array", array() );
		
		$info = array( 'title'=>'Rain TPL Example',
					   'copyright' => 'Copyright 2006 - 2011 Rain TPL<br>Project By Rain Team' );
	
		$tpl->assign( $info );

		//draw the template	
		echo $tpl->draw( 'page', $return_string = true );
}


?>
<?php

	//include the RainTPL class
	include "inc/rain.tpl.class.php";

	raintpl::configure("base_url", null );
	raintpl::configure("tpl_dir", "tpl/" );
	raintpl::configure("cache_dir", "tmp/" );

	//initialize a Rain TPL object
	$tpl = new RainTPL;

	//variable assign example
	$variable = "Hello World!";
	$tpl->assign( "variable", $variable );

	//loop example
	$week = array( 'Monday', 'Tuersday', 'Wednesday', 'Friday', 'Saturday', 'Sunday' );
	$tpl->assign( "week", $week );

	//loop example 2
	$user = array(  array( 'name'=>'Jupiter', 'color'=>'yellow'),
					array( 'name'=>'Mars', 'color'=>'red' ),
					array( 'name'=>'Earth', 'color'=>'blue' ),
	);
	$tpl->assign( "user", $user );

	//loop example with empty array
	$tpl->assign( "empty_array", array() );

	$info = array( 'title'=>'Rain TPL Example',
                       'copyright' => 'Copyright 2006 - 2011 Rain TPL<br>Project By Rain Team' );

	$tpl->assign( $info );

	global $global_variable;
	$global_variable = 'This is a global variable';


        // you can draw the output
        // $tpl->draw( 'page' );
        // or the template output string by setting $return_string = true:
	$html = $tpl->draw( 'page', $return_string = true );

        // and then draw the output
        echo $html;

?>
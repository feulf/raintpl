<?php

/**
 * RainTPL easy template engine compiles HTML templates to PHP.
 *
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation;
 * either version 3 of the License, or any later version.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
 * See the GNU Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 * 
 *  @author Federico Ulfo <rainelemental@gmail.com>
 *  @copyright 2006 - 2011 Federico Ulfo | www.federicoulfo.it
 *  @link http://www.raintpl.com
 *  @version 2.5
 *  @package RainFramework
 */




/**
 * Check template.
 * true: checks template update time, if changed it compile them
 * false: loads the compiled template. Set false if server doesn't have write permission for cache_directory.
 * 
 */
define( "RAINTPL_CHECK_TEMPLATE_UPDATE", true );




/**
 * Default cache expiration time (in seconds)
 * 
 */
define( "RAINTPL_CACHE_EXPIRE_TIME", 3600 );




/**
 * Default template extension (default html)
 * 
 */
define( "TPL_EXT", "html" );




/**
 * Load and draw templates
 *
 */
class RainTPL{

	// CONFIGURATION //
	static 	$tpl_dir = "tpl/",		// template directory
			$cache_dir = "tmp/",	// template cache/compile directory
			$base_url = null;		// template base url (useful for absolute path eg. http://www.raintpl.com )
	// CONFIGURATION //

	public  $var = array();				 // template var
	private $tpl = array(),				 // array of raintpl variables
		   	$static_cache = false;		 // static cache enabled / disabled



	/**
	 * Assign variable
	 * eg. 	$t->assign('name','duck');
	 *
	 * @param mixed $variable_name Name of template variable or associative array name/value
	 * @param mixed $value value assigned to this variable. Not set if variable_name is an associative array
	 */

	function assign( $variable, $value = null ){
		if( is_array( $variable ) )
			$this->var += $variable;
		elseif( is_object( $variable ) )
			$this->var += (array) $variable;
		else
			$this->var[ $variable ] = $value;
	}
	


	/**
	 * Draw the template
	 * eg. 	$html = $tpl->draw( 'demo', TRUE ); // return template in string
	 * or 	$tpl->draw( $tpl_name ); // echo the template
	 *
	 * @param string $tpl_name  template to load
	 * @param boolean $return_string  true=return a string, false=echo the template
	 * @return string
	 */

	function draw( $tpl_name, $return_string = false ){

		$this->check_template( $tpl_name );

		// load the template
		ob_start();
		// extract all variables assigned to the template
		include $this->tpl['cache_filename'];
		$raintpl_contents = ob_get_contents();
		ob_end_clean();

		// if static_cache is enabled I refresh the static cache
		if( $this->static_cache )
			file_put_contents( $this->tpl['static_cache_filename'], "<?php if(!class_exists('raintpl')){exit;}?>" . $raintpl_contents );

		// return or print the template
		if( $return_string ) return $raintpl_contents; else echo $raintpl_contents;

	}
	

	// by default the expire time is an hour
	function cache( $tpl_name, $expire_time = RAINTPL_CACHE_EXPIRE_TIME ){

		$this->check_template( $tpl_name );
		if( !$this->tpl['tpl_has_changed'] && file_exists( $this->tpl['static_cache_filename'] ) && ( time() - filemtime( $this->tpl['static_cache_filename'] ) < $expire_time ) )
			return substr( file_get_contents( $this->tpl['static_cache_filename'] ), 43 );
		else{
			//delete the cache of the selected template
			array_map( "unlink", glob( $this->tpl['static_cache_filename'] ) );
			$this->static_cache = true;
		}
	}
	
	// check if has to compile the template
	private function check_template( $tpl_name = null ){

		if( !isset($this->tpl['checked']) ){
			$this->tpl['tpl_has_changed'] 		= false;
			$this->tpl['tpl_basename'] 			= basename( $tpl_name );														// template basename
			$this->tpl['tpl_basedir'] 			= strpos($tpl_name,"/") ? dirname($tpl_name) . '/' : null;						// template basedirectory
			$this->tpl['tpl_dir'] 				= raintpl::$tpl_dir . $this->tpl['tpl_basedir'];								// template directory
			$this->tpl['tpl_filename'] 			= $this->tpl['tpl_dir'] . $this->tpl['tpl_basename'] . '.' . TPL_EXT;			// template filename
			$this->tpl['cache_dir'] 			= raintpl::$cache_dir . $this->tpl['tpl_dir'];									// cache directory
			$this->tpl['cache_filename']		= $this->tpl['cache_dir'] . $this->tpl['tpl_basename'] . '.php';				// cache filename				
			$this->tpl['static_cache_filename'] = $this->tpl['cache_dir'] . $this->tpl['tpl_basename'] . '.s.php';				// static cache filename

			// if the template doesn't exsist throw an error
			if( RAINTPL_CHECK_TEMPLATE_UPDATE && !file_exists( $this->tpl['tpl_filename'] ) ){
				trigger_error( 'Template '.$this->tpl['tpl_basename'].' not found!' );
				return '<div style="background:#f8f8ff;border:1px solid #aaaaff;padding:10px;">Template <b>'.$this->tpl['tpl_basename'].'</b> not found</div>';
			}

			// file doesn't exsist, or the template was updated, Rain will compile the template
			if( RAINTPL_CHECK_TEMPLATE_UPDATE && !file_exists( $this->tpl['cache_filename'] ) || filemtime($this->tpl['cache_filename']) < filemtime($this->tpl['tpl_filename']) ){
				$this->compileFile( $this->tpl['tpl_basedir'], $this->tpl['tpl_filename'], $this->tpl['cache_dir'], $this->tpl['cache_filename'] );
				$this->tpl['tpl_has_changed'] = true;
			}
			$this->tpl['checked'] = true;
		}
	}
	


	/**
	 * Compile and write the compiled template file
	 * @access private
	 */
	private function compileFile( $tpl_basedir, $tpl_filename, $cache_dir, $cache_filename ){

		//read template file
		$template_code = file_get_contents( $tpl_filename );

		//xml substitution
		$template_code = preg_replace( "/\<\?xml(.*?)\?\>/", "##XML\\1XML##", $template_code );

		//disable php tag
		$template_code = preg_replace( array("/\<\?/","/\?\>/"), array("&lt;?","?&gt;"), $template_code );

		//xml re-substitution
		$template_code = preg_replace( "/\#\#XML(.*?)XML\#\#/", "<?php echo '<?xml' . stripslashes('\\1') . '?>'; ?>", $template_code );

		//compile template
		$template_compiled = "<?php if(!class_exists('raintpl')){exit;}?>" . $this->compileTemplate( $template_code, $tpl_basedir );

		// create directories
		if( !is_dir( $cache_dir ) )
			mkdir( $cache_dir, 0755, true );

		//write compiled file
		file_put_contents( $cache_filename, $template_compiled );			
	}



	/**
	 * Compile template
	 * @access private
	 */
	private function compileTemplate( $template_code, $tpl_basedir ){

		//tag list
		$tag_regexp = '/(\{loop(?: name){0,1}="(?:\$){0,1}(?:.*?)"\})|(\{\/loop\})|(\{if(?: condition){0,1}="(?:.*?)"\})|(\{elseif(?: condition){0,1}="(?:.*?)"\})|(\{else\})|(\{\/if\})|(\{function="(?:.*?)"\})|(\{noparse\})|(\{\/noparse\})|(\{ignore\})|(\{\/ignore\})|(\{include="(?:.*?)"(?: cache="(?:.*?)")?\})/';

		//split the code with the tags regexp
		$template_code = preg_split ( $tag_regexp, $template_code, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY );

		//compile the code
		$compiled_code = $this->compileCode( $template_code, $tpl_basedir );

		//return the compiled code
		return $compiled_code;

	}



	/**
	 * Compile the code
	 * @access private
	 */
	private function compileCode( $parsed_code, $tpl_basedir ){

		//variables initialization
		$parent_loop[ $level = 0 ] = $loop_name = $loop_loopelse_open = $compiled_code = $compiled_return_code = $open_if = $comment_is_open = $ignore_is_open = null;

	 	//read all parsed code
	 	while( $html = array_shift( $parsed_code ) ){

	 		//close ignore tag
	 		if( !$comment_is_open && preg_match( '/\{\/ignore\}/', $html ) )
	 			$ignore_is_open = false;

	 		//code between tag ignore id deleted
	 		elseif( $ignore_is_open ){
	 			//ignore the code
	 		}

	 		//close no parse tag
	 		elseif( preg_match( '/\{\/noparse\}/', $html ) )
	 			$comment_is_open = false;	

	 		//code between tag noparse is not compiled
	 		elseif( $comment_is_open )
 				$compiled_code .= $html;

	 		//ignore
	 		elseif( preg_match( '/\{ignore\}/', $html ) )
	 			$ignore_is_open = true;

	 		//noparse
	 		elseif( preg_match( '/\{noparse\}/', $html ) )
	 			$comment_is_open = true;

			//include tag
			elseif( preg_match( '/(?:\{include="(.*?)"(?: cache="(.*?)"){0,1}\})/', $html, $code ) ){

				//variables substitution
				$include_var = $this->var_replace( $code[ 1 ], $left_delimiter = null, $right_delimiter = null, $php_left_delimiter = '".' , $php_right_delimiter = '."', $this_loop_name = $parent_loop[ $level ] );
				
				// if the cache is active
				if( isset($code[ 2 ]) )
					//dynamic include
					$compiled_code .= '<?php $tpl = new RainTPL();' .
								 'if( $cache = $tpl->cache( $cache_filename = basename("'.$include_var.'") ) )' .
								 '	echo $cache;' .
								 'else{ ' .
								 '$tpl_dir_temp = raintpl::$tpl_dir;' .
								 '$tpl->assign( $this->var );' .
								 'raintpl::$tpl_dir .= dirname("'.$include_var.'") . ( substr("'.$include_var.'",-1,1) != "/" ? "/" : "" );' .
								 ( !$this_loop_name ? null : '$tpl->assign( "key", $key'.$this_loop_name.' ); $tpl->assign( "value", $value'.$this_loop_name.' );' ).
								 '$tpl->draw( $cache_filename );'.
								 'raintpl::$tpl_dir = $tpl_dir_temp;' . 
								 '}' .
								 '?>';
				else
					//dynamic include
					$compiled_code .= '<?php $tpl = new RainTPL();' .
								 '$tpl_dir_temp = raintpl::$tpl_dir;' .
								 '$tpl->assign( $this->var );' .
								 'raintpl::$tpl_dir .= dirname("'.$include_var.'") . ( substr("'.$include_var.'",-1,1) != "/" ? "/" : "" );' .
								 ( !$this_loop_name ? null : '$tpl->assign( "key", $key'.$this_loop_name.' ); $tpl->assign( "value", $value'.$this_loop_name.' );' ).
								 '$tpl->draw( basename("'.$include_var.'") );'.
								 'raintpl::$tpl_dir = $tpl_dir_temp;' . 
								 '?>';
								 
			}

	 		//loop
	 		elseif( preg_match( '/\{loop(?: name){0,1}="(?:\$){0,1}(.*?)"\}/', $html, $code ) ){
	 			
	 			//increase the loop counter
	 			$level++;
	 			
	 			//name of this loop
				$parent_loop[ $level ] = $level;

				//replace the variable in the loop
				$var = $this->var_replace( '$' . $code[ 1 ], $tag_left_delimiter=null, $tag_right_delimiter=null, $php_left_delimiter=null, $php_right_delimiter=null, $level-1 );

				//loop variables
				$counter = "\$counter$level";	// count iteration
				$key = "\$key$level";			// key
				$value = "\$value$level";		// value
				
				//loop code
				$compiled_code .=  "<?php $counter=-1; if( isset($var) && is_array($var) && sizeof($var) ) foreach( $var as $key => $value ){ $counter++; ?>";

			}

			//close loop tag
			elseif( preg_match( '/\{\/loop\}/', $html ) ){

				//iterator
				$counter = "\$counter$level";

				//decrease the loop counter
				$level--;

				//close loop code
				$compiled_code .=  "<?php } ?>";
				
			}

			//if
			elseif( preg_match( '/\{if(?: condition){0,1}="(.*?)"\}/', $html, $code ) ){
				
				//increase open if counter (for intendation)
				$open_if++;
				
				//condition attribute
				$condition = $code[ 1 ];

				//variable substitution into condition (no delimiter into the condition)
				$parsed_condition = $this->var_replace( $condition, $tag_left_delimiter = null, $tag_right_delimiter = null, $php_left_delimiter = null, $php_right_delimiter = null, $parent_loop[ $level ] );				

				//if code
				$compiled_code .=   "<?php if( $parsed_condition ){ ?>";
			}

			//elseif
			elseif( preg_match( '/\{elseif(?: condition){0,1}="(.*?)"\}/', $html, $code ) ){

				//increase open if counter (for intendation)
				$open_if++;

				//condition attribute
				$condition = $code[ 1 ];

				//variable substitution into condition (no delimiter into the condition)
				$parsed_condition = $this->var_replace( $condition, $tag_left_delimiter = null, $tag_right_delimiter = null, $php_left_delimiter = null, $php_right_delimiter = null, $parent_loop[ $level ] );				

				//elseif code
				$compiled_code .=   "<?php }elseif( $parsed_condition ){ ?>";
			}

			//else
			elseif( preg_match( '/\{else\}/', $html ) ){

				//else code
				$compiled_code .=   '<?php }else{ ?>';

			}
						
			//close if tag
			elseif( preg_match( '/\{\/if}/', $html ) ){
				
				//decrease if counter
				$open_if--;
				
				// close if code 
				$compiled_code .=   '<?php } ?>';

			}

			//function
			elseif( preg_match( '/\{function="(.*?)(\((.*?)\)){0,1}"\}/', $html, $code ) ){

				//function
				$function = $code[ 1 ];

				//parse the parameters
				$parsed_param = isset( $code[2] ) ? $this->var_replace( $code[2], $tag_left_delimiter = null, $tag_right_delimiter = null, $php_left_delimiter = null, $php_right_delimiter = null, $parent_loop[ $level ] ) : '()';

				//if code
				$compiled_code .=   "<?php echo {$function}{$parsed_param}; ?>";
			}

			//all html code
			else{

				//path replace (src of img, background and href of link)
				$html = $this->path_replace( $html, $tpl_basedir );

				//variables substitution (es. {$title})
				$compiled_code .= $this->var_replace( $html, $left_delimiter = '\{', $right_delimiter = '\}', $php_left_delimiter = '<?php ', $php_right_delimiter = ';?>', $parent_loop[ $level ], $echo = true );

			}
		}

		return $compiled_code;
	}
	

	
	/**
	 * replace the path of image src, link href
	 * url => template_dir/url
	 * url# => url
	 * http://url => http://url
	 * 
	 * @param string $html 
	 * @return string html sostituito
	 */
	private function path_replace( $html, $tpl_basedir ){
		
		$exp = array( '/src=(?:")http\:\/\/([^"]+?)(?:")/i', '/src=(?:")([^"]+?)#(?:")/i', '/src="(.*?)"/', '/src=(?:\@)([^"]+?)(?:\@)/i', '/<link(.*?)href=(?:")http\:\/\/([^"]+?)(?:")/i', '/<link(.*?)href=(?:")([^"]+?)#(?:")/i', '/<link(.*?)href="(.*?)"/', '/<link(.*?)href=(?:\@)([^"]+?)(?:\@)/i' );
		$sub = array( 'src=@http://$1@', 'src=@$1@', 'src="' . raintpl::$base_url . raintpl::$tpl_dir . $tpl_basedir . '\\1"', 'src="$1"', '<link$1href=@http://$2@', '<link$1href=@$2@' , '<link$1href="' . raintpl::$base_url . raintpl::$tpl_dir . $tpl_basedir . '$2"', '<link$1href="$2"' );

		return preg_replace( $exp, $sub, $html );
	}



	/**
	 * Variable substitution
	 *
	 * @param string $html Html code
	 * @param string $tag_left_delimiter default {
	 * @param string $tag_right_delimiter default }
	 * @param string $php_left_delimiter default <?php=
	 * @param string $php_right_delimiter  default ;?>
	 * @param string $loop_name Loop name
	 * @param string $echo if is true make the variable echo
	 * @return string Replaced code
	 */
	function var_replace( $html, $tag_left_delimiter, $tag_right_delimiter, $php_left_delimiter = null, $php_right_delimiter = null, $loop_name = null, $echo = null ){


		// const
		$html = preg_replace( '/\{\#(\w+)\#\}/', $php_left_delimiter . ( $echo ? " echo " : null ) . '\\1' . $php_right_delimiter, $html );

		
		//all variables
		preg_match_all( '/' . $tag_left_delimiter . '\$(\w+(?:\.\${0,1}(?:\w+))*(?:\[\${0,1}(?:\w+)\])*(?:\-\>\${0,1}(?:\w+))*)(.*?)' . $tag_right_delimiter . '/', $html, $matches );

		$n = sizeof( $matches[ 0 ] );
		for( $i = 0; $i < $n; $i++ ){

			//complete tag ex: {$news.title|substr:0,100}
			$tag = $matches[ 0 ][ $i ];

			//variable name ex: news.title
			$var = $matches[ 1 ][ $i ];
			
			//function and parameters associate to the variable ex: substr:0,100
			$extra_var = $matches[ 2 ][ $i ];
			$extra_var = $this->var_replace( $extra_var, null, null, null, null, $loop_name );
			
			// check if there's an operator = in the variable tags, if there's this is an initialization so it will not output any value
			$is_init_variable = preg_match( "/^(\s*?)\=[^=](.*?)$/", $extra_var );
			
			//function associate to variable
			$function_var = ( $extra_var and $extra_var[0] == '|') ? substr( $extra_var, 1 ) : null;
			
			//variable path split array (ex. $news.title o $news[title]) or object (ex. $news->title)
			$temp = preg_split( "/\.|\[|\-\>/", $var );
			
			//variable name
			$var_name = $temp[ 0 ];
			
			//variable path
			$variable_path = substr( $var, strlen( $var_name ) );
			
			//parentesis transform [ e ] in [" e in "]
			$variable_path = str_replace( '[', '["', $variable_path );
			$variable_path = str_replace( ']', '"]', $variable_path );
			
			//transform .$variable in ["$variable"]
			$variable_path = preg_replace('/\.\$(\w+)/', '["$\\1"]', $variable_path );
			
			//transform [variable] in ["variable"]
			$variable_path = preg_replace('/\.(\w+)/', '["\\1"]', $variable_path );

			//if there's a function
			if( $function_var ){
				
				//split function by function_name and parameters (ex substr:0,100)
				$function_split = explode( ':', $function_var, 2 );
				
				//function name
				$function = $function_split[ 0 ];
				
				//function parameters
				$params = ( isset( $function_split[ 1 ] ) ) ? $function_split[ 1 ] : null;

			}
			else
				$function = $params = null;

			if( $var_name == 'GLOBALS' )
				$php_var = '$GLOBALS' . $variable_path;
			
			//if it is inside a loop
			elseif( $loop_name ){
				//verify the variable name
				if( $var_name == 'key' )
					$php_var = '$key' . $loop_name;
				elseif( $var_name == 'value' )
					$php_var = '$value' . $loop_name . $variable_path;
				elseif( $var_name == 'counter' )
					$php_var = '$counter' . $loop_name;
				else
					$php_var = "\$this->var['" . $var_name . "']" . $variable_path;
			}else
				$php_var = "\$this->var['" . $var_name . "']" . $variable_path;

			// compile the variable for php
			if( isset( $function ) )
				$php_var = $php_left_delimiter . ( !$is_init_variable && $echo ? 'echo ' : null ) . ( $params ? "( $function( $php_var, $params ) )" : "$function( $php_var )" ) . $php_right_delimiter;
			else
				$php_var = $php_left_delimiter . ( !$is_init_variable && $echo ? 'echo ' : null ) . $php_var . $extra_var . $php_right_delimiter;

			$html = str_replace( $tag, $php_var, $html );

		}
		
		return $html;
	}

}




?>
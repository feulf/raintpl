<?php

/**
 *  RainTPL
 *  -------
 *	Realized by Federico Ulfo & maintained by the Rain Team
 *	Distributed under GNU/LGPL 3 License
 * 
 *  @version 2.6.2
 */


class RainTPL{

	// -------------------------
	// 	CONFIGURATION 
	// -------------------------

		/**
		 * Template directory
		 *
		 * @var string
		 */
		static $tpl_dir = "tpl/";


		/**
		 * Cache directory. Is the directory where RainTPL will compile the template and save the cache
		 *
		 * @var string
		 */
		static $cache_dir = "tmp/";


		/**
		 * Template base URL. RainTPL will add this URL to the relative paths of element selected in $path_replace_list.
		 *
		 * @var string
		 */
		static $base_url = null;


		/**
		 * Template extension.
		 *
		 * @var string
		 */
		static $tpl_ext = "html";


		/**
		 * Path replace is a cool features that replace all relative paths of images (<img src="...">), stylesheet (<link href="...">), script (<script src="...">) and link (<a href="...">)
		 * Set true to enable the path replace.
		 *
		 * @var unknown_type
		 */
		static $path_replace = true;


		/**
		 * You can set what the path_replace method will replace.
		 * Avaible options: a, img, link, script
		 *
		 * @var array
		 */
		static $path_replace_list = array( 'a', 'img', 'link', 'script' ); 


		/**
		 * You can define in the black list what string are disabled into the template tags
		 *
		 * @var unknown_type
		 */
		static $black_list = array( '\$this', 'raintpl::', 'self::', '_SESSION', '_SERVER', '_ENV',  'eval', 'exec', 'unlink', 'rmdir' );


		/**
		 * Check template.
		 * true: checks template update time, if changed it compile them
		 * false: loads the compiled template. Set false if server doesn't have write permission for cache_directory.
		 * 
		 */
		static $check_template_update = true;

	// -------------------------


	// -------------------------
	// 	RAINTPL VARIABLES
	// -------------------------

		/**
		 * Is the array where RainTPL keep the variables assigned
		 *
		 * @var array
		 */
		public $var = array();
	
		private $tpl = array(),				 // variables to keep the template directories and info
			   	$static_cache = false;		 // static cache enabled / disabled

	// -------------------------



	const CACHE_EXPIRE_TIME = 3600; // default cache expire time = hour



	/**
	 * Assign variable
	 * eg. 	$t->assign('name','yoda');
	 *
	 * @param mixed $variable_name Name of template variable or associative array name/value
	 * @param mixed $value value assigned to this variable. Not set if variable_name is an associative array
	 */

	function assign( $variable, $value = null ){
		if( is_array( $variable ) )
			$this->var += $variable;
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

		// compile the template if necessary and set the template filepath
		$this->check_template( $tpl_name );

		//----------------------
		// load the template
		//----------------------

			ob_start();
			extract( $this->var );
			include $this->tpl['cache_filename'];
			$raintpl_contents = ob_get_contents();
			ob_end_clean();
		
		//----------------------


		//----------------------
		// save the static cache
		//----------------------

			if( $this->static_cache )
				file_put_contents( $this->tpl['static_cache_filename'], "<?php if(!class_exists('raintpl')){exit;}?>" . $raintpl_contents );
		
		//----------------------
		

		// free memory
		unset( $this->tpl );

		// return or print the template
		if( $return_string ) return $raintpl_contents; else echo $raintpl_contents;

	}
	


	
	/**
	 * If exists a valid cache for this template it returns the cache
	 *
	 * @param string $tpl_name Name of template (set the same of draw)
	 * @param int $expiration_time Set after how many seconds the cache expire and must be refreshed 
	 * @return string it return the HTML or null if the cache must be recreated
	 */

	function cache( $tpl_name, $expire_time = self::CACHE_EXPIRE_TIME ){

		if( !$this->check_template( $tpl_name ) && file_exists( $this->tpl['static_cache_filename'] ) && ( time() - filemtime( $this->tpl['static_cache_filename'] ) < $expire_time ) )
			return substr( file_get_contents( $this->tpl['static_cache_filename'] ), 43 );
		else{
			//delete the cache of the selected template
			array_map( "unlink", glob( $this->tpl['static_cache_filename'] ) );
			$this->static_cache = true;
		}
	}



	/**
	 * Configure the settings of RainTPL
	 *
	 */
	static function configure( $setting, $value ){
		if( is_array( $setting ) )
			foreach( $setting as $key => $value )
				$this->configure( $key, $value );	
		else if( property_exists( "raintpl", $setting ) )
			self::$$setting = $value;
	}
	
	
	
	// check if has to compile the template
	// return true if the template has changed
	private function check_template( $tpl_name ){

		if( !isset($this->tpl['checked']) ){
			
			$tpl_basename = basename( $tpl_name );														// template basename
			$tpl_basedir = strpos($tpl_name,"/") ? dirname($tpl_name) . '/' : null;						// template basedirectory
			$tpl_dir = self::$tpl_dir . $tpl_basedir;								// template directory
			$this->tpl['tpl_filename'] = $tpl_dir . $tpl_basename . '.' . self::$tpl_ext;	// template filename
			$cache_dir = self::$cache_dir . $tpl_dir;	// cache directory
			$temp_cache_filename = $cache_dir . $tpl_basename;
			$this->tpl['cache_filename']		= $temp_cache_filename . '.php';	// cache filename
			$this->tpl['static_cache_filename'] = $temp_cache_filename . '.s.php';	// static cache filename			

			// if the template doesn't exsist throw an error
			if( self::$check_template_update && !file_exists( $this->tpl['tpl_filename'] ) ){
				trigger_error( 'Template '.$tpl_basename.' not found!' );
				return '<div style="background:#f8f8ff;border:1px solid #aaaaff;padding:10px;">Template <b>'.$tpl_basename.'</b> not found</div>';
			}

			// file doesn't exsist, or the template was updated, Rain will compile the template
			if( !file_exists( $this->tpl['cache_filename'] ) || ( self::$check_template_update && filemtime($this->tpl['cache_filename']) < filemtime( $this->tpl['tpl_filename'] ) ) ){
				$this->compileFile( $tpl_basename, $tpl_basedir, $this->tpl['tpl_filename'], $cache_dir, $this->tpl['cache_filename'] );
				return true;
			}
			$this->tpl['checked'] = true;
		}
	}




	/**
	 * Compile and write the compiled template file
	 * @access private
	 */
	private function compileFile( $tpl_basename, $tpl_basedir, $tpl_filename, $cache_dir, $cache_filename ){

		// delete the old template file
		array_map( "unlink", glob( $cache_dir . $tpl_basename . "*.php" ) );

		//read template file
		$this->tpl['source'] = $template_code = file_get_contents( $tpl_filename );

		//xml substitution
		$template_code = preg_replace( "/\<\?xml(.*?)\?\>/", "##XML\\1XML##", $template_code );

		//disable php tag
		$template_code = preg_replace( array("/\<\?/","/\?\>/"), array("&lt;?","?&gt;"), $template_code );

		//xml re-substitution
		$template_code = preg_replace( "/\#\#XML(.*?)XML\#\#/", "<?php echo '<?xml' . stripslashes('\\1') . '?>'; ?>", $template_code );

		//compile template
		$template_compiled = "<?php if(!class_exists('raintpl')){exit;}?>" . $this->compileTemplate( $template_code, $tpl_basedir );

		// fix the php-eating-newline-after-closing-tag-problem
		$template_compiled = str_replace( "?>\n", "?>\n\n", $template_compiled );

		// create directories
		if( !is_dir( $cache_dir ) )
			mkdir( $cache_dir, 0755, true );

		if( !is_writable( $cache_dir ) )
			die( "Cache directory <b>$cache_dir</b> doesn't have write permission. Set write permission or set RAINTPL_CHECK_TEMPLATE_UPDATE to false. More details on <a target=_blank href=http://www.raintpl.com/Documentation/Documentation-for-PHP-developers/Configuration/>Configuration</a>");

		//write compiled file
		file_put_contents( $cache_filename, $template_compiled );			
	}



	/**
	 * Compile template
	 * @access private
	 */
	private function compileTemplate( $template_code, $tpl_basedir ){

		//tag list
		$tag_regexp = array( 	'loop' 			=> '(\{loop(?: name){0,1}="\${0,1}(?:.*?)"\})',
								'loop_close'	=> '(\{\/loop\})',
								'if'			=> '(\{if(?: condition){0,1}="(?:.*?)"\})',
								'elseif'		=> '(\{elseif(?: condition){0,1}="(?:.*?)"\})',
								'else'			=> '(\{else\})',
								'if_close'		=> '(\{\/if\})',
								'function'		=> '(\{function="(?:.*?)"\})',
								'noparse'		=> '(\{noparse\})',
								'noparse_close' => '(\{\/noparse\})',
								'ignore'		=> '(\{ignore\})',
								'ignore_close'	=> '(\{\/ignore\})',
								'include'		=> '(\{include="(?:.*?)"(?: cache="(?:.*?)")?\})',
								'template_info'	=> '(\{\$template_info\})',
							);
		
		$tag_regexp = "/" . join( "|", $tag_regexp ) . "/";

		//split the code with the tags regexp
		$template_code = preg_split ( $tag_regexp, $template_code, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY );

		//path replace (src of img, background and href of link)
		$template_code = $this->path_replace( $template_code, $tpl_basedir );

		//compile the code
		$compiled_code = $this->compileCode( $template_code );

		//return the compiled code
		return $compiled_code;

	}



	/**
	 * Compile the code
	 * @access private
	 */
	private function compileCode( $parsed_code ){

		//variables initialization
		$parent_loop[ $level = 0 ] = $loop_name = $compiled_code = $open_if = $comment_is_open = $ignore_is_open = null;

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
					$compiled_code .= '<?php $tpl = new RainTPL;' .
								 'if( $cache = $tpl->cache( $cache_filename = basename("'.$include_var.'") ) )' .
								 '	echo $cache;' .
								 'else{ ' .
								 '$tpl_dir_temp = self::$tpl_dir;' .
								 '$tpl->assign( $this->var );' .
								 'self::$tpl_dir .= dirname("'.$include_var.'") . ( substr("'.$include_var.'",-1,1) != "/" ? "/" : "" );' .
								 ( !$this_loop_name ? null : '$tpl->assign( "key", $key'.$this_loop_name.' ); $tpl->assign( "value", $value'.$this_loop_name.' );' ).
								 '$tpl->draw( $cache_filename );'.
								 'self::$tpl_dir = $tpl_dir_temp;' . 
								 '}' .
								 '?>';
				else
					//dynamic include
					$compiled_code .= '<?php $tpl = new RainTPL;' .
								 '$tpl_dir_temp = self::$tpl_dir;' .
								 '$tpl->assign( $this->var );' .
								 'self::$tpl_dir .= dirname("'.$include_var.'") . ( substr("'.$include_var.'",-1,1) != "/" ? "/" : "" );' .
								 ( !$this_loop_name ? null : '$tpl->assign( "key", $key'.$this_loop_name.' ); $tpl->assign( "value", $value'.$this_loop_name.' );' ).
								 '$tpl->draw( basename("'.$include_var.'") );'.
								 'self::$tpl_dir = $tpl_dir_temp;' . 
								 '?>';
								 
			}

	 		//loop
	 		elseif( preg_match( '/\{loop(?: name){0,1}="\${0,1}(.*?)"\}/', $html, $code ) ){
	 			
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
				
				//tag
				$tag = $code[ 0 ];

				//condition attribute
				$condition = $code[ 1 ];
				
				// check if there's any function disabled by black_list
				$this->function_check( $tag );

				//variable substitution into condition (no delimiter into the condition)
				$parsed_condition = $this->var_replace( $condition, $tag_left_delimiter = null, $tag_right_delimiter = null, $php_left_delimiter = null, $php_right_delimiter = null, $parent_loop[ $level ] );				

				//if code
				$compiled_code .=   "<?php if( $parsed_condition ){ ?>";
			}

			//elseif
			elseif( preg_match( '/\{elseif(?: condition){0,1}="(.*?)"\}/', $html, $code ) ){

				//tag
				$tag = $code[ 0 ];

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

				//tag
				$tag = $code[ 0 ];

				//function
				$function = $code[ 1 ];

				// check if there's any function disabled by black_list
				$this->function_check( $tag );

				//parse the parameters
				$parsed_param = isset( $code[2] ) ? $this->var_replace( $code[2], $tag_left_delimiter = null, $tag_right_delimiter = null, $php_left_delimiter = null, $php_right_delimiter = null, $parent_loop[ $level ] ) : '()';

				//if code
				$compiled_code .=   "<?php echo {$function}{$parsed_param}; ?>";
			}

			// show all vars
			elseif( preg_match( '/\{\$template_info\}/', $html, $code ) ){

				//tag
				$tag = $code[ 0 ];

				//if code
				$compiled_code .=   '<?php echo "<pre>"; print_r( $this->var ); echo "</pre>"; ?>';
			}


			//all html code
			else{
				
				//variables substitution (es. {$title})
				$compiled_code .= $this->html_var_replace( $html, $left_delimiter = '\{', $right_delimiter = '\}', $php_left_delimiter = '<?php ', $php_right_delimiter = ';?>', $parent_loop[ $level ], $echo = true );

			}
		}
		
		if( $open_if > 0 )
			die( "Error! You need to close an {if} tag in <b>". $this->tpl['tpl_filename'] ." </b>template" );

		return $compiled_code;
	}
	

	
	/**
	 * replace the path of image src, link href and a href.
	 * url => template_dir/url
	 * url# => url
	 * http://url => http://url
	 * 
	 * @param string $html 
	 * @return string html sostituito
	 */
	private function path_replace( $html, $tpl_basedir ){
		
		if( self::$path_replace ){

			$exp = $sub = array();

			if( in_array( "img", self::$path_replace_list ) ){
				$exp = array( '/<img(.*?)src=(?:")http\:\/\/([^"]+?)(?:")/i', '/<img(.*?)src=(?:")([^"]+?)#(?:")/i', '/<img(.*?)src="(.*?)"/', '/<img(.*?)src=(?:\@)([^"]+?)(?:\@)/i' );
				$sub = array( '<img$1src=@http://$2@', '<img$1src=@$2@', '<img$1src="' . self::$base_url . self::$tpl_dir . $tpl_basedir . '$2"', '<img$1src="$2"' );
			}
			
			if( in_array( "script", self::$path_replace_list ) ){
				$exp = array_merge( $exp , array( '/<script(.*?)src=(?:")http\:\/\/([^"]+?)(?:")/i', '/<script(.*?)src=(?:")([^"]+?)#(?:")/i', '/<script(.*?)src="(.*?)"/', '/<script(.*?)src=(?:\@)([^"]+?)(?:\@)/i' ) );
				$sub = array_merge( $sub , array( '<script$1src=@http://$2@', '<script$1src=@$2@', '<script$1src="' . self::$base_url . self::$tpl_dir . $tpl_basedir . '$2"', '<script$1src="$2"' ) );
			}
			
			if( in_array( "link", self::$path_replace_list ) ){
				$exp = array_merge( $exp , array( '/<link(.*?)href=(?:")http\:\/\/([^"]+?)(?:")/i', '/<link(.*?)href=(?:")([^"]+?)#(?:")/i', '/<link(.*?)href="(.*?)"/', '/<link(.*?)href=(?:\@)([^"]+?)(?:\@)/i' ) );
				$sub = array_merge( $sub , array( '<link$1href=@http://$2@', '<link$1href=@$2@' , '<link$1href="' . self::$base_url . self::$tpl_dir . $tpl_basedir . '$2"', '<link$1href="$2"' ) );
			}
			
			if( in_array( "a", self::$path_replace_list ) ){
				$exp = array_merge( $exp , array( '/<a(.*?)href=(?:")http\:\/\/([^"]+?)(?:")/i', '/<a(.*?)href="(.*?)"/' ) );
				$sub = array_merge( $sub , array( '<a$1href=@http://$2@',  '<a$1href="' . self::$base_url . '$2"' ) );
			}

			return preg_replace( $exp, $sub, $html );
			
		}
		else
			return $html;

	}



	
	// replace var const and functions
	function html_var_replace( $html, $tag_left_delimiter, $tag_right_delimiter, $php_left_delimiter = null, $php_right_delimiter = null, $loop_name = null, $echo = null ){

		// const
		$html = preg_replace( '/\{\#(\w+)\#{0,1}\}/', $php_left_delimiter . ( $echo ? " echo " : null ) . '\\1' . $php_right_delimiter, $html );

		preg_match_all( '/' . '\{\#{0,1}(\"{0,1}.*?\"{0,1})(\|\w.*?)\#{0,1}\}' . '/', $html, $matches );
		for( $i=0, $n=count($matches[0]); $i<$n; $i++ ){

			//complete tag ex: {$news.title|substr:0,100}
			$tag = $matches[ 0 ][ $i ];

			//variable name ex: news.title
			$var = $matches[ 1 ][ $i ];

			//function and parameters associate to the variable ex: substr:0,100
			$extra_var = $matches[ 2 ][ $i ];
			
			// check if there's any function disabled by black_list
			$this->function_check( $tag );

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
			
			$php_var = $var_name . $variable_path;

			// compile the variable for php
			if( isset( $function ) ){
				if( $php_var )
					$php_var = $php_left_delimiter . ( !$is_init_variable && $echo ? 'echo ' : null ) . ( $params ? "( $function( $php_var, $params ) )" : "$function( $php_var )" ) . $php_right_delimiter;
				else
					$php_var = $php_left_delimiter . ( !$is_init_variable && $echo ? 'echo ' : null ) . ( $params ? "( $function( $params ) )" : "$function()" ) . $php_right_delimiter;
			}
			else
				$php_var = $php_left_delimiter . ( !$is_init_variable && $echo ? 'echo ' : null ) . $php_var . $extra_var . $php_right_delimiter;

			$html = str_replace( $tag, $php_var, $html );

		}
		
		$html = $this->var_replace( $html, $tag_left_delimiter, $tag_right_delimiter, $php_left_delimiter, $php_right_delimiter, $loop_name, $echo );
		return $html;

	}
	
	
	
	function var_replace( $html, $tag_left_delimiter, $tag_right_delimiter, $php_left_delimiter = null, $php_right_delimiter = null, $loop_name = null, $echo = null ){

		//all variables
		preg_match_all( '/' . $tag_left_delimiter . '\$(\w+(?:\.\${0,1}(?:\w+))*(?:\[\${0,1}(?:\w+)\])*(?:\-\>\${0,1}(?:\w+))*)(.*?)' . $tag_right_delimiter . '/', $html, $matches );
		for( $i=0, $n=count($matches[0]); $i<$n; $i++ ){

			//complete tag ex: {$news.title|substr:0,100}
			$tag = $matches[ 0 ][ $i ];

			//variable name ex: news.title
			$var = $matches[ 1 ][ $i ];
			
			//function and parameters associate to the variable ex: substr:0,100
			$extra_var = $matches[ 2 ][ $i ];
			
			// check if there's any function disabled by black_list
			$this->function_check( $tag );
			
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
			
			//if it is inside a loop
			if( $loop_name ){
				//verify the variable name
				if( $var_name == 'key' )
					$php_var = '$key' . $loop_name;
				elseif( $var_name == 'value' )
					$php_var = '$value' . $loop_name . $variable_path;
				elseif( $var_name == 'counter' )
					$php_var = '$counter' . $loop_name;
				else
					$php_var = "\$" . $var_name . $variable_path;
			}else
				$php_var = "\$" . $var_name . $variable_path;

			// compile the variable for php
			if( isset( $function ) )
				$php_var = $php_left_delimiter . ( !$is_init_variable && $echo ? 'echo ' : null ) . ( $params ? "( $function( $php_var, $params ) )" : "$function( $php_var )" ) . $php_right_delimiter;
			else
				$php_var = $php_left_delimiter . ( !$is_init_variable && $echo ? 'echo ' : null ) . $php_var . $extra_var . $php_right_delimiter;

			$html = str_replace( $tag, $php_var, $html );

		}
		
		return $html;
	}
	
	
	
	/**
	 * Check if function is in black list (sandbox)
	 *
	 * @param string $code
	 * @param string $tag
	 */
	private function function_check( $code ){

		$preg = '#(\W|\s)' . implode( '(\W|\s)|(\W|\s)', self::$black_list ) . '(\W|\s)#';

		// check if the function is in the black list (or not in white list)
		if( count(self::$black_list) && preg_match( $preg, $code, $match ) ){

			// find the line of the error
			$line = 0;
			$rows=explode("\n",$this->tpl['source']);
			while( !strpos($rows[$line],$code) )
				$line++;

			// draw the error line
			$error = str_replace( array('<','>'), array( '&lt;','&gt;' ), array($code,$rows[$line]) );
			$error = str_replace( $code, "<font color=red>$code</font>", $rows[$line] );

			// debug the error and stop the execution of the script
			die( "<div>RainTPL Sandbox Error in template <b>{$this->tpl['tpl_filename']}</b> at line $line : <i>$error</i></b>" );
		}
		
	}

}




?>
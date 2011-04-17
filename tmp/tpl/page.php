<?php if(!class_exists('raintpl')){exit;}?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset=utf-8 />

<title><?php echo $title;?></title>
<!-- this link will be substituted with the right path : href="THEMES/acid/style.css" -->
<link href="tpl/style.css" type="text/css" rel="stylesheet" >
</head>
<body>
	<!-- this img  will be substituted with the right path : src="THEMES/acid/images.gif" -->			
	<div id="logo"><a href="http://www.raintpl.com"><img src="tpl/img/logo.gif"></a></div>
	
	<!-- content -->
	<div id="content">
	
		<h2>Variable example</h2>
		<div class="layout">

			<h3>Variable example</h3>
			<!-- all code between noparse tags is not compiled -->
			<tt>variable <b><?php echo $variable;?></b></tt>


			<br/><br/>
			<h3>Variable assignment</h3>
			<tt>assignment <?php $number=10;?> and print <?php echo $number;?></tt>

			<br/><br/>
			<h3>Operation with strings</h3>
	
			<tt>
				<?php echo $variable . $number;?><br/>
				<?php echo $number + 20;?>

			</tt>

			<br/><br/>
			<h3>Variable Modifiers</h3>
			<tt>
				<?php echo ( substr( $variable, 0,7 ) );?><br/>
				a modifier on string: <?php echo strtoupper( "hello world" );?>

			</tt>
			
			<br/><br/>
			<h3>Global variables</h3>
			<tt>The variable is declared as global into the PHP <?php echo $GLOBALS["global_variable"];?></tt>
			<br/><br/>
			
			<h3>Show all declared variables</h3>
			To show all declared variable use {$template_info}.<br/><br/>
			<tt>
				<?php echo "<pre>"; print_r( $this->var ); echo "</pre>"; ?>

			</tt>
			<br/><br/>

		</div>
					
		<h2>Constants</h2>
		<div class="layout">
			<h3>Constant</h3>
			<tt>Constant: <?php  echo true;?></tt>
			
			<br/><br/>
			<h3>Modier on constant as follow</h3>
			<tt>Negation of false is true: <?php echo round( PHP_VERSION );?></tt>
		</div>

		<h2>Loop example</h2>
		<div class="layout">
			<h3>Simple loop example</h3>
			<tt>
			<ul>
			<?php $counter1=-1; if( isset($week) && is_array($week) && sizeof($week) ) foreach( $week as $key1 => $value1 ){ $counter1++; ?>

				<li><?php echo $key1;?> = <?php echo $value1;?></li>
			<?php } ?>

			</ul>
			</tt>

			<br/><br/>
			
			<h3>Loop example with associative array</h3>
			<tt>
			<ul>
				<li>ID _ Name _ Color</li>
				<?php $counter1=-1; if( isset($user) && is_array($user) && sizeof($user) ) foreach( $user as $key1 => $value1 ){ $counter1++; ?>

					<li class="color<?php echo $counter1%2+1;?>"><?php echo $key1;?>) - <?php echo strtoupper( $value1["name"] );?> - <?php echo $value1["color"];?></li>
				<?php } ?>

			</ul>
			</tt>
			
			<br/><br/>
			
			<h3>Loop an empty array</h3>
			<tt>
			<ul>
				<?php $counter1=-1; if( isset($empty_array) && is_array($empty_array) && sizeof($empty_array) ) foreach( $empty_array as $key1 => $value1 ){ $counter1++; ?>

					<li class="color<?php echo $counter1%2+1;?>"><?php echo $key1;?>) - <?php echo $value1["name"];?> - <?php echo $value1["color"];?></li>
				<?php }else{ ?>

					<b>The array is empty</b>
				<?php } ?>

			</ul>
			</tt>

		</div>
		
		<h2>If Example</h2>
		<div class="layout">
		
			<h3>simple if example</h3>
			<tt>
			<?php if( $number==10 ){ ?>OK!
			<?php }else{ ?>NO!<?php } ?>

			</tt>
			
			<br/><br/>
			
			<h3>example of if, elseif, else example</h3>
			<tt>
			<?php if( substr($variable,0,1)=='A' ){ ?>First character is A
			{elseif="substr($variable,0,1)=='B')First character is B
			<?php }else{ ?>First character of variable is not A neither B
			<?php } ?>

			</tt>
			<br/><br/>
			
			<h3>use of ? : operator (number==10?'OK!':'no')</h3>
			You can also use the ? operator instead of if
			<tt><?php echo $number==10? 'OK!' : 'no';?></tt>
			
		</div>
		
		<h2>Include Example</h2>
		<div class="layout">
			<h3>Example of include file</h3>
			<tt><?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );self::$tpl_dir .= dirname("test") . ( substr("test",-1,1) != "/" ? "/" : "" );$tpl->draw( basename("test") );self::$tpl_dir = $tpl_dir_temp;?></tt>
		</div>
		
		<h2>Functions/Modifiers Example</h2>
		<div class="layout">
			<h3>Example of function: substr('Hello World!',1,8)</h3>
			<tt><?php echo substr('Hello World!',1,8); ?></tt>
			
			<h3>Example of equivalent modifier</h3>
			<tt><?php echo ( substr( "Hello World!", 1,8 ) );?></tt>

		</div>
		
		<h2>Path Replace (WYSIWYG)</h2>
		<div class="layout">
		
			<h3>WYSIWYG</h3>

			RainTPL replaces relative paths of images, css and links automatically with the correct server paths.
			<br/><br/>
			
			<h3>Path replace on relative path of image</h3>
			into the template the image is wrote as:
			<code>&lt;img src="img/logo.gif" alt="logo"/&gt;</code>
			in the compiled template the path is replaced with the correct path <b>tpl/img/logo.gif</b>:<br/>
			<img src="tpl/img/logo.gif" alt="logo"/>
			<br/><br/><br/>
			<b>Absolute paths and path ending with # are not replaced</b>
			<br/><br/>For more info read the documentation:
			<tt><a href="http://www.raintpl.com/Documentation/Documentation-for-web-designers/WYSIWYG/">http://www.raintpl.com/Documentation/Documentation-for-web-designers/WYSIWYG/</a></tt>


		</div>

	</div>
	
	<div id="footer"><?php echo $copyright;?></div>

</body>
</html>

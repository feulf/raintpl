<?php if(!class_exists('raintpl')){exit;}?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset=utf-8 />
<title><?php echo $title;?></title>
<!-- this link will be substituted with the right path : href="THEMES/acid/style.css" -->
<link href="tpl/style.css" type="text/css" rel="stylesheet" >
</head>
<body>
	<!-- this img src will be substituted with the right path : src="THEMES/acid/images.gif" -->			
	<div id="logo"><a href=@http://www.raintpl.com@><img src="tpl/img/logo.gif"></a></div>

	<!-- content -->
	<div id="content">
	
		<h1>Variable example</h1>
		<div class="layout">

			variable: <?php echo $variable;?>


			<br/><br/>

			variable assignment (number=10): <?php $number=10;?>  <br/>
			and print (number): <?php echo $number;?>


			<br/><br/>
			
			operation with strings (variable . number): <?php echo $variable . $number;?>


			<br/><br/>
			
			maths operations (number+10): <?php echo $number + 10;?>


			<br/><br/>
			
			a simple modifiers (variable|substr:0,7): <?php echo ( substr( $variable, 0,7 ) );?>

			
			<br/><br/>
			
			global variable (GLOBALS.global_variable):  <?php echo $GLOBALS["global_variable"];?>

		</div>

		<h1>Constants</h1>
		<div class="layout">
			Constant: <?php  echo true;?>

		</div>
		
		<h1>Loop example</h1>
		<div class="layout">
			Simple loop example:

			<ul>
			<?php $counter1=-1; if( isset($week) && is_array($week) && sizeof($week) ) foreach( $week as $key1 => $value1 ){ $counter1++; ?>

				<li><?php echo $key1;?> = <?php echo $value1;?></li>
			<?php } ?>

			</ul>

			<br/><br/>
			
			Loop example with associative array:
			<ul>
				<li>ID _ Name _ Color</li>
				<?php $counter1=-1; if( isset($user) && is_array($user) && sizeof($user) ) foreach( $user as $key1 => $value1 ){ $counter1++; ?>

					<li class="color<?php echo $counter1%2+1;?>"><?php echo $key1;?>) - <?php echo $value1["name"];?> - <?php echo $value1["color"];?></li>
				<?php } ?>

			</ul>
			
			<br/><br/>
			
			Loop an empty array
			<ul>
				<?php $counter1=-1; if( isset($empty_array) && is_array($empty_array) && sizeof($empty_array) ) foreach( $empty_array as $key1 => $value1 ){ $counter1++; ?>

					<li class="color<?php echo $counter1%2+1;?>"><?php echo $key1;?>) - <?php echo $value1["name"];?> - <?php echo $value1["color"];?></li>
				<?php }else{ ?>

					<b>The array is empty</b>
				<?php } ?>

			</ul>

		</div>
		
		<h1>If Example</h1>
		<div class="layout">
		
			simple if example:<br/>
			<?php if( $number==10 ){ ?>OK!
			<?php }else{ ?>NO!<?php } ?>

			
			<br/><br/>
			
			example of if, elseif, else example:<br/>
			<?php if( substr($variable,0,1)=='A' ){ ?>First character is A
			{elseif="substr($variable,0,1)=='B')First character is B
			<?php }else{ ?>First character of variable is not A neither B
			<?php } ?>

			
			<br/><br/>
			use of ? : operator (number==10?'OK!':'no')<br/>
			<?php echo $number==10? 'OK!' : 'no';?>

			
		</div>
		
		<h1>Include Example</h1>
		<div class="layout">
			Example of include file
			<?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );self::$tpl_dir .= dirname("test") . ( substr("test",-1,1) != "/" ? "/" : "" );$tpl->draw( basename("test") );self::$tpl_dir = $tpl_dir_temp;?>

		</div>
		
		<h1>Function Example</h1>
		<div class="layout">
			Example of function: substr('Hello World!',1,8)<br>
			<?php echo substr('Hello World!',1,8); ?>

		</div>

	</div>
	
	<div id="footer"><?php echo $copyright;?></div>

</body>
</html>

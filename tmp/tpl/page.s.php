<?php if(!class_exists('raintpl')){exit;}?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset=utf-8 />
<title>Rain TPL Example</title>
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

			variable: Hello World!

			<br/><br/>

			variable assignment (number=10):   <br/>
			and print (number): 10

			<br/><br/>
			
			operation with strings (variable . number): Hello World!10

			<br/><br/>
			
			maths operations (number+10): 20

			<br/><br/>
			
			a simple modifiers (variable|substr:0,7): Hello W
			
			<br/><br/>
			
			global variable (GLOBALS.global_variable):  Hello world I'm global!
		</div>

		<h1>Constants</h1>
		<div class="layout">
			Constant: 1
		</div>
		
		<h1>Loop example</h1>
		<div class="layout">
			Simple loop example:

			<ul>
			
				<li>0 = Monday</li>
			
				<li>1 = Tuersday</li>
			
				<li>2 = Wednesday</li>
			
				<li>3 = Friday</li>
			
				<li>4 = Saturday</li>
			
				<li>5 = Sunday</li>
			
			</ul>

			<br/><br/>
			
			Loop example with associative array:
			<ul>
				<li>ID _ Name _ Color</li>
				
					<li class="color1">0) - Jupiter - yellow</li>
				
					<li class="color2">1) - Mars - red</li>
				
					<li class="color1">2) - Hearth - blue</li>
				
			</ul>
			
			<br/><br/>
			
			Loop an empty array
			<ul>
				
					<b>The array is empty</b>
				
			</ul>

		</div>
		
		<h1>If Example</h1>
		<div class="layout">
		
			simple if example:<br/>
			OK!
			
			
			<br/><br/>
			
			example of if, elseif, else example:<br/>
			First character of variable is not A neither B
			
			
			<br/><br/>
			use of ? : operator (number==10?'OK!':'no')<br/>
			OK!
			
		</div>
		
		<h1>Include Example</h1>
		<div class="layout">
			Example of include file
			<div class="output">This is test.html</div>
		</div>
		
		<h1>Function Example</h1>
		<div class="layout">
			Example of function: substr('Hello World!',1,8)<br>
			ello Wor
		</div>

	</div>
	
	<div id="footer">Copyright 2006 - 2011 Rain TPL<br>Project By Rain Team</div>

</body>
</html>

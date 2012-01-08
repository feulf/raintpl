<?php if(!class_exists('raintpl')){exit;}?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset=utf-8 />
<title>Rain TPL Example</title>

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
			<tt>variable <b>Hello World!</b></tt>

			<br/><br/>
			<h3>Variable assignment</h3>
			<tt>assignment  and print 10</tt>

			<br/><br/>
			<h3>Operation with strings</h3>
	
			<tt>
				Hello World!10<br/>
				30
			</tt>

			<br/><br/>
			<h3>Variable Modifiers</h3>
			<tt>
				Hello W<br/>
				a modifier on string: HELLO WORLD
			</tt>

			<br/><br/>
			<h3>Global variables</h3>
			<tt>The variable is declared as global into the PHP This is a global variable</tt>
			<br/><br/>
			
			<h3>Show all declared variables</h3>
			To show all declared variable use {$template_info}.<br/><br/>
			<tt>
				<pre>Array
(
    [variable] => Hello World!
    [week] => Array
        (
            [0] => Monday
            [1] => Tuersday
            [2] => Wednesday
            [3] => Friday
            [4] => Saturday
            [5] => Sunday
        )

    [user] => Array
        (
            [0] => Array
                (
                    [name] => Jupiter
                    [color] => yellow
                )

            [1] => Array
                (
                    [name] => Mars
                    [color] => red
                )

            [2] => Array
                (
                    [name] => Earth
                    [color] => blue
                )

        )

    [empty_array] => Array
        (
        )

    [title] => Rain TPL Example
    [copyright] => Copyright 2006 - 2011 Rain TPL<br>Project By Rain Team
    [number] => 10
)
</pre>
			</tt>
			<br/><br/>

		</div>
					
		<h2>Constants</h2>
		<div class="layout">
			<h3>Constant</h3>
			<tt>Constant: 1</tt>
			
			<br/><br/>
			<h3>Modier on constant as follow</h3>
			<tt>Negation of false is true: 5</tt>
		</div>

		<h2>Loop example</h2>
		<div class="layout">
			<h3>Simple loop example</h3>
			<tt>
			<ul>
			
				<li>0 = Monday</li>
			
				<li>1 = Tuersday</li>
			
				<li>2 = Wednesday</li>
			
				<li>3 = Friday</li>
			
				<li>4 = Saturday</li>
			
				<li>5 = Sunday</li>
			
			</ul>
			</tt>

			<br/><br/>
			
			<h3>Loop example with associative array</h3>
			<tt>
			<ul>
				<li>ID _ Name _ Color</li>
				
					<li class="color1">0) - JUPITER - yellow</li>
				
					<li class="color2">1) - MARS - red</li>
				
					<li class="color1">2) - EARTH - blue</li>
				
			</ul>
			</tt>
			
			<br/><br/>
			
			<h3>Loop an empty array</h3>
			<tt>
			<ul>
				
					<b>The array is empty</b>
				
			</ul>
			</tt>

		</div>
		
		<h2>If Example</h2>
		<div class="layout">
		
			<h3>simple if example</h3>
			<tt>
			OK!
			
			</tt>
			
			<br/><br/>
			
			<h3>example of if, elseif, else example</h3>
			<tt>
			First character of variable is not A neither B
			
			</tt>
			<br/><br/>
			
			<h3>use of ? : operator (number==10?'OK!':'no')</h3>
			You can also use the ? operator instead of if
			<tt>OK!</tt>
			
		</div>
		
		<h2>Include Example</h2>
		<div class="layout">
			<h3>Example of include file</h3>
			<tt><div class="output">This is test.html</div></tt>
		</div>
		
		<h2>Functions</h2>
		<div class="layout">
			<h3>Example of function: ucfirst(strtolower($title))</h3>
			<tt>Rain tpl example</tt>
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
	
	<div id="footer">Copyright 2006 - 2011 Rain TPL<br>Project By Rain Team</div>

</body>
</html>

<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php __('Ping Pawn: Social Quotes'); ?>
		<?php echo $title_for_layout; ?>
	</title>
    
    <link rel="shortcut icon" href="/img/favicon.ico" /> 
    
	<?php
		echo $this->Html->css('reset');
        echo $this->Html->css('font');
        echo $this->Html->css('pingpawn');
		echo $scripts_for_layout;
	?>
</head>
<body>
    <div id="container">
        
        <div id="leftbar">
            <div id="leftbar_top">
                <h1>PING PAWN</h1>
            </div>
                        
            <div id="leftbar_bottom">
                <div id="menu">
                    <ul>
                        <li><?= $html->link('Register', '/users/Register')  ?></li>
                        <li><?= $html->link('Login', '/users/Login')  ?></li>
                        <li>&nbsp;</li>
                        <li>Random</li>
                        <li>&nbsp;</li>
                        <li>Top Quotes</li>
                        <li>Top Files</li>
                        <li>About</li>
                    </ul>
                </div>
        
                <div id="footer">
                    Site by 
                    <?php echo $this->Html->link(
                            'Ben McGraw (grue)',
                            'http://www.gruniverse.com/',
                            array('target' => '_blank', 'escape' => false)
                        );
                    ?><br /> All content &copy; their respective posters and/or actors.
                </div>
            </div>
        </div>
        
        <div id="content">
            <div id="twitter">
                <div class="call_to_action"><a href="http://twitter.com/sexymans/">follow @sexymans on twitter</a></div>
                <div class="current_quote">&lt;Hahn&gt; Hmm... assassins are cool because they have two asses.</div>
                <div class="when">Last posted like a day ago, dude</div>
            </div>
            
            <div id="flash">
                <?php echo $this->Session->flash(); ?>
            </div>
    
            <?php echo $content_for_layout; ?>
        </div>
        
        <div id="rightbar">
            <h2>ADD QUOTE?</h2>
        </div>
    </div>
    
    <script type="text/javascript">
    
      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', 'UA-5387635-9']);
      _gaq.push(['_trackPageview']);
    
      (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();
    
    </script>
    
</body>
</html>
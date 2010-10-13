<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php __('Ping Pawn: Social Quotes'); ?>
		<?php echo $title_for_layout; ?>
	</title>
    
    <? if(isset($rssurl)):  ?>
        <link rel="alternate" type="application/rss+xml" title="<?= $rssname ?>" href="<?= $rssurl ?>" />
    <? endif; ?>
    
    <link rel="shortcut icon" href="/img/favicon.ico" /> 
    
	<?php
		echo $this->Html->css('reset');
        echo $this->Html->css('font');
        echo $this->Html->css('pingpawn');
        echo $this->Html->css('modal');
		echo $scripts_for_layout;
        
        echo $this->Html->script('jquery');
        echo $this->Html->script('jquery.simplemodal');
        echo $this->Html->script('basic');
	?>
</head>
<body>
    <div id="container">
        
        <div id="leftbar">
            <a href="/">
            <div id="leftbar_top">
                <h1>PING PAWN</h1>
            </div>
            </a>
                        
            <div id="leftbar_bottom">
                <div id="menu">
                    <ul>                       
                        <? if( !empty($sessuser) ): ?>
                            <li class="identity">Hello, <?= $sessuser['User']['display_name'] ?>!</li>
                            <li><?= $html->link('Your Dashboard', '/users/home')  ?></li>
                            <li><?= $html->link('Logout?', '/users/logout')  ?></li>
                        <? else: ?>
                            <li><?= $html->link('Login via Twitter', '/twitter/login')  ?></li>
                            <li class="reassure">(we won't post to your account, honest.)</li>
                        <? endif; ?>
                        <li>&nbsp;</li>
                        <li><?= $html->link('Random', '/random')  ?></li>
                        <li>&nbsp;</li>
                        <li class="unimplemented">Top Quotes</li>
                        <li class="unimplemented">Top Files</li>
                        <li><?= $html->link('About', '/about')  ?></li>
                    </ul>
                </div>
        
                <div id="footer">
                    Site by 
                    <?php echo $html->link(
                            'Ben McGraw (grue)',
                            'http://www.gruniverse.com/',
                            array('target' => '_blank', 'escape' => false)
                        );
                    ?><br /> All content &copy; their respective posters and/or actors.
                </div>
            </div>
        </div>
        
        <style>
            .twtr-hd, .twtr-ft { display: none; }
            .#twtr-widget-1 .twtr-tweet-text  {
                
            }
            
            .twtr-widget .twtr-tweet-wrap {
                padding: 0px;
                padding-top: 4px;
            }

            #twtr-widget-1 .twtr-tweet a {
                font-size: 10px;
                color: #aaa;
                font-weight: bold;
            }
            
        </style>
        
        <div id="content">
            <div id="twitter">
                <div class="call_to_action"><a href="http://twitter.com/sexymans/">follow @sexymans on twitter</a></div>
                
                <script src="http://widgets.twimg.com/j/2/widget.js"></script>
                <script>
                new TWTR.Widget({
                  version: 2,
                  type: 'profile',
                  rpp: 1,
                  interval: 6000,
                  width: 'auto',
                  height: 100,
                  theme: {
                    shell: {
                      background: 'transparent',
                      color: '255'
                    },
                    tweets: {
                      background: '0',
                      color: '0',
                      links: '0'
                    }
                  },
                  features: {
                    scrollbar: false,
                    loop: false,
                    live: false,
                    hashtags: false,
                    timestamp: true,
                    avatars: false,
                    behavior: 'all'
                  }
                }).render().setUser('sexymans').start();
                </script>
            </div>
            
            <div id="flash">
                <?php echo $this->Session->flash(); ?>
            </div>
    
            <?php echo $content_for_layout; ?>
        </div>
        
        <? if(isset($rssurl)):  ?>
        <div id="rss_button">
            <a href="<?= $rssurl ?>"><?= $html->image('rss.png', array('alt'=>$rssname)) ?></a>
        </div>
        <? endif; ?>
        
        
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
        
<?
        $prefill = '';
        if( isset($_SESSION['quick_prf']) ) {
            $prefill = $_SESSION['quick_prf'];
        }

?>
    
    <!-- modal content -->
    <div id="basic-modal-content" style='display:none' class="modal-dialog-form">
        <h3>Add Quote</h3>
        
        <?= $this->Form->create('Quote', array('action' => 'add')); ?>
        <?= $this->Form->input('prf', array('label' => 'quotefile name', 'value' => $prefill)); ?>
        <label for="quote">quote</label><?= $this->Form->textarea('quote'); ?>
        <?= $this->Form->submit('add quote'); ?>
        
        <?= $this->Form->end(); ?>
    </div>

    <!-- preload the images -->
    <div style='display:none'>
        <img src='img/basic/x.png' alt='' />
    </div>
    
    <script>

        $('#rightbar').click(function (e) {
<? if($sessuser) : ?>    
            $('#basic-modal-content').modal();
            return false;
<? else: ?>
            window.location.href = '/twitter/login/';
<? endif ?>
        });
    </script>

</body>
</html>
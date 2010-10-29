<div class="single_quote">
<script>
    if(typeof do_formatting === 'undefined') {
        function do_formatting(str) {
            str = str.replace( /</g, '&lt;' );
            str = str.replace( /\n/g, '</p><p>' );
            return str;
        }
    }
</script>
<?
if(!function_exists('do_formatting')) {
    function do_formatting($str) {
        $str = str_replace( '<', '&lt;', $str );
        $str = preg_replace ( '/\n/' , '</p><p>' , $str );
        return $str;
    }
}

$prf = $quote['Prf'];
$quote = $quote['Quote'];
$id = $quote['id'];

$PERMALINK = $this->webroot . 'quotes/'. (($quote['url_key']) ? $quote['url_key'] : $quote['id']);

if( $quote['is_formatted'] ) {
    $str = do_formatting( $quote['quote'] );        
} else {
    $str = str_replace( '<', '<p>&lt;', $quote['quote'] );
}

$title = $quote['title'] ? $quote['title'] : 'Untitled Quote';

$title_chaser = ' (#'.$quote['id'].')';

$canedit = can_edit($sessuser, $quote);

if( isset($noedit) ) {
    $canedit = false;    
}

?>

<div id="voting">
    <?
        $vote_up_class = '';
        $vote_down_class = '';
    
    
    if(!empty($vote)) {
        $vote_up_class = 'voted';
        $vote_down_class = 'voted';
        if( $vote > 0 ) {
            $vote_up_class .= ' up';
        } else if( $vote < 0 ) {
            $vote_down_class .= ' down';
        }   
    }
        
    ?>
    
    <?= $html->link('+','/vote/up/'.$quote['id'],array('id'=>'vote_up', 'class'=>$vote_up_class)); ?>
    <span id="tally">(<?= $quote['tally'] ?>)</span>
    <?= $html->link('-','/vote/down/'.$quote['id'],array('id'=>'vote_down', 'class'=>$vote_down_class)); ?>

</div>

<h1 class="title"><a href="<?=$PERMALINK ?>"><span class="quote_title" id="quote_title"><?=$title ?></span></a><span id="title_chaser"><?= $title_chaser?></span></h1>
<h2 class="fromfile">from the <?= $html->link( $prf['name'].' quotefile','/quotefile/'.$prf['id'] )   ?></h2>

<div class="quote">
    <div class="body" id="quote_body"><?=$str ?></div>
</div>

<script>    
<? if($canedit): ?>
    
    $(".title").append("<div class='edit'></div>");
    $(".title").append("<div class='delete'></div>");
    $(".quote").append("<div class='edit'></div>");
    
    var _title_init = false;
    var _body_init = false;
    
    $(".title .edit").click(
        function() {
            if( !_title_init ) {
                $( ".title .quote_title" ).eip( "<?=$this->webroot ?>quotes/update/<?=$id?>/title", { cancel_on_esc: true, max_size: 255 } );
                _title_init = 1;
            }
            
            $(".title .quote_title").click();
        }
    )
    
    $(".title .delete").click(
        function() {
            
            var i = confirm('Are you sure you want to remove this quote from the website?');
            if( i ) {
                window.location.href = '<?=$this->webroot ?>quotes/delete/<?=$id?>';
            }
        }
    )
    
    var pre_edit_body;
    
    var body_error = function() {
        $(".quote .body").html(pre_edit_body);
    }
    
    var body_success = function(o, reshtml) {
        $(o).html(do_formatting(reshtml));
    }
    
    $(".quote .edit").click(
        function() {
            if( !_body_init ) {
                $( ".quote .body" ).eip( "<?=$this->webroot ?>quotes/update/<?=$id?>/body", { form_type: "textarea", cancel_on_esc: true, on_error: body_error, after_save: body_success } );
                _body_init = 1;
            }
            
            pre_edit_body = $(".quote .body").html();
            
            $(".quote .body").html( $("#original_quote").html() );
            
            $(".quote .body").click();
        }
    )
<? endif; ?>
</script>

<textarea id="original_quote" style="display: none;"><?= $quote['original_quote']; ?></textarea>
</div>
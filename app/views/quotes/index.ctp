<?

$prf = $res['Prf'];
$quote = $res['Quote'];
$id = $quote['id'];

$tags = array();

if( isset($res['Tag']) ) {
    foreach( $res['Tag'] as $t ) {
        $tags[] = $t['tag'];
    }
}

$comments = array();
if( isset($res['Comment']) ) {
    foreach( $res['Comment'] as $c ) {
        $comments[] = $c;
    }
}

$users = array();

if(!empty($res['Commentors'])) {
    $users = $res['Commentors'];
}

$str = str_replace( '<', '<p>&lt;', $quote['quote'] );

$title = $quote['title'] ? $quote['title'] : 'Untitled Quote';

$title_chaser = ' (#'.$quote['id'].')';

$canedit = can_edit($sessuser, $res);

?>

<div id="voting">
    <? if(empty($vote)): ?>
        <?= $html->link('+','/vote/up/'.$quote['id'],array('id'=>'vote_up')); ?>
        <span id="tally">(<?= $quote['tally'] ?>)</span>
        <?= $html->link('-','/vote/down/'.$quote['id'],array('id'=>'vote_down')); ?>
    <? else: ?>
        <span class="voted <?= ($vote>0)?'up':'' ?>">+</span>
        <span id="tally">(<?= $quote['tally'] ?>)</span>
        <span class="voted <?= ($vote<0)?'down':'' ?>">-</span>
    <? endif; ?>
</div>


<h1 class="title"><span class="quote_title" id="quote_title"><?=$title ?></span><?= $title_chaser?></h1>
<h2>from the <?= $html->link( $prf['name'].' quotefile','/quotefile/'.$prf['id'] )   ?></h2>

<div class="quote">
    <div class="body" id="quote_body"><?=$str ?></div>
</div>

<h3>tags</h3>
<div class="tags">
    <? if($tags): ?>
        <?=implode(' ', $tags) ?>
    <? else: ?>
        <span class="no-tags">There are no tags yet.</span>
    <? endif; ?>
    <span id="add-tag-link">add tag?</span>
</div>

<h3>Comments</h3>
<div class="comments">
    
    <? if($comments): ?>
<style>
    .comments {
        position: relative;
    }

    .c-single {
        background:none repeat scroll 0 0 #EFEFEF;
        border-top:1px solid #DDDDDD;
        position: relative;
        width: 480px ;
        
        padding:10px;
    }

    .c-gravitar {
        width: 58px;
        height: 58px;
        display: inline-block;
    }
    
    .c-body {
        width: 350px;
        display: inline-block;
        vertical-align: top;
    }
    
    .c-head a {
        color: #aaa;
        text-decoration: none;
    }
    
    .c-date {
        font-size: 10px;
        color: #bbb;
    }
    
    .c-date span {
        
    }
    
    .c-head {
        padding-bottom: 4px;
    }
    
</style>
    <?    
        foreach( $comments as $c ): 
    ?>
        
        <div class="c-single clear" id="comment-<?= $c['id'] ?>" > 
            <div class="c-gravitar">
                <img src="http://api.twitter.com/1/users/profile_image/<?= $users[$c['user_id']]['twitter_name'] ?>.json ?>" />
            </div>
            <div class="c-body"> 
                <div class="c-date">
                    <span><?= date('Y', time($c['created'])) ?></span> <?= date('F j', time($c['created'])) ?>
                </div> 
                    
                <div class="c-head">
                    <? if($c['website']) {
                        $c['website'] = str_replace( 'http://', '', $c['website'] );
                    ?>
                        <a href='http://<?= $c['website'] ?>' rel='external nofollow' class='url'><?= $c['name'] ?></a> <?  
                    } else {
                        echo $c['name'];
                    }?>
                </div> 
    
                <p><?= nl2br(htmlentities($c['body'], ENT_QUOTES) ) ?></p>
            </div> 
        </div>
        
    <?  endforeach;
       else: ?>
        <span class="no-tags">There are no comments yet.</span>
    <? endif; ?>
    <span id="add-comment-link">add comment?</span>
</div>

<div id="add-tag-dialog" style='display:none' class="modal-dialog-form">
    <h3>Add Tag</h3>
    
    <?= $this->Form->create('Tag', array('action' => 'add')); ?>
    <?= $this->Form->input('tag', array('label' => 'tag')); ?>
    <?= $this->Form->hidden('post_id', array('value' => $id)); ?>
    <?= $this->Form->submit('add tag'); ?>
    <?= $this->Form->end(); ?>
</div>

<div id="add-comment-dialog" style='display:none' class="modal-dialog-form">
    <h3>Add Comment</h3>
    <?
        $pf_name = '';
        if(!empty($sessuser['User']['display_name'])) {
            $pf_name = $sessuser['User']['display_name'];
        }
    ?>
        
    <?= $this->Form->create('Comment', array('action' => 'add')); ?>
    <label for="name">logged in as </label> <span class='identity'><?=$pf_name ?></span> <br /><br />
    
    <label for="body">comment</label><?= $this->Form->textarea('body', array('validation' => 'required')); ?>
    
    <?= $this->Form->hidden('quote_id', array('value' => $id)); ?>
    
    <?= $this->Form->submit('add comment'); ?>
    <?= $this->Form->end(); ?>
</div>

<script>
    $('#add-tag-link').click(
        function() {
<? if($sessuser) : ?>    
            $('#add-tag-dialog').modal();
            return false;
<? else: ?>
            window.location.href = '/twitter/login/';
<? endif ?>
        }
    );
    
    $('#add-comment-link').click(
        function() {
<? if($sessuser) : ?>    
            $('#add-comment-dialog').modal();
            return false;
<? else: ?>
            window.location.href = '/twitter/login/';
<? endif ?>
        }
    );
    
<? if($canedit): ?>
    
    $(".title").append("<div class='edit'></div>");
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
    $(".quote .edit").click(
        function() {
            if( !_body_init ) {
                $( ".quote .body" ).eip( "<?=$this->webroot ?>quotes/update/<?=$id?>/body", { form_type: "textarea", cancel_on_esc: true } );
                _body_init = 1;
            }
            
            $(".quote .body").click();
        }
    )
    
    //$( "#content" ).eip( "save.php" );

    
<? endif; ?>
    
</script>
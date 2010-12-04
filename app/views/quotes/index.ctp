<style>

#content h1.title {
    padding-top: 20px;
}

#voting {
    position: relative;
    left: 0px;
    
    font-size: 36px;
    padding-top: 34px;
    padding-left: 30px;
}

#voting #vote_down {
    font-size: 48px;
}

#tally {
    font-size: 36px;
}

</style>
<?

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

$canedit = can_edit($sessuser, $res);

$id = $res['Quote']['id'];

?>

<?= $this->element('quote/single', array('quote'=>$res)) ?>

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
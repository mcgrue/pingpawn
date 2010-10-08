<?

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


$str = str_replace( '<', '<p>&lt;', $quote['quote'] );

$title = $quote['title'] ? $quote['title'] : 'Untitled Quote (#'.$quote['id'].')';

?>

<h1><?=$title ?></h1>
<h2>from the <?=$quote['prf_name'] ?> quotefile</h2>

<div class="quote">
    <?=$str ?>
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
        <?=implode(' ', $comments) ?>
    <? else: ?>
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
    
    <?= $this->Form->create('Comment', array('action' => 'add')); ?>
    <?= $this->Form->input('name', array('label' => 'name', 'validation' => 'required')); ?>
    <?= $this->Form->input('email', array('label' => 'email', 'validation' => 'required email')); ?>
    <?= $this->Form->input('website', array('label' => 'website')); ?>
    <label for="body">comment</label><?= $this->Form->textarea('body', array('validation' => 'required')); ?>
    
    <?= $this->Form->hidden('post_id', array('value' => $id)); ?>
    
    <?= $this->Form->submit('add comment'); ?>
    <?= $this->Form->end(); ?>
</div>

<script>
    $('#add-tag-link').click(
        function() {
            $('#add-tag-dialog').modal();
            return false;    
        }
    );
    
    $('#add-comment-link').click(
        function() {
            $('#add-comment-dialog').modal();
            return false;    
        }
    );
    
</script>
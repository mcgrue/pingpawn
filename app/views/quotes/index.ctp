<?

$quote = $res['Quote'];
$id = $quote['id'];

$tags = array();
if( isset($res['Tag']) ) {
    foreach( $res['Tag'] as $t ) {
        $tags[] = $t['tag'];
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
    <?=implode(' ', $tags) ?> <span id="add-tag-link">add tag?</span>
</div>

<div id="add-tag-dialog" style='display:none' class="modal-dialog-form">
    <h3>Add Tag</h3>
    
    <?= $this->Form->create('Tag', array('action' => 'add')); ?>
    <?= $this->Form->input('tag', array('label' => 'tag')); ?>
    <?= $this->Form->hidden('post_id', array('value' => $id)); ?>
    <?= $this->Form->submit('add tag'); ?>
    <?= $this->Form->end(); ?>
</div>

<script>
    $('#add-tag-link').click(
        function() {
            $('#add-tag-dialog').modal();
            return false;    
        }
    )
</script>





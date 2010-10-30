<style>
a {
    color: #666;
    font-weight: bold;
    text-decoration: none;
}
</style>
<h1>Welcome home, <?= $sessuser['User']['display_name'] ?></h1>
<hr style="margin-bottom: 32px; border: 1px solid #ccc;">
    
<? if(!empty($sessuser['Prf'])): ?>
<p>You currently have <?= $html->link(count($sessuser['Prf']).' quotefiles', '/users/list') ?>.</p>
<? else: ?>
<p>You currently don't have any quotefiles.  To make one, add a quote using the button on the bottom-right.  The name you choose for your quotefile will become your quotefile.</p>
<? endif; ?>
<?
    $us = 's';
    $ds = 's';
    $ms = 's';
    
    if( $upvotes === 1 ) $us = '';
    if( $downvotes === 1 ) $ds = '';
    if( $modcount === 1 ) $ms = '';
    
?>
<p>You have up-voted <?= $upvotes ?> time<?=$us?>.  You have down-voted <?= $downvotes ?> time<?=$ds?>.</p>

<!-- <p>You have <?= $modcount ?> item<?= $ms ?> in your <?= $html->link('moderation queue', '/users/moderation_queue/'); ?>.</p> -->

<hr style="margin-bottom: 32px; border: 1px solid #ccc;">

<h2>Actions</h2>
<ul>
    <li><?= $html->link('mass upload', '/users/mass_upload/'); ?></li>
    <!-- <li><?= $html->link('moderation queue', '/users/moderation_queue/'); ?></li> -->
    <li><?= $html->link('logout', '/users/logout/'); ?></li>
</ul>
<style>

h1 .title a, #quote_title {
    font-size: 24px;
}

#voting {
    font-size: 20px;
}

#tally {
    font-size: 20px;
}

#title_chaser {
    font-size: 20px;
}

h2.fromfile, h2.fromfile a {
    color: #999;
}

.paginator {
    padding-bottom: 20px;
}

.paginator a {
    color: #999;
    text-decoration: none;
    font-weight: bold;
}

.paginator .disabled {
    color: #ccc;
}

.paginator .page {
    color: #aaa;
}

</style>

<h1 class="recent">Top Quotes</h1>
<div class="paginator">
    <?= $this->Paginator->prev('prev', null, null, array('class' => 'disabled')); ?>
    <?= $this->Paginator->numbers( array('separator'=>' ') ); ?>
    <?= $this->Paginator->next('next', null, null, array('class' => 'disabled')); ?>

    - <span class="page">

    <?=
        $this->Paginator->counter(array(
            'format' => 'Page %page% of %pages%'
        ))
    ?>
    </span>    
</div>
<?
    foreach( $data as $q ):
        $qid = $q['Quote']['id'];
        $v = false;
        
        if(isset($vote[$qid]['votes']['vote'])) {
            $v = $vote[$qid]['votes']['vote'];
        }
    
?>
<div class="top_quote">
<?= $this->element('quote/single', array('quote'=>$q, 'vote' => $v, 'noedit' => true)) ?>
</div>

<?  endforeach; ?>
<div class="paginator">
    <?= $this->Paginator->prev('prev', null, null, array('class' => 'disabled')); ?>
    <?= $this->Paginator->numbers( array('separator'=>' ') ); ?>
    <?= $this->Paginator->next('next', null, null, array('class' => 'disabled')); ?>

    - <span class="page">

    <?=
        $this->Paginator->counter(array(
            'format' => 'Page %page% of %pages%'
        ))
    ?>
    </span>    
</div>

<style>

h1.recent {
    margin-bottom: 40px;
    position: relative;
    top: 10px;
}

.title a {
    color: #666;
    text-decoration: none;
}

.top_quote {
    margin-bottom: 50px;   
}

.top_quote .title {
    font-weight: bold;
    font-size: 14px;
    color: #888;
}

.top_quote .byline {
    font-weight: normal;
    font-size: 10px;
}

.top_quote .body {
    color: #AAA;
}

.top_quote .votes a {
    color: #888;
    text-decoration: none;
    font-size: 12px;
}

.top_quote .votes #tally {
    font-size: 16px;
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
<?
    $name = $prf['Prf']['name'];
        
    if(!empty($prf['User']['url'])) {
        $ownername = "<a href='{$prf['User']['url']}'>{$prf['User']['display_name']}</a>";
    } else {
        $ownername = $prf['User']['display_name'];
    }
?>

<h1>The <?=$name ?> Quotefile</h1>
<h2>maintained by <?= $ownername ?></h2>

<ul class=stats>
    <li>Quotes in file: <?= $stats['quote_count'] ?></li>
    <li>Votes on Quotes in this file: <?= $stats['vote_count'] ?></li>
    <li>High Score: <?= $stats['max'] ?></li>
    <li>Low Score: <?= $stats['min'] ?></li>
    <li>Total Score: <?= $stats['sum'] ?></li>
</ul>

<h1 class="recent">Top Quotes in this Quotefile</h1>
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
<? foreach( $data as $q ):

    $qid = $q['Quote']['id'];
    $v = false;
    
    if(isset($vote[$qid]['votes']['vote'])) {
        $v = $vote[$qid]['votes']['vote'];
    }
    
?>

<?= $this->element('quote/single', array('quote'=>$q, 'vote' => $v, 'noedit' => true)) ?>

<? endforeach; ?>
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
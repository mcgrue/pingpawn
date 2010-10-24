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
<? foreach( $data as $q ):
    $prf = $q['Prf'];
    $q = $q['Quote'];
    
    $str = str_replace( '<', '<p>&lt;', $q['quote'] );
    $title = $q['title'] ? $q['title'] : 'Untitled Quote (#'.$q['id'].')';
?>
<div class="top_quote">
    <div class="votes">
        <?= $html->link('+','/vote/up/'.$q['id'],array('id'=>'vote_up')); ?>
        <span id="tally">(<?= $q['tally'] ?>)</span>
        <?= $html->link('-','/vote/down/'.$q['id'],array('id'=>'vote_down')); ?>
    </div>
    <div class="title"><?= $this->Html->link($title, '/quotes/'.$q['id'])  ?> <span class="byline">in the <?= $html->link( $prf['name'].' quotefile','/quotefile/'.$prf['id'] ) ?></span></div>
    <div class="body"><?= $str ?></div>
</div>
<? endforeach; ?>
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

.recent_quote {
    margin-bottom: 50px;   
}

.recent_quote .title {
    font-weight: bold;
    font-size: 14px;
    color: #888;
}

.recent_quote .byline {
    font-weight: normal;
    font-size: 10px;
}

.recent_quote .body {
    color: #AAA;
}
</style>

<h1 class="recent">Most recent quotes</h1>
<? foreach( $res as $q ):
    $prf = $q['Prf'];
    $q = $q['Quote'];
    
    $str = str_replace( '<', '<p>&lt;', $q['quote'] );
    $title = $q['title'] ? $q['title'] : 'Untitled Quote (#'.$q['id'].')';
?>
<div class="recent_quote">
    <div class="title"><?= $this->Html->link($title, '/quotes/'.$q['id'])  ?> <span class="byline">in the <?= $html->link( $prf['name'].' quotefile','/quotefile/'.$prf['id'] ) ?></span></div>
    <div class="body"><?= $str ?></div>
</div>
<? endforeach; ?>
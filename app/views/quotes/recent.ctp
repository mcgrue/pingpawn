<link rel="alternate" type="application/rss+xml" title="Ping Pawn RSS Feed" href="/quotes/index.rss" />

<style>

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

<h1 style="font-size: 20px; margin-bottom: 20px;">Most recent quotes</h1>
<? foreach( $res as $q ):
    $q = $q['Quote'];
    $str = str_replace( '<', '<p>&lt;', $q['quote'] );
    $title = $q['title'] ? $q['title'] : 'Untitled Quote (#'.$q['id'].')';
?>
<div class="recent_quote">
    <div class="title"><?= $this->Html->link($title, '/quotes/'.$q['id'])  ?> <span class="byline">in the <?= $q['prf_name'] ?> quotefile</span></div>
    <div class="body"><?= $str ?></div>
</div>
<? endforeach; ?>
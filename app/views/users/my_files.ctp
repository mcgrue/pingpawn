<style>
    .topquotes {
        padding: 2px;
        list-style: decimal;
    }
    
    li.name {
        padding: 2px;
    }
    
    li.name a {
        font-weight: bold;
        text-decoration: none;
        color: #888;
    }
    
    li.name span.score {
        color: #aaa;
    }
    
</style>


<h1>Your Quotefiles</h1>

<ol class="topquotes">
<? foreach($prfs as $l):
?>
    <li class="name"><?= $html->link( $l['name'], '/quotefile/'.$l['url_key'] ) ?>
    <span class="score"><?= $l['quotecount'] ?> entries, <?= $l['tallysum'] ?> score.</span></li>
<? endforeach; ?>
</ol>
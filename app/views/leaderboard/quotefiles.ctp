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


<h1>Top Quotefiles</h1>

<ol class="topquotes">
<? foreach($lb as $l):
        $pid = $l['prf_id'];
        if(empty($prfs[$pid])) continue;
?>
    <li class="name"><?= $html->link( $prfs[$pid]['name'], '/quotefile/'.$prfs[$pid]['key'] ) ?>
    <span class="score">Freshness of <?= $l['score'] ?> with <?= $l['quotecount'] ?> entries.</span></li>
<? endforeach; ?>
</ol>
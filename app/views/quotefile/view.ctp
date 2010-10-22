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
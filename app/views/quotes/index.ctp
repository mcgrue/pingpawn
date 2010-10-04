<?

$str = str_replace( '<', '<p>&lt;', $quote['quote'] );

$title = $quote['title'] ? $quote['title'] : 'Untitled Quote (#'.$quote['id'].')';

?>

<h1><?=$title ?></h1>
<h2>from the <?=$quote['prf_name'] ?> quotefile</h2>

<div class="quote">
    <?=$str ?>
</div>


<?
    $i = 0;
?>
<style>



#voting_leaderboard {
    margin-top: 10px;
    position: relative;
}

#voting_leaderboard li span {
    padding: 2px;
}

#voting_leaderboard li {
    height: 18px;
    width: 450px;
}

li.heading {
    background-color: #cb9;
}

li.odd {
    background-color: #ddd;
    opacity: .9;
}


#voting_leaderboard li span.tally {
    
    position: absolute;
    left: 150px;
}

#voting_leaderboard li span.sum {
    position: absolute;
    left: 250px;
}

#voting_leaderboard li span.voter {
    position: absolute;
    left: 00px;
}

</style>

<h1>Leaderboards!</h1>

<h2>Voting</h2>
<ol id="voting_leaderboard" >
    <li class="heading"><span class="tally">(# of votes)</span><span class="voter">Voter</span><span class="sum">Sum of votes</span></li>
<? foreach( $voters as $v ): ?>
    <li <?= (++$i%2)?"class='odd'":'' ?> ><span class="tally"><?= $v[0]['votecount'] ?></span><span class="voter"><?= $v['u']['uname'] ?></span><span class="sum"><?= $v[0]['tally'] ?></span></li>
<? endforeach; ?>
</ol>
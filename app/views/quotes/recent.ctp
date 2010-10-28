<style>

h1 .title a, #quote_title {
    font-size: 24px;
}

#title_chaser {
    font-size: 20px;
}

h2.fromfile, h2.fromfile a {
    color: #999;
}

</style>


<h1 class="recent">Most recent quotes</h1>
<? foreach( $res as $q ):

    $qid = $q['Quote']['id'];
    $v = false;
    if(isset($vote[$qid]['votes']['vote'])) {
        $v = $vote[$qid]['votes']['vote'];
    }

    echo $this->element('quote/single', array('quote'=>$q, 'vote' => $v, 'noedit' => true));

 endforeach;
<?php
do {
    
    $rankings = Collection::getRankings($dbc, $params);

    $response = $rankings;
    
    return $response;

} while (0);

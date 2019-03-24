<?php

function create_json($id, $p_flat, $p_hole, $p_obstacle, $speed, $force, $gravity, $obst_height, $block_length, $seed, $score, $difficulty, $difficulty_standard_deviation, $survivors, $max_players)
{
    $obj = [
        "p_flat" => $p_flat,
        "p_hole" => $p_hole,
        "p_obstacle" => $p_obstacle,
        "speed" => $speed,
        "force" => $force,
        "gravity" => $gravity,
        "obst_height" => $obst_height,
        "block_length" => $block_length,
        "seed" => $seed,
        "score" => $score,
        "difficulty" => $difficulty,
        "difficulty_standard_deviation" => $difficulty_standard_deviation,
        "survivors" => $survivors,
        "max_players" => $max_players,
        "id" => $id
    ];
    return json_encode($obj);
}

function print_stars($num)
{
    $num = round($num);
    for ($i=0; $i < 5; $i++): ?>
        <div class="rating-star" style="fill:<?= $i < $num ? 'gold' : 'grey' ?>">
            <svg viewBox="0 0 15 23" height="25" width="23" class="star rating" data-rating="2">
                <polygon points="9.9, 1.1, 3.3, 21.78, 19.8, 8.58, 0, 8.58, 16.5, 21.78" style="fill-rule:nonzero;"/>
            </svg>
        </div>
    <?php endfor;
}

$dbHost = 'localhost';
$dbUser = '';
$dbPass = '';
$db = 'webgl';

$conn = new mysqli($dbHost,
                   $dbUser,
                   $dbPass,
                   $db);
$conn->query("SET time_zone = '+0:00'");

 ?>

<?php
    //webgl
    //ipimsdomanuel69


    header('Access-Control-Allow-Origin: http://twysvn.com', false);
    // header('Access-Control-Allow-Origin: http://localhost:54603', false);
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Allow-Headers: X-Requested-With');

    if(!isset($_POST['p_flat'])) {
        die();
    }
    require_once 'database.php';

    print_r($_POST);

    $p_flat = $_POST['p_flat'];
    $p_hole = $_POST['p_hole'];
    $p_obstacle = $_POST['p_obstacle'];
    $speed = $_POST['speed'];
    $force = $_POST['force'];
    $gravity = $_POST['gravity'];
    $obst_height = $_POST['obst_height'];
    $block_length = $_POST['block_length'];
    $seed = $_POST['seed'];
    $score = $_POST['score'];
    $difficulty = $_POST['difficulty'];
    $difficulty_standard_deviation = $_POST['difficulty_standard_deviation'];
    $survivors = $_POST['survivors'];
    $max_players = $_POST['max_players'];
    $image = $_POST['image'];
    $diff_scaled = $_POST['difficulty'] + $_POST['difficulty'] * 30 / ( $_POST['speed'] * 30 / $_POST['block_length'] ) * 4 /30;

    $type = $_POST['type'];

    $every_second_flat = 1;
    if (isset($_POST['every_second_flat'])) {
        $every_second_flat = $_POST['every_second_flat'];
    }

    // $stmt = $conn->prepare("INSERT INTO game (p_flat, p_hole, p_obstacle, speed, `force`, gravity, obst_height, block_length, seed, score, difficulty, difficulty_standard_deviation, survivors, max_players, image, every_second_flat, difficulty_scaled, type)
    //                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? , ?)");
    // $stmt->bind_param("ddddddiiidddiisidi", $p_flat, $p_hole, $p_obstacle, $speed, $force, $gravity, $obst_height, $block_length, $seed, $score, $difficulty, $difficulty_standard_deviation, $survivors, $max_players, $image, $every_second_flat, $diff_scaled, $type);
    // echo $stmt->execute();
    // $stmt->close();

 ?>

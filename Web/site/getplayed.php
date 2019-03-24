<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
// $http_origin = $_SERVER['HTTP_ORIGIN'];
// header("Access-Control-Allow-Origin: $http_origin", false);

header('Access-Control-Allow-Origin: http://twysvn.com', false);
header('Access-Control-Allow-Methods: POST, GET');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");

session_start();

require_once 'database.php';

function order_by($value, $offset)
{
    if (isset($_SESSION['last']) && $_SESSION['last'] == $value) {
        if($offset <= 0) $_SESSION['sort'] = $_SESSION['sort'] == "DESC" ? "ASC" : "DESC";
    }else{
        $_SESSION['sort'] = "DESC";
    }
    $_SESSION['last'] = $value;
    switch ($value) {
        case 0:
            return '`difficulty`';
        case 1:
            return '`difficulty_standard_deviation`';
        case 2:
            return '`score`';
        case 3:
            return '`survivors`';
        case 4:
            return '`timestamp`';
        case 5:
            return '`interesting`';
        case 6:
            return 'RAND()';
        case 7:
            return 'block_length';
        case 8:
            return 'obst_height';
        case 9:
            return 'gravity';
        case 10:
            return '`force`';
        case 11:
            return 'speed';
        case 12:
            return 'difficulty_metric';

        default:
            $_SESSION['last'] = 4;
            $_SESSION['sort'] = "DESC";
            return '`timestamp`';

    }
}

if (isset($_POST['level']) || isset($_GET['level']) && (isset($_POST['game']) || isset($_GET['game']))) {

    $stmt = $conn->prepare("SELECT id, p_flat, p_hole, p_obstacle, speed, `force`, gravity, obst_height, block_length, seed, score, difficulty, difficulty_standard_deviation, survivors, max_players, `timestamp`, image, interesting FROM game AS g
                            WHERE interesting = ? AND id = ?");

    if (!($stmt)) {
        echo("Error");
        echo("    ".$conn->error);
        die();
    }

    $interesting = 1;
    isset($_GET['level']) && $level = $_POST["level"];
    isset($_POST['level']) && $level = $_POST["level"];
    $stmt->bind_param("ii", $interesting, $level);
    $stmt->bind_result($id, $p_flat, $p_hole, $p_obstacle, $speed, $force, $gravity, $obst_height, $block_length, $seed, $score, $difficulty, $difficulty_standard_deviation, $survivors, $max_players, $timestamp, $image, $interesting);
    $stmt->execute();
    $stmt->fetch();

    $_SESSION['lastlevel'] = $id;
    echo create_json($id, $p_flat, $p_hole, $p_obstacle, $speed, $force, $gravity, $obst_height, $block_length, $seed, $score, $difficulty, $difficulty_standard_deviation, $survivors, $max_players);

    die();
}

if (isset($_POST['game']) || isset($_GET['game'])) {


    $extra = "";
    if (isset($_SESSION['lastlevel'])) {
        $extra = "AND g.id <> ".$_SESSION['lastlevel'];
    }

    $stmt = null;
    if (rand(0, 4) == 1) {
        $stmt = $conn->prepare("SELECT g.id, p_flat, p_hole, p_obstacle, speed, `force`, gravity, obst_height, block_length, seed, g.score, g.difficulty, g.difficulty_standard_deviation, survivors, max_players, g.timestamp, image, interesting FROM game AS g
                                LEFT JOIN rating AS r ON r.game = g.id
                                WHERE interesting = ? ".$extra."
								GROUP BY g.id
                                ORDER BY count(r.id) ASC
                                LIMIT ?
                                OFFSET ".rand(0, 6));
    }else{
        $stmt = $conn->prepare("SELECT id, p_flat, p_hole, p_obstacle, speed, `force`, gravity, obst_height, block_length, seed, score, difficulty, difficulty_standard_deviation, survivors, max_players, `timestamp`, image, interesting FROM game AS g
                                WHERE interesting = ? ".$extra."
                                ORDER BY RAND()
                                LIMIT ?");
    }

    if (!($stmt)) {
        echo("Error");
        echo("    ".$conn->error);
        die();
    }

    $interesting = 1;
    $limit = 1;
    $stmt->bind_param("ii", $interesting, $limit);
    $stmt->bind_result($id, $p_flat, $p_hole, $p_obstacle, $speed, $force, $gravity, $obst_height, $block_length, $seed, $score, $difficulty, $difficulty_standard_deviation, $survivors, $max_players, $timestamp, $image, $interesting);
    $stmt->execute();
    $stmt->fetch();

    $_SESSION['lastlevel'] = $id;
    echo create_json($id, $p_flat, $p_hole, $p_obstacle, $speed, $force, $gravity, $obst_height, $block_length, $seed, $score, $difficulty, $difficulty_standard_deviation, $survivors, $max_players);
    die();
}

$offset = 0;
$order = -1;
if (isset($_POST['order'])) {
    $order = $_POST['order'];
    $_SESSION['order'] = $order;
}
if (isset($_POST['onlynew'])) {
    if (isset($_POST['last'])) {
        if (isset($_SESSION['sort']) && $_SESSION['sort'] == "ASC" ||
            !(isset($_SESSION['order']) && $_SESSION['order'] == 4) || !isset($_SESSION['sort']))
        {
            ?>
            <tr>
                <td colspan="18" style="text-align:center;">live update only for timestamp DESC</td>
            </tr>
            <?php
            exit(0);
        }
        $last = $_POST['last'];

        $stmt = $conn->prepare("SELECT id, p_flat, p_hole, p_obstacle, speed, `force`, gravity, obst_height, block_length, seed, score, difficulty, difficulty_standard_deviation, survivors, max_players, `timestamp`, image, interesting, difficulty_metric FROM game
                                WHERE type = 1
                                WHERE `timestamp` > ? ORDER BY `timestamp` DESC");
        $stmt->bind_param("s", $last);
    }else{
        die();
    }
}else{

    $offset = 0;
    if (isset($_POST['sb-id'])) {
        $offset = $_POST['sb-id'];
    }
    $limit = 10;
    if (isset($_POST['items'])) {
        $limit = $_POST['items'];
    }

    $stmt = $conn->prepare("SELECT id, p_flat, p_hole, p_obstacle, speed, `force`, gravity, obst_height, block_length, seed, score, difficulty, difficulty_standard_deviation, survivors, max_players, `timestamp`, image, interesting, difficulty_metric FROM game
                            WHERE type = 1
                            ORDER BY ".order_by($order, $offset)." ".$_SESSION['sort']."
                            LIMIT ? OFFSET ?");



    $stmt->bind_param("ii", $limit, $offset);
}

$stmt->bind_result($id, $p_flat, $p_hole, $p_obstacle, $speed, $force, $gravity, $obst_height, $block_length, $seed, $score, $difficulty, $difficulty_standard_deviation, $survivors, $max_players, $timestamp, $image, $interesting, $difficulty_metric);
$stmt->execute();
while ($stmt->fetch()) {
    ?>
        <tr sb-id="<?= ++$offset ?>">
            <td><?= $p_flat + 0 ?></td>
            <td><?= $p_hole + 0 ?></td>
            <td><?= $p_obstacle + 0 ?></td>
            <td><?= $speed + 0 ?></td>
            <td><?= $force + 0 ?></td>
            <td><?= $gravity + 0 ?></td>
            <td><?= $obst_height ?></td>
            <td><?= $block_length ?></td>
            <td><?= $seed ?></td>
            <td><?= $score + 0 ?></td>
            <td><?= $difficulty + 0 ?></td>
            <td><?= $difficulty_standard_deviation + 0 ?></td>
            <td><?= $difficulty_metric + 0 ?></td>
            <td><?= $survivors + 0 ?></td>
            <td><?= $max_players ?></td>
            <td><?= $timestamp ?></td>
            <td><img src="<?= $image ?>"></img></td>
            <td><a class="<?= $interesting ? 'interesting' : '' ?>" href="interesting.php" sb-button="int<?= $timestamp ?>" sb-bind="int<?= $timestamp ?>" gid = "<?= $id ?>"><?= $interesting ? 'true' : 'false' ?></a></td>
            <td>
                <a href="javascript:void(0)" onclick="this.innerHTML = copytoclipboard('<?= htmlspecialchars(create_json($id, $p_flat, $p_hole, $p_obstacle, $speed, $force, $gravity, $obst_height, $block_length, $seed, $score, $difficulty, $difficulty_standard_deviation, $survivors, $max_players)) ?>')">
                    copy
                </a>
            </td>

        </tr>
    <?php
}

$stmt->close();
 ?>

<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database.php';

$isuserpage = false;
if (!isset($_POST['game'])) {
    if( !isset($_POST['user'])) {
        die("no game, no user!");
    }
    $isuserpage = true;
    $id = $_POST['user'];
}else{
    $game = $_POST['game'];

    $stmt = $conn->prepare("SELECT g.id, g.p_flat, g.p_hole, g.p_obstacle, g.speed, g.force, g.gravity, g.obst_height, g.block_length, g.seed, g.score, g.difficulty, g.difficulty_standard_deviation, g.survivors,
        g.max_players, g.timestamp, g.image, g.interesting, COUNT(r.id),
        AVG(r.fun), STD(r.fun), AVG(r.difficulty), STD(r.difficulty),
        AVG(r.score), STD(r.score) FROM game AS g
        INNER JOIN rating AS r ON r.game = g.id
        WHERE g.id = ?
        GROUP BY g.id");


        $stmt->bind_param("i", $game);
        $stmt->bind_result($game_id, $game_p_flat, $game_p_hole, $game_p_obstacle, $game_speed, $game_force, $game_gravity, $game_obst_height, $game_block_length, $game_seed, $game_score, $game_difficulty, $game_difficulty_standard_deviation, $game_survivors, $game_max_players, $game_timestamp, $game_image, $game_interesting, $numberratings,
        $avgfun, $stdfun, $avgdiff, $stddiff, $avgscore, $stdscore);
        $stmt->execute();


        if (!$stmt->fetch()) {
            $stmt->close();
            die("err");
        }
}

?>
<a href="javascript:void(0)" class="closeoverlay" onclick="closeoverlay()">❌</a>

<?php if ($isuserpage): ?>

    <?php
    $stmt = $conn->prepare("SELECT COUNT(id) FROM rating WHERE user = ?");
    $stmt->bind_param("i", $id);
    $stmt->bind_result($len);
    $stmt->execute();
    $stmt->fetch();
    $stmt->close();
    ?>
    <a href="javascript:void(0)" class="closeoverlay" onclick="closeoverlay()">❌</a>
    <h1>User: <?php
        if($id == 853) echo "Michael";
        else if($id == 1014) echo "Juri";
        else echo $id;
    ?></h1>

    <table>
        <thead>
            <tr>
                <th><div>player</div></th>
                <th><div>played games</div></th>
                <th><div>μ(fun)</div></th>
                <th><div>σ(fun)</div></th>
                <th><div>μ(difficulty)</div></th>
                <th><div>σ(difficulty)</div></th>
                <th><div>μ(score)</div></th>
                <th><div>σ(score)</div></th>
            </tr>
        </thead>

        <tbody sb-ajax="getplayers.php" items = "1" user="<?= $id ?>"></tbody>

    </table>

    <h1><?= $len ?> game<?= $len > 1 ? 's' : '' ?></h1>

<?php else: ?>

    <h1>Game</h1>

    <table>
        <thead>
            <tr>
                <th><div>p_flat</div></th>
                <th><div>p_hole</div></th>
                <th><div>p_obstacle</div></th>
                <th><div>speed</div></th>
                <th><div>force</div></th>
                <th><div>gravity</div></th>
                <th><div>obst_height</div></th>
                <th><div>block_length</div></th>
                <th><div>seed</div></th>
                <th><div>score</a></div></th>
                <th><div>difficulty</div></th>
                <th><div>sigma</div></th>
                <th><div>survivors</div></th>
                <th><div>max_players</div></th>
                <th><div>timestamp</div></th>
                <th><div>graph</div></th>
                <th><div>export</div></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= $game_p_flat + 0 ?></td>
                <td><?= $game_p_hole + 0 ?></td>
                <td><?= $game_p_obstacle + 0 ?></td>
                <td><?= $game_speed + 0 ?></td>
                <td><?= $game_force + 0 ?></td>
                <td><?= $game_gravity + 0 ?></td>
                <td><?= $game_obst_height ?></td>
                <td><?= $game_block_length ?></td>
                <td><?= $game_seed ?></td>
                <td><?= $game_score + 0 ?></td>
                <td><?= $game_difficulty + 0 ?></td>
                <td><?= $game_difficulty_standard_deviation + 0 ?></td>
                <td><?= $game_survivors ?></td>
                <td><?= $game_max_players ?></td>
                <td><?= $game_timestamp ?></td>
                <td><img src="<?= $game_image ?>"></img></td>
                <td>
                    <a href="javascript:void(0)" onclick="this.innerHTML = copytoclipboard('<?= htmlspecialchars(create_json($game_id, $game_p_flat, $game_p_hole, $game_p_obstacle, $game_speed, $game_force, $game_gravity, $game_obst_height, $game_block_length, $game_seed, $game_score, $game_difficulty, $game_difficulty_standard_deviation, $game_survivors, $game_max_players)) ?>')">
                        copy
                    </a>
                </td>
            </tr>
        </tbody>
    </table>


    <h1>Average Rating</h1>
    <table>
        <thead>
            <tr>
                <th><div>μ(fun)</div></th>
                <th><div>σ(fun)</div></th>
                <th><div>μ(difficulty)</div></th>
                <th><div>σ(difficulty)</div></th>
                <th><div>μ(score)</div></th>
                <th><div>σ(score)</div></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><div class="stars"><?php print_stars($avgfun) ?> (<?= round($avgfun, 2) + 0 ?>)</div></td>
                <td><?= round($stdfun, 2) + 0 ?></td>
                <td><div class="stars"><?php print_stars($avgdiff) ?> (<?= round($avgdiff, 2) + 0 ?>)</div></td>
                <td><?= round($stddiff, 2) + 0 ?></td>
                <td>
                    <div class="score">
                        <span>
                            <span style="width:<?=$avgscore*100?>%;">
                                <span style="width:<?= $stdscore*100 ?>%;right:-<?= $stdscore*50 ?>%;"></span>
                            </span>
                        </span>(<?= round($avgscore, 2) + 0 ?>)
                    </div>
                </td>
                <td><?= round($stdscore, 2) + 0 ?></td>
            </tr>
        </tbody>
    </table>

    <h1><?= $numberratings ?> Rating<?= $numberratings > 1 ? 's' : '' ?></h1>
    <?php
    $stmt->close();
    ?>
<?php endif; ?>

<?php
if($isuserpage) {
    $stmt = $conn->prepare("SELECT r.game, r.fun, r.difficulty, r.score, r.timestamp FROM rating AS r
        WHERE r.user = ?
        ORDER BY r.timestamp DESC");
    $stmt->bind_param("i", $id);
} else {
    $stmt = $conn->prepare("SELECT r.user, r.fun, r.difficulty, r.score, r.timestamp FROM rating AS r
        WHERE r.game = ?
        ORDER BY r.timestamp DESC");
        $stmt->bind_param("i", $game);
}
$stmt->bind_result($user, $fun, $difficulty, $score, $timestamp);
$stmt->execute();

$diag_fun_diff = array();
$diag_fun_score = array();
$diag_diff_score = array();
$diag_bubble = [];

?>
<table>
    <thead>
        <tr>
            <?php if ($isuserpage): ?>
                <th><div>game</div></th>
            <?php else: ?>
                <th><div>user</div></th>
            <?php endif ?>
            <th><div>fun</div></th>
            <th><div>difficulty</div></th>
            <th><div>score</div></th>
            <th><div>timestamp</div></th>
        </tr>
    </thead>

    <tbody>
        <?php
        while ($stmt->fetch()) {

            array_push($diag_fun_diff, array("x"=>$fun, "y"=>$difficulty));
            array_push($diag_fun_score, array("x"=>$fun, "y"=>$score));
            array_push($diag_diff_score, array("x"=>$difficulty, "y"=>$score));
            array_push($diag_bubble, [
              "x" => $difficulty,
              "y" => $score,
              "r" => $fun*2
            ]);

            ?>
                <tr>
                    <?php if ($isuserpage): ?>
                        <td><a href="javascript:void(0)" onclick="showgameratings(<?=$user?>)"><?= $user ?></a></td>
                    <?php else: ?>
                        <td><a href="javascript:void(0)" onclick="showuser(<?=$user?>)">
                            <?php
                            if($user == 853) echo "Michael";
                            else if($user == 1014) echo "Juri";
                            else echo $user;
                            ?>
                        </a></td>
                    <?php endif ?>
                    <td><div class="stars"><?php print_stars($fun) ?> (<?= $fun + 0 ?>)</div></td>
                    <td><div class="stars"><?php print_stars($difficulty) ?> (<?= $difficulty + 0 ?>)</div></td>
                    <td>
                        <div class="score">
                            <span>
                                <span style="width:<?=$score*100?>%;"></span>
                            </span>(<?= $score + 0 ?>)
                        </div>
                    </td>
                    <td><?= $timestamp ?></td>
                </tr>
            <?php
        }

        $stmt->close();
         ?>
    </tbody>
</table>

<h1>Graphs</h1>

<div class="charts">

    <div>
        <h1>Bubble Score/User Difficulty/Fun</h1>
        <canvas id="overlay-chart-bubble"></canvas>
    </div>
    <div>
        <h1>User Difficulty/Fun</h1>
        <canvas id="overlay-chart-fun-diff"></canvas>
    </div>
    <div>
        <h1>Score/Fun</h1>
        <canvas id="overlay-chart-fun-score"></canvas>
    </div>
    <div>
        <h1>Score/User Difficulty</h1>
        <canvas id="overlay-chart-diff-score"></canvas>
    </div>

    <script type="text/javascript">
        (function(){

            setupScatter("overlay-chart-fun-diff", <?= json_encode($diag_fun_diff)?>, 'user difficulty/fun', [5, 5], [1, 1])
            setupScatter("overlay-chart-fun-score", <?= json_encode($diag_fun_score)?>, 'score/fun', [1, 5], [0, 1])
            setupScatter("overlay-chart-diff-score", <?= json_encode($diag_diff_score)?>, 'score/user difficulty', [1, 5], [0, 1])
            setupBubble("overlay-chart-bubble", <?= json_encode([[
                                                "data" => $diag_bubble,
                                                "backgroundColor" => "rgba(255, 80, 90, 0.6)",
                                                "borderColor" => "rgba(255, 80, 90, 0.8)"
                                                ]]) ?>, "score/user difficulty", [1, 5], [0, 1])

        })()

    </script>
</div>

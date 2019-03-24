
<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

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
            return 'r.game';
        case 1:
            return 'g.difficulty';
        case 2:
            return 'g.difficulty_standard_deviation';
        case 3:
            return 'COUNT(r.id)';
        case 4:
            return 'AVG(r.fun)';
        case 5:
            return 'STD(r.fun)';
        case 6:
            return 'AVG(r.difficulty)';
        case 7:
            return 'STD(r.difficulty)';
        case 8:
            return 'AVG(r.score)';
        case 9:
            return 'STD(r.score)';

        default:
            $_SESSION['last'] = 4;
            $_SESSION['sort'] = "DESC";
            return 'AVG(r.fun)';

    }
}
$order = -1;
if (isset($_POST['order'])) {
    $order = $_POST['order'];
}

$offset = 0;
if (isset($_POST['sb-id'])) {
    $offset = $_POST['sb-id'];
}
$limit = 10;
if (isset($_POST['items'])) {
    $limit = $_POST['items'];
}

$stmt = $conn->prepare("SELECT r.game, g.difficulty, g.difficulty_standard_deviation, COUNT(r.id),
                               AVG(r.fun), STD(r.fun), AVG(r.difficulty), STD(r.difficulty),
                               AVG(r.score), STD(r.score), g.difficulty_metric, g.p_hole, g.p_obstacle, g.speed, g.force, g.gravity, g.obst_height, g.block_length FROM rating AS r
                        INNER JOIN game AS g ON g.id = r.game
                        GROUP BY r.game
                        ORDER BY ".order_by($order, $offset)." ".$_SESSION['sort']."
                        LIMIT ? OFFSET ?");

$stmt->bind_param("ii", $limit, $offset);

$stmt->bind_result($game, $difficulty, $sigma, $numberratings, $avgfun, $stdfun, $avgdiff, $stddiff, $avgscore, $stdscore, $difficulty_metric, $p_hole, $p_obstacle, $speed, $force, $gravity, $obst_height, $block_length);
$stmt->execute();

$data = [];
$gdiffudiff = [];
$diffmetudiff = [];
$diffun = [];
$sigmadiff = [];
$scorediff = [];
$scoreuserdiff = [];
$sigmasigma = [];

while ($stmt->fetch()) {
    if (isset($data[round($avgdiff)])) {
        array_push($data[round($avgdiff)], $difficulty);
    }else{
        $data[round($avgdiff)] = [$difficulty];
    }
    array_push($gdiffudiff, ["x"=>$avgdiff, "y"=>$difficulty]);
    array_push($diffmetudiff, ["x"=>$avgdiff, "y"=>$difficulty_metric]);
    array_push($diffun, ["x"=>$avgfun, "y"=>$difficulty]);
    array_push($sigmadiff, ["x"=>$avgdiff, "y"=>$sigma]);
    array_push($scorediff, ["x"=>$difficulty_metric, "y"=>$avgscore]);
    array_push($scoreuserdiff, ["x"=>$avgdiff, "y"=>$avgscore]);
    array_push($sigmasigma, ["x"=>$stdscore, "y"=>$sigma]);
    ?>
        <tr sb-id="<?= ++$offset ?>">
            <td><a href="javascript:void(0)" onclick="showgameratings(<?= $game ?>)"><?= $game ?></a></td>
            <td><?= $difficulty + 0 ?></td>
            <td><?= $sigma + 0 ?></td>
            <td><?= $numberratings + 0 ?></td>
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
            <td style="background: rgba(0,0,0,<?=$p_hole?>)"><?=$p_hole?></td>
            <td style="background: rgba(0,0,0,<?=$p_obstacle?>)"><?=$p_obstacle?></td>
            <td style="background: rgba(0,0,0,<?=($speed-5)/15?>)"><?=$speed?></td>
            <td style="background: rgba(0,0,0,<?=($force-4)/16?>)"><?=$force?></td>
            <td style="background: rgba(0,0,0,<?=($gravity-0.6)/3?>)"><?=$gravity?></td>
            <td style="background: rgba(0,0,0,<?=($obst_height-1)/9?>)"><?=$obst_height?></td>
            <td style="background: rgba(0,0,0,<?=($block_length-2)/38?>)"><?=$block_length?></td>
        </tr>
    <?php
}

$stmt->close();

foreach ($data as $key => $value) {
    $data[$key] = array_sum($data[$key])/count($data[$key]);
}

$rdata = [];

for ($i = 1; $i < 6; $i++) {
    if (!isset($data[$i])) {
        array_push($rdata, null);
    }else{
        array_push($rdata, $data[$i]);
    }
}

 ?>

<tr style="display:none;">
    <script type="text/javascript">
        updateChart(chartdiffrate, [<?php echo implode(",", $rdata) ?>]);
        updateChart(chartdiffratepoint, <?php echo json_encode($gdiffudiff) ?>);
        updateChart(chartdiffmetrate, <?php echo json_encode($diffmetudiff) ?>);
        updateChart(chartfundiff, <?php echo json_encode($diffun) ?>);
        updateChart(chartsigmadiff, <?php echo json_encode($sigmadiff) ?>);
        updateChart(chartscorediff, <?php echo json_encode($scorediff) ?>);
        updateChart(chartscoreuserdiff, <?php echo json_encode($scoreuserdiff) ?>);
        updateChart(chartsigmasigma, <?php echo json_encode($sigmasigma) ?>);

    </script>
</tr>

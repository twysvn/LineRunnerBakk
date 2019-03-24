<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database.php';


$y = "game.block_length";
if(isset($_POST["y"]))
    $y = $_POST["y"];

$x = "rating.score";
if(isset($_POST["x"]))
    $x = $_POST["x"];

$type = 1;
if(isset($_POST["type"]))
    $type = $_POST["type"];

$z = "1";
if(isset($_POST["z"]))
    $z = $_POST["z"];

function print_option($value, $name, $is, $selected="")
{
    ?> <option value="<?= $value ?>" <?= ($value == $is) ? "selected" : "" ?>><?= $name ?></option> <?php
}

function get_min($value)
{
    switch ($value) {
        case "game.p_flat";
            return 0;
        case "game.p_hole";
            return 0;
        case "game.p_obstacle";
            return 0;
        case "game.speed";
            return 5;
        case "game.`force`";
            return 4;
        case "game.gravity";
            return 0.6;
        case "game.obst_height";
            return 1;
        case "game.block_length";
            return 1;
        case "game.score";
            return 0;
        case "game.difficulty";
            return 0;
        case "game.difficulty_scaled";
            return 0;
        case "game.difficulty_metric";
            return 0;
        case "game.difficulty_test";
            return 0;
        case "game.difficulty_standard_deviation";
            return 0;
        case "game.survivors";
            return 0;

        case "rating.fun":
            return 1;
        case "rating.difficulty":
            return 1;
        case "rating.score":
            return 0;
        case "rating.gender":
            return 0;
        case "rating.age":
            return 0;
    }
}

function get_max($value)
{
    switch ($value) {
        case "game.p_flat";
            return 1;
        case "game.p_hole";
            return 1;
        case "game.p_obstacle";
            return 1;
        case "game.speed";
            return 20;
        case "game.`force`";
            return 20;
        case "game.gravity";
            return 3.6;
        case "game.obst_height";
            return 10;
        case "game.block_length";
            return 40;
        case "game.score";
            return 30;
        case "game.difficulty";
            return 1;
        case "game.difficulty_scaled";
            return 1;
        case "game.difficulty_metric";
            return 1;
        case "game.difficulty_test";
            return 1;
        case "game.difficulty_standard_deviation";
            return 0.5;
        case "game.survivors";
            return 100;

        case "rating.fun":
            return 5;
        case "rating.difficulty":
            return 5;
        case "rating.score":
            return 1;
        case "rating.gender":
            return 2;
        case "rating.age":
            return 60;
    }
}

?>




<div class="">
    <div class="" style="display: block">

        Compare
        <select class="" name="y" onchange="sbecky_submit(this.form)">
            <?php
            $val = $y;
            print_option("rating.fun", "Fun rating", $val);
            print_option("rating.difficulty", "Difficulty rating", $val);
            print_option("rating.score", "User score", $val);
            ?>
        </select>

        to

        <select class="" name="x" onchange="sbecky_submit(this.form)">
            <?php
            $val = $x;
            print_option("game.p_flat", "P(flat)", $val);
            print_option("game.p_hole", "P(hole)", $val);
            print_option("game.p_obstacle", "P(obstacle)", $val);
            print_option("game.speed", "Player speed", $val);
            print_option("game.`force`", "Jump force", $val);
            print_option("game.gravity", "Gravity", $val);
            print_option("game.obst_height", "Obstacle height", $val);
            print_option("game.block_length", "Block length", $val);
            print_option("game.score", "Score", $val);
            print_option("game.difficulty", "Difficulty", $val);
            print_option("game.difficulty_scaled", "Difficulty Scaled", $val);
            print_option("game.difficulty_metric", "Difficulty Metric", $val);
            print_option("game.difficulty_standard_deviation", "Sigma", $val);
            print_option("game.survivors", "Survivors", $val);
            print_option("rating.gender", "Gender", $val);
            print_option("rating.age", "Age", $val);
            print_option("rating.score", "User score", $val);
            print_option("rating.fun", "Fun rating", $val);
            print_option("rating.difficulty", "Difficulty rating", $val);
            ?>
        </select>

        with

        <select class="" name="z" onchange="sbecky_submit(this.form)">
            <?php
            $val = $z;
            print_option("1", "none", $val);
            print_option("game.p_flat", "P(flat)", $val);
            print_option("game.p_hole", "P(hole)", $val);
            print_option("game.p_obstacle", "P(obstacle)", $val);
            print_option("game.speed", "Player speed", $val);
            print_option("game.`force`", "Jump force", $val);
            print_option("game.gravity", "Gravity", $val);
            print_option("game.obst_height", "Obstacle height", $val);
            print_option("game.block_length", "Block length", $val);
            print_option("game.score", "Score", $val);
            print_option("game.difficulty", "Difficulty", $val);
            print_option("game.difficulty_scaled", "Difficulty Scaled", $val);
            print_option("game.difficulty_metric", "Difficulty Metric", $val);
            print_option("game.difficulty_test", "Difficulty Test", $val);
            print_option("game.difficulty_standard_deviation", "Sigma", $val);
            print_option("game.survivors", "Survivors", $val);
            print_option("rating.fun", "Fun rating", $val);
            print_option("rating.difficulty", "Difficulty rating", $val);
            print_option("rating.score", "User score", $val);
            print_option("rating.gender", "Gender", $val);
            print_option("rating.age", "Age", $val);
            ?>
        </select>
        <input type="submit" name="" value="Reload Chart">
    </div>

    <div class="charts">
        <?php

        $type = 1;

        $stmt = $conn->prepare("SELECT MIN($z), MAX($z) FROM game INNER JOIN rating ON rating.game = game.id
                                WHERE type = ? LIMIT 500");
        // echo "$x vs $y $conn->error";
        $stmt->bind_param("i", $type);
        $stmt->bind_result($z_min, $z_max);
        $stmt->execute();
        $stmt->fetch();
        $stmt->close();

        $stmt = $conn->prepare("SELECT $x, $y, $z FROM game INNER JOIN rating ON rating.game = game.id
                                WHERE type = ?");
        // echo "$x vs $y $conn->error";
        $stmt->bind_param("i", $type);
        $stmt->bind_result($x_val, $y_val, $z_val);
        $stmt->execute();

        $arr = array();
        while ($stmt->fetch()) {
            // array_push($arr, array("x"=>$x_val, "y"=>$y_val));
            array_push($arr, [
                "x" => $x_val,
                "y" => $y_val,
                "r" => ($z == "1") ? 3 : ($z_val - $z_min) / ($z_max - $z_min) * 4 + 2
            ]);
        }
        $stmt->close();
        ?>
        <div style="float: left; width: 50%;text-align:center;">
            <canvas id="canvas<?= $type ?>"></canvas>
        </div>
        <script type="text/javascript">
        (function(){
            setupBubble("canvas<?= $type ?>",
            <?= json_encode([[
                "data" => $arr,
                "backgroundColor" => "rgba(255, 80, 90, 0.6)",
                "borderColor" => "rgba(255, 80, 90, 0.8)"
                ]])?>,
                '<?= $y ?>/<?= $x ?>',
                [<?= get_max($y) ?>, <?= get_max($x) ?>],
                [<?= get_min($y) ?>, <?= get_min($x) ?>]);
            })();
        </script>
    </div>
</div>

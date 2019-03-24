<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database.php';


$y = "block_length";
if(isset($_POST["y"]))
    $y = $_POST["y"];

$x = "score";
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
        case "p_flat";
            return 0;
        case "p_hole";
            return 0;
        case "p_obstacle";
            return 0;
        case "speed";
            return 5;
        case "`force`";
            return 4;
        case "gravity";
            return 0.6;
        case "obst_height";
            return 1;
        case "block_length";
            return 1;
        case "score";
            return 0;
        case "difficulty";
            return 0;
        case "difficulty_scaled";
            return 0;
        case "difficulty_metric";
            return 0;
        case "difficulty_test";
            return 0;
        case "difficulty_standard_deviation";
            return 0;
        case "survivors";
            return 0;
    }
}

function get_max($value)
{
    switch ($value) {
        case "p_flat";
            return 1;
        case "p_hole";
            return 1;
        case "p_obstacle";
            return 1;
        case "speed";
            return 20;
        case "`force`";
            return 20;
        case "gravity";
            return 3.6;
        case "obst_height";
            return 10;
        case "block_length";
            return 40;
        case "score";
            return 30;
        case "difficulty";
            return 1;
        case "difficulty_scaled";
            return 1;
        case "difficulty_metric";
            return 1;
        case "difficulty_test";
            return 1;
        case "difficulty_standard_deviation";
            return 0.5;
        case "survivors";
            return 100;
    }
}

?>




<div class="">
    <div class="" style="display: block">

        Compare

        <select class="" name="y" onchange="sbecky_submit(this.form)">
            <?php
            print_option("p_flat", "P(flat)", $y);
            print_option("p_hole", "P(hole)", $y);
            print_option("p_obstacle", "P(obstacle)", $y);
            print_option("speed", "Player speed", $y);
            print_option("`force`", "Jump force", $y);
            print_option("gravity", "Gravity", $y);
            print_option("obst_height", "Obstacle height", $y);
            print_option("block_length", "Block length", $y);
            print_option("score", "Score", $y);
            print_option("difficulty", "Difficulty", $y);
            print_option("difficulty_scaled", "Difficulty Scaled", $y);
            print_option("difficulty_metric", "Difficulty Metric", $y);
            print_option("difficulty_test", "Difficulty Test", $y);
            print_option("difficulty_standard_deviation", "Sigma", $y);
            print_option("survivors", "Survivors", $y);
            ?>
        </select>

        to

        <select class="" name="x" onchange="sbecky_submit(this.form)">
            <?php
            print_option("p_flat", "P(flat)", $x);
            print_option("p_hole", "P(hole)", $x);
            print_option("p_obstacle", "P(obstacle)", $x);
            print_option("speed", "Player speed", $x);
            print_option("`force`", "Jump force", $x);
            print_option("gravity", "Gravity", $x);
            print_option("obst_height", "Obstacle height", $x);
            print_option("block_length", "Block length", $x);
            print_option("score", "Score", $x);
            print_option("difficulty", "Difficulty", $x);
            print_option("difficulty_scaled", "Difficulty Scaled", $x);
            print_option("difficulty_metric", "Difficulty Metric", $x);
            print_option("difficulty_test", "Difficulty Test", $x);
            print_option("difficulty_standard_deviation", "Sigma", $x);
            print_option("survivors", "Survivors", $x);
            ?>
        </select>

        with

        <select class="" name="z" onchange="sbecky_submit(this.form)">
            <?php
            print_option("1", "none", $z);
            print_option("p_flat", "P(flat)", $z);
            print_option("p_hole", "P(hole)", $z);
            print_option("p_obstacle", "P(obstacle)", $z);
            print_option("speed", "Player speed", $z);
            print_option("`force`", "Jump force", $z);
            print_option("gravity", "Gravity", $z);
            print_option("obst_height", "Obstacle height", $z);
            print_option("block_length", "Block length", $z);
            print_option("score", "Score", $z);
            print_option("difficulty", "Difficulty", $z);
            print_option("difficulty_scaled", "Difficulty Scaled", $z);
            print_option("difficulty_metric", "Difficulty Metric", $z);
            print_option("difficulty_test", "Difficulty Test", $z);
            print_option("difficulty_standard_deviation", "Sigma", $z);
            print_option("survivors", "Survivors", $z);
            ?>
        </select>
        <input type="submit" name="" value="Reload Chart">
    </div>

    <div class="charts">
    <?php

    foreach ([1, 0, 2, 3] as $type) {

        $stmt = $conn->prepare("SELECT MIN($z), MAX($z) FROM game
        WHERE type = ? LIMIT 500");
        // echo "$x vs $y $conn->error";
        $stmt->bind_param("i", $type);
        $stmt->bind_result($z_min, $z_max);
        $stmt->execute();
        $stmt->fetch();
        $stmt->close();

        $stmt = $conn->prepare("SELECT $x, $y, $z FROM game
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
                <h1 style="display:inline-block;width:100%;text-align:center;"><?php
                    switch ($type) {
                        case 1:
                            echo "Default";
                            break;
                        case 0:
                            echo "every_second_flat off";
                            break;
                        case 2:
                            echo "smart_level_generation off";
                            break;
                        case 3:
                            echo "every_second_flat off, smart_level_generation off";
                            break;
                    }
                    ?></h1>
                <canvas id="canvas<?= $type ?>" width="100" height="100" style="display:inline-block;max-width: 400px;"></canvas>
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
    <?php
    }
    ?>
    </div>
</div>

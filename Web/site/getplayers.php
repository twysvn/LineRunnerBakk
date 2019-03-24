<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once 'database.php';

$ext = "";
if(isset($_POST["user"]))
    $ext = "WHERE r.user = ?";

$stmt = $conn->prepare("SELECT r.user, COUNT(r.id),
                               AVG(r.fun), STD(r.fun), AVG(r.difficulty), STD(r.difficulty),
                               AVG(r.score), STD(r.score) FROM rating AS r
                        $ext
                        GROUP BY r.user");

if(isset($_POST["user"])){
    $id = $_POST["user"];
    $stmt->bind_param("i", $id);
}
echo "$conn->error";
$stmt->bind_result($id, $count, $avgfun, $stdfun, $avgdiff, $stddiff, $avgscore, $stdscore);
$stmt->execute();

$offset = 0;
while ($stmt->fetch()) {
    ?>
        <tr sb-id="<?= ++$offset ?>">
            <td>
                <a href="javascript:void(0)" onclick="showuser(<?=$id?>)">
                    <?php
                    if($id == 853) echo "Michael";
                    else if($id == 1014) echo "Juri";
                    else echo $id;
                    ?>
                </a>
            </td>
            <td><?= $count ?></td>
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
    <?php
}

$stmt->close();

 ?>

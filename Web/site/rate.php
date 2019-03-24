<?php

// $http_origin = $_SERVER['HTTP_ORIGIN'];
// header("Access-Control-Allow-Origin: $http_origin");

header('Access-Control-Allow-Origin: http://twysvn.com', false);
// header('Access-Control-Allow-Origin: http://localhost:57604', false);
header('Access-Control-Allow-Methods: POST');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization", true);


if (!(isset($_POST['user']) &&
      isset($_POST['fun']) &&
      isset($_POST['difficulty']) &&
      isset($_POST['score']) &&
      isset($_POST['gender']) &&
      isset($_POST['age']) &&
      isset($_POST['id'])))
{
    die("Error: parameter mismach.");
}

$user = $_POST['user'];
$fun = $_POST['fun'];
$difficulty = $_POST['difficulty'];
$score = str_replace( ',', '.', $_POST['score']);
$game = $_POST['id'];
$gender = $_POST['gender'];
$age = $_POST['age'];
require_once 'database.php';
// $stmt = $conn->prepare("INSERT INTO `testrating`(`user`, `fun`, `difficulty`, `score`, `game`) VALUES (?, ?, ?, ?, ?)");
$stmt = $conn->prepare("INSERT INTO `rating`(`user`, `fun`, `difficulty`, `score`, `game`, `gender`, `age`) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iiidsii", $user, $fun, $difficulty, $score, $game, $gender, $age);
$stmt->execute();
$stmt->close();

$stmt = $conn->prepare("INSERT INTO `user`(`id`, `gender`, `age`) VALUES (?, ?, ?)");
$stmt->bind_param("iii", $user, $gender, $age);
$stmt->execute();
$stmt->close();

?>

<p>Thank you for voting!</p>
<p>Would you like to play another level?</p>
<div class="rating-controls">
    <a href="javascript:void(0)" onclick="play_new();">Try another level!</a>
    <a href="javascript:void(0)" onclick="play_again();">Play the same again!</a>
</div>

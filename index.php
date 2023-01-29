<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $host = 'localhost';
    $db   = 'naggissou';
    $user = 'root';
    $pass = '123456';
    $port = "3306";
    $charset = 'utf8';

    $options = [
        \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset;port=$port";
    try {
        $pdo = new \PDO($dsn, $user, $pass, $options);
    } catch (\PDOException $e) {
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }

    $questions = array(
        1 => array("question" => "Les papillons monarques pondent leurs œufs exclusivement sur des asclépiades.", "answer" => "Vrai", "explication" => "Les papillons monarques pondent leurs œufs exclusivement sur des asclépiades, dont les feuilles constituent le seul aliment des chenilles. Une fois adulte, le papillon peut toutefois se nourrir du nectar de plusieurs autres espèces de fleurs."),
        2 => array("question" => "Question de test 2", "answer" => "Faux", "explication" => "Explication de la question 2"),
        3 => array("question" => "Question de test 3", "answer" => "Faux", "explication" => "Explication de la question 3"),
        4 => array("question" => "Question de test 4", "answer" => "Vrai", "explication" => "Explication de la question 4"),
    );

    $username = $_POST['username'] ?? NULL;
    $active = $_POST['active'] ?? NULL;
    $answer = $_POST['answer'] ?? NULL;
    $score = $_POST["score"] ?? NULL;

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Quiz Module i214</title>
        <link rel="stylesheet" href="assets/scripts/styles.css">
    </head>
    <body>
        <div class="page">
            <header>
                <div class="site_title">
                    <h1 class="title">OPEI 1 - Module i214</h1>
                </div>
                <img class="logo" src="assets/img/icon.png">
            </header>
<?php
            if ($username) {
                if ($active) {
                    if ($active > count($questions)) {
                        
                        $stmt3 = $pdo->prepare("SELECT * FROM scores WHERE pseudo = :pseudo");
                        $stmt3->execute([
                            "pseudo" => $username
                        ]);
                        $results2 = $stmt3->fetchAll();

                        if (count($results2) > 0) {
                            if ($score > $results2[0]["score"]) {
                                $stmt4 = $pdo->prepare("DELETE FROM scores WHERE pseudo = :pseudo");
                                $stmt4->execute([
                                    "pseudo" => $username
                                ]);

                                $stmt2 = $pdo->prepare("INSERT INTO scores (pseudo, score) VALUES(:pseudo, :score)");
                                $stmt2->execute([
                                    "pseudo" => $username,
                                    "score" => $score
                                ]);
                            }
                        } else {
                            $stmt2 = $pdo->prepare("INSERT INTO scores (pseudo, score) VALUES(:pseudo, :score)");
                            $stmt2->execute([
                                "pseudo" => $username,
                                "score" => $score
                            ]);
                        }

                        $stmt = $pdo->prepare("SELECT * FROM scores");
                        $stmt->execute();
                        $results = $stmt->fetchAll();
?>
                        <h2 class="sub">Votre score: <?= $score ?>/<?= count($questions) ?></h2>
                        <table>
                            <thead>
                                <tr>
                                    <th colspan="2">Tableau des scores</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Pseudo</strong></td>
                                    <td><strong>Score</strong></td>
                                </tr>
<?php
                                if (count($results) > 0) {
                                    foreach($results as $key => $result) {
?>
                                        <tr>
                                            <td><?= $result["pseudo"] ?></td>
                                            <td><?= $result["score"] ?></td>
                                        </tr>
<?php
                                    }
                                }
?>

                            </tbody>
                        </table>
<?php
                    } else if ($answer) {
                        if ($answer === $questions[$active]["answer"]) {
                            $score = intval($score) + 1;
                        }

?>
                        <div class="question">
                            <h2 class="sub">Question <?= $active ?>/<?= count($questions) ?></h2>
                            <h2 class="detail"><?= $questions[$active]["question"] ?></h2>
                            <h2 class="detail" style="color: gold;">La bonne réponse est: <?= $questions[$active]["answer"] ?></h2>
                            <h2 class="detail"><?= $questions[$active]["explication"] ?></h2>
                            <form action="" method="post">
                                <input type="hidden" name="username" value="<?= $username ?>">
                                <input type="hidden" name="active" value="<?= $active + 1 ?>">
                                <input type="hidden" name="score" value="<?= $score ?>">
                                <input class="next" type="submit" value="Question suivante >">
                            </form> 
                        </div>
<?php
                    } else {
?>
                        <div class="question">
                            <h2 class="sub">Question <?= $active ?>/<?= count($questions) ?></h2>
                            <h2 class="detail"><?= $questions[$active]["question"] ?></h2>
                            <form action="" method="post">
                                <input type="hidden" name="username" value="<?= $username ?>">
                                <input type="hidden" name="active" value="<?= $active ?>">
                                <input type="hidden" name="score" value="<?= $score ?>">
                                <input class="green_b answer" type="submit" value="Vrai" name="answer">
                                <input class="red_b answer" type="submit" value="Faux" name="answer">
                            </form> 
                        </div>
<?php
                    }
                }
            } else {
?>
                <h2 class="sub">Entrez votre pseudo</h2>
                <form class="userform" action="" method="post">
                    <input type="hidden" name="active" value="1">
                    <input id="username" type="text" name="username">
                    <input id="username_submit" type="submit">
                </form>
<?php
            }
?>
            <footer>
                Made by <a href="https://samnx.xyz/">SamNx</a>
            </footer>
        </div>
    </body>
</html>
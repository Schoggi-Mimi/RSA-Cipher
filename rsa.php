<?php

$conn = mysqli_connect("localhost", "root", "", "rsa");
$zaehler = 0;

function primeCheck($prime)
{
    if ($prime == 1) {
        return 0;
    }

    if ($prime == 2) {
        return 1;
    }

    if ($prime % 2 == 0) {
        return 0;
    }

    $ceil = ceil(sqrt($prime));
    for ($i = 3; $i <= $ceil; $i = $i + 2) {
        if ($prime % $i == 0) {
            return 0;
        }
    }
    return 1;
}

function ggT($euclid, $euler)
{ # Iterativ Euklidischer Algorithmus
    while ($euler != 0) {
        $placeholder = bcmod($euclid, $euler);
        $euclid = $euler;
        $euler = $placeholder;
    }
    return $euclid; # euclid > 1 und zu euler teilerfremd, also ggT(euclid, euler) = 1
}

function modInverse($euclid, $euler)
{ # Modular multiplicative Invers wird erzeugt mit erweiterter Euklidischer Algorithmus
    $substitute = 1;
    $modmulinverse = '';
    while (!is_int($modmulinverse)) {
        $modmulinverse = (1 + $substitute * $euler) / $euclid;
        $substitute++;
    }

    return $modmulinverse;
}

function encryptMessage($publickey, $message)
{
    $key = strval($publickey);
    $message = trim($message);
    list($euler, $product) = explode(".", $key);
    $list = "";
    $placeholder = 0;

    for ($number = 0; $number < strlen($message); $number++) {
        $byte = substr($message, $number, 1);

        if ($byte != ' ') {
            $placeholder = ord($byte);
            $code = bcpowmod($placeholder, $euler, $product);
        } else {
            $code = '400';
        }

        if ($list == "") {
            $list .= $code;
        } else {
            $list .= ',' . $code;
        }
    }
    return $list;
}

function decryptMessage($private, $decode)
{
    $key = strval($private);

    list($euclid, $product) = explode(".", $key);
    $str_arr = explode(",", $decode);
    $list = "";

    foreach ($str_arr as $key => $byte) {
        if ($byte != '400') {
            $decode = bcpowmod($byte, $euclid, $product);
            $placeholder = chr((int) $decode);
            $list .= $placeholder;
        } else {
            $list .= " ";
        }
    }
    return $list;
}
?>

<!doctype html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <title>RSA</title>
    <link href="./css/rsa.css" rel="stylesheet" type="text/css">
</head>

<body>
    <header>
        <h1>RSA Schl??sselserver</h1>
    </header>
    <section>
        <article>
            <?php

if (isset($_POST['submit'])) {

    $primep = $_POST['primep'];
    $primeq = $_POST['primeq'];

    if (primeCheck($primep) == 1 && primeCheck($primeq) == 1) {

        $product = bcmul($primep, $primeq); # RSA Produkt n berechnen

        # Eulerschen ??-Funktion berechnen
        if ($primep == $primeq) {
            $euler = bcsub(bcpow($primep, '2'), $primep);
        } else {
            $euler = bcmul(($primep - 1), ($primeq - 1));
        }

        foreach (range(2, 2000) as $number) {
            if (ggT($number, $euler) == 1) {
                $euclid = $number;
                break;
            }
        }

        $modmulinverse = modInverse($euclid, $euler);

        $privatekey = "{$modmulinverse}.{$product}";
        $publickey = "{$euclid}.{$product}";

        $sql = "INSERT INTO encrypt(publickey) VALUES('$publickey')";

        if (!mysqli_query($conn, $sql)) {
            echo "Fehler: " . $sql . "<br>" . mysqli_error($conn);
        }
        echo ("<p>Die Primzahlen {$primep} & {$primeq} haben folgende Schl??ssel erzeugt:</p><br><br>");
        echo ("<p><b class='privatekey'>{$privatekey} = Der private Schl??ssel</b></p><br><br>");
        echo ("<p><b class='publickey'>{$publickey} = Der ??ffentliche Schl??ssel</b></p><br><br>");
        echo ("<p>Geben Sie nur den ??ffentlichen Schl??ssel frei. Den privaten Schl??ssel brauchen Sie, um den entsprechenden Code zu entziffern.<b></b></p>");
    } else {
        echo ("<p>Bei der Eingabe m??ssen beide Zahlen aus Primzahlen bestehen, bitte kehren Sie auf die Homepage zur??ck und versuchen Sie es erneut.</p>");
    }
}

if (isset($_POST['encrypt'])) {

    $message = trim($_POST['message']);
    $public = trim($_POST['public']);

    if (!is_numeric($public) || strpos($public, '.') === false) {
        echo ("<p>Der ??ffentliche Schl??ssel wurde nicht korrekt eingetragen, bitte keheren Sie auf die Homepage zur??ck und versuchen Sie es erneut.</p>");
    } else if ($message && $public) {
        $encryptmessage = encryptMessage($public, $message);

        $sql = "UPDATE encrypt set cipher = '$encryptmessage' WHERE publickey = '$public'";

        if (!mysqli_query($conn, $sql)) {
            echo "Fehler: " . $sql . "<br>" . mysqli_error($conn);
        }
        echo ("<p>Die Verschl??sselung mit dem ??ffentlichen Schl??ssel <b>{$public}</b> hat folgendes Resultat ergeben:</p><br><br>");
        echo ("<p><b class='encryptmessage'>{$encryptmessage}</b></p><br><br>");
        echo ("<p>Geben Sie nun die Verschl??sselung {$encryptmessage} frei.<b></b></p>");
        echo ("<p>Nur der Inhaber eines privaten Schl??ssels kann die entsprechende Nachricht dechiffrieren.<b></b></p>");
    } else {
        echo ("<p>Leider ist ein Fehler aufgetreten, bitte kehren Sie auf die Homepage zur??ck und versuchen Sie es erneut.</p>");
    }
}

if (isset($_POST['decrypt'])) {

    $decode = trim($_POST['decode']);
    $private = trim($_POST['private']);

    if (!is_numeric($private) || strpos($private, '.') === false) {
        echo ("<p>Der private Schl??ssel wurde nicht korrekt eingetragen, bitte keheren Sie auf die Homepage zur??ck und versuchen Sie es erneut.</p>");
    } else if ($decode && $private) {
        $decrypt = decryptMessage($private, $decode);

        if (!isset($_COOKIE['zaehler'])) {
            $zaehler = 1;
        } else {
            $zaehler = $_COOKIE['zaehler'] + 1;
        }
        setcookie("zaehler", $zaehler);

        $sql = "DELETE FROM encrypt WHERE cipher = '$decode'";

        if (!mysqli_query($conn, $sql)) {
            echo "Fehler: " . $sql . "<br>" . mysqli_error($conn);
        }
        echo ("<p>Die Entschl??sselung mit dem privaten Schl??ssel <b>{$private}</b> hat folgendes Resultat ergeben:</p><br><br>");
        echo ("<p><b class='result'>{$decrypt}</b></p><br><br>");
        echo ("<p>Das ist Ihre Geheimnachricht.<b></b></p>");
    } else {
        echo ("<p>Leider ist ein Fehler aufgetreten, bitte keheren Sie auf die Homepage zur??ck und versuchen Sie es erneut.</p>");
    }
}

$conn->close();
?>
        </article>
    </section>
    <div class="button">
        <a href="./index.html#formPrime">Zur Startseite zur??ckkehren</a>
    </div>
    <div id="cookieId">
        <?php
if ($zaehler > 0) {
    echo sprintf("Sie haben %d Mal erfolgreich entschl??sselt", $zaehler);
}
?>
    </div>
    <footer>
        <p id="copyright">RSA ?? Choekyel Nyungmartsang</p>
    </footer>
</body>

</html>
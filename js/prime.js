// JavaScript Document

function primeCheck() {
    var prime = parseInt(document.forms["check"]["prime"].value, 10);

    if (Number.isInteger(prime)) {
        if (prime <= 1) {
            alert(prime + " ist leider kein Primzahl.");
            return false;
        };
        if (prime == 2 || prime == 3) {
            alert(prime + " ist eine Primzahl.");
            return false;
        };

        for (var i = 2; i <= Math.sqrt(prime); i++) {
            if (prime % i == 0) {
                alert(prime + " ist leider kein Primzahl.");
                return false;
            }
        }; alert(prime + " ist eine Primzahl.");
    } else {
        alert("Nur die ganzen Zahlen können überprüft werden. Bitte überprüfen Sie Ihre Eingabe und versuchen es erneut!");
    };
    return false;
}

function doRequest() {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            var jsonObj = JSON.parse(this.responseText);
            var primep = document.getElementById("primep");
            var primeq = document.getElementById("primeq");
            var zitat = document.getElementById("zitat");
            var author = document.getElementById("author");
            primep.value = jsonObj.p;
            primeq.value = jsonObj.q;
            author.innerText = jsonObj.s[0];
            zitat.innerText = jsonObj.s[1];
        }
    };
    xhr.onerror = function () {
        alert("Fehler beim Abfragen der Mitteilung");
    };
    xhr.ontimeout = function () {
        alert("Zeitüberschreitung beim Abfragen der Mitteilung");
    };
    xhr.open("GET", "https://api.tsarma.ch/prime/?key=8m1C3eC23i8Ac33", true);
    xhr.send();
    return false;
}

function validateInput() {
    var inputp = document.getElementById("primep");
    var inputq = document.getElementById("primeq");
    var product = inputp.value * inputq.value;
    var min = 250;
    var max = 2147483647;
    if (product < min) {
        inputp.setCustomValidity("Stellen Sie sicher, dass die gewählten Primzahlen möglichst gross sind!");
    } else if (inputp.value > max) {
        inputp.setCustomValidity("Verwenden Sie Primzahlen kleiner als " + max);
    } else if (inputq.value > max) {
        inputq.setCustomValidity("Verwenden Sie Primzahlen kleiner als " + max);
    } else {
        inputp.setCustomValidity("");
    }
    return false;
}
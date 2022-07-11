// JavaScript Document

var canvas = document.getElementById("rsaDemo");
var ctx = canvas.getContext("2d");

canvas.height = 600;
canvas.width = 1000;

var encryption = "2 3 5 7 11 13 17 19 23 29 31 37 41 43 47";
encryption = encryption.split(" ");

var fontSize = 50;
var columns = canvas.width / fontSize;

var codefall = [];
for (var x = 0; x < columns; x++)
    codefall[x] = 1;

function draw() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.font = fontSize + "px courier";

    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.strokeStyle = "#cf0000";
    ctx.lineWidth = 10;

    ctx.beginPath();
    ctx.moveTo(20, 160);
    ctx.lineTo(20, 20);
    ctx.lineTo(120, 20);
    ctx.lineTo(120, 90);
    ctx.lineTo(40, 90);
    ctx.lineTo(120, 160);
    ctx.stroke();

    ctx.beginPath();
    ctx.moveTo(260, 55);
    ctx.lineTo(210, 20);
    ctx.lineTo(160, 55);
    ctx.lineTo(260, 125);
    ctx.lineTo(210, 160);
    ctx.lineTo(160, 125);
    ctx.stroke();

    ctx.beginPath();
    ctx.moveTo(300, 150);
    ctx.lineTo(300, 20);
    ctx.lineTo(400, 20);
    ctx.lineTo(400, 80);
    ctx.lineTo(300, 80);
    ctx.stroke();
    ctx.beginPath();
    ctx.lineTo(400, 80);
    ctx.lineTo(400, 150);
    ctx.stroke();

    for (var i = 0; i < codefall.length; i++) {
        var text = encryption[Math.floor(Math.random() * encryption.length)];
        ctx.fillText(text, i * fontSize, codefall[i] * fontSize);
        ctx.fillStyle = "#0F0"

        if (codefall[i] * fontSize > canvas.height && Math.random() > 0.975)
            codefall[i] = 0;
        codefall[i]++;
    }
}

setInterval(draw, 100);
var experience;
var canvas;
var ctx;
var currentCircles;
var distance;
var id;
var diam;
var coeff;
var n;
var nbCouples;
var nbCouplesDone;
var allDistances;
var allDiams;
var currentId;
var allCouples;
var countClicksTotal;
var countClicksTotalOnInterval;
var countMoves;
var countClicksOnCircleTotal;
var tableErrors;
var firstClick;
var newFirstClick;
var username;
//variables pour le chrono
var dateStartChrono; //heure du lancement de l'expérience
var end;
var diff;
var timerID;
var interval;

var results;


/*
id = log(d/r + 1)
LARGEUR = d / (pow(2, id) - 1)
*/
function init(url){

	$.get(url + "ajax/getExperience.php", function(data){
		experience = data;
		//affiche le chrono
		$('#chronotime').css('visibility', 'visible');

		//retire le pop username
		if(username === undefined)
			username = $("#username").val();
		$("#inputUsername").fadeOut(500, function(){
			$(this).remove();
		});
		//retire l'alerte de fin d'expérience ou d'erreur
		$(".alert").hide();

		//affiche le bouton restart
		$("#topButtons").css('visibility', 'visible');

		//retire et affiche le bouton terminer
		$('#btnTerminer').remove();
		var buttonTerminer = '<button id="btnTerminer" class="btn btn-success" role="button" onclick="saveRes(); $(this).remove()">Terminer</button>'
		$('#bottomButtons').append(buttonTerminer);

		//cache les autres boutons
		$("#bottomButtons").css('visibility', 'hidden');

		//bloque le bouton recommencer
		$('#replayButton').attr('disabled', 'true');

		//met la barre à 0 et en bleu
		$(".progress-bar").width("0%");
		$(".progress-bar").removeClass("bg-success");

		canvas = document.getElementById('canvas');
		canvas.width = $("#canvas").width();
		canvas.height = canvas.width / 3;

		ctx = canvas.getContext('2d');

		id = parseFloat(experience['indice_diff']); 
		distance = parseInt(experience['distance']); 
		coeff = parseFloat(experience['coeff']);
		n = parseInt(experience['mouvement']) + 1; 
		nbCouples = 4;

		diam = distance / (Math.pow(2, id) - 1);

		var distanceMin = distance;
		var diamMin =  diam;

		allDistances = [distanceMin];
		for (var i = 1; i < nbCouples; i++) {
			allDistances[i] = allDistances[i-1] * coeff;
		}
		allDiams = [diamMin];
		for (var i = 1; i < 2*nbCouples - 1; i++) {
			allDiams[i] = allDiams[i-1] * coeff;
		}

		allCouples = [];
		for (var i = 0; i < nbCouples ; i++) {
			for (var j = i; j < nbCouples + i; j++) {
				allCouples.push([ allDistances[i], allDiams[j] ]);
			}
		}

		randomsParameters();
		
		results = new Object();
		nbCouplesDone = 0;
		countMoves = 0;
		countClicksTotal = 0;
		countClicksOnCircleTotal = 0;
		countClicksTotalOnInterval = 0;
		tableErrors = [];
		firstClick = true;
		newFirstClick = false;

		//initialise le tableau des cercles courants
		currentCircles = [];
		currentCircles[0] = new circle(canvas.width/2 - distance/2, randomInt(diam / 2, canvas.height - diam / 2), diam / 2, 'blue');
		currentCircles[1] = newCircle();
		currentCircles[1].clicked = true;
		
		//event sur la souris pressée
		canvas.addEventListener("mousedown", onClick, false);

		resetChrono();

		//lance l'application si les paramètres vont bien
		if(diamMin > 9 && distanceMin >= allDiams[nbCouples - 1] && allDiams[allDiams.length - 1] <= canvas.height 
			&& allDiams[allDiams.length - 1] + allDistances[allDistances.length - 1] <= canvas.width){
			run();
		}
		else{
			runError();
		}
	});
}

function run(){

	//clear l'écran complet
	ctx.clearRect(0, 0, canvas.width, canvas.height); 
	drawCircles();
	window.requestAnimationFrame(run); //on appelle run en boucle

}

function circle(x, y, radius, color){
	this.x = x; 
	this.y = y;
	this.radius = radius;
	this.color = color;
	this.clicked = false;
	this.draw = function(){
		ctx.fillStyle = this.color;
		ctx.beginPath();
		ctx.arc(this.x, this.y, this.radius, 0, Math.PI*2, true);
		ctx.closePath();
		ctx.fill();
	}

	// (x - Cx)² + (y - Cy)² = r²
	// il faut donc que : sqrt((x - Cx)² + (y - Cy)²) <= radius
	this.clickedOnMe = function(clicX, clicY){
		return Math.sqrt( (clicX - this.x)*(clicX - this.x) + (clicY - this.y)*(clicY - this.y) ) <= this.radius;
	}
}

function drawCircles(){
	for(var i = 0; i < currentCircles.length; i++){
		currentCircles[i].draw();
	}
}

function randomsParameters(){
	var i = randomInt(0, allCouples.length);
	distance = allCouples[i][0];
	diam = allCouples[i][1];
	currentId = Math.round(100 * Math.log2( (distance / diam) + 1)) / 100;
	allCouples.splice(i, 1);
}

function randomInt(min, max){
	return parseInt(Math.random() * (max-min) + min, 10);
}

function newCircle(){

	var line, column; //coordonnées finales

	line = currentCircles[0].y;
	column = currentCircles[0].x + distance;

	return new circle(column, line, diam / 2, 'grey');
}


function onClick(event){

	//quand le popUpStart est présent, on peut pas cliquer pour jouer
	if(!isPopUpVisible){

		var x = event.x;
		var y = event.y;

		var rect = canvas.getBoundingClientRect();

		x -= rect.left;
		y -= rect.top;

		//quand on touche pas le cercle courant
		if(!currentCircles[countMoves%2].clickedOnMe(x, y)){

			currentCircles[countMoves%2].color = 'red';
			setTimeout(function() {
				currentCircles[countMoves%2].color = 'blue';
			}, 200);

		//on lance le chrono quand on touche le 1er cercle de l'expérience
		}else{
			
			if(firstClick){
				firstClick = false;
				$('#replayButton').attr('disabled', null);
				interval = new Date();
				startChrono();
			}
			if(newFirstClick){
				newFirstClick = false;
				interval = new Date();
				chronoContinue();
			}
		}

		//quand on touche le cercle 1
		if(currentCircles[0].clickedOnMe(x, y) ) {
			if(!currentCircles[0].clicked){
				currentCircles[0].clicked = true;
				currentCircles[0].color = 'green';
				setTimeout(function() {
					currentCircles[0].color = 'grey';
				}, 200);
				currentCircles[1].clicked = false;
				currentCircles[1].color = 'blue';
				countMoves++;
				countClicksOnCircleTotal++;
			}
		}
		//quand on touche le cercle 2
		if(currentCircles[1].clickedOnMe(x, y) ) {
			if(!currentCircles[1].clicked){
				currentCircles[1].clicked = true;
				currentCircles[1].color = 'green';
				setTimeout(function() {
					currentCircles[1].color = 'grey';	
				}, 200);
				currentCircles[0].clicked = false;
				currentCircles[0].color = 'blue';
				countMoves++;
				countClicksOnCircleTotal++;
			}
		}

		//incrémente les conteurs que si la chrono a démarré
		if(!firstClick && !newFirstClick){
			countClicksTotal++;
			countClicksTotalOnInterval++;
		}

		//tous les n trajets entre 2 cercles, on change de cercles.
		if(countMoves == n){
			pauseChrono();
			nbCouplesDone++;
			$(".progress-bar").width(((nbCouplesDone / (nbCouples * nbCouples)) * 100)+"%");
			var temp = new Date();
			interval = temp - interval;
			// Stockage du résultat 
			tableErrors.push(Math.round(100 * 100 * countMoves / countClicksTotalOnInterval) / 100);
			results['nom'] = username;
			results['(' + Math.round(distance) + ',' + currentId + ')'] = Math.round(interval / (n - 1));

			if(nbCouplesDone == nbCouples * nbCouples) endExperience();
			else{
				randomsParameters();
				setTimeout(function() {
					currentCircles[0] = new circle(canvas.width/2 - distance/2, randomInt(diam / 2, canvas.height - diam / 2), diam / 2, 'blue');
					currentCircles[1] = newCircle();
					currentCircles[1].clicked = true;
				}, 200);
				countMoves = 0;
				countClicksTotalOnInterval = 0;
				newFirstClick = true;
			}
		}
	}
}

function startChrono(){
	dateStartChrono = new Date();
	doChrono();
}

function pauseChrono(){
	previousChrono = diff;
	clearTimeout(timerID);
}

function resetChrono(){
	clearTimeout(timerID);
	document.getElementById("chronotime").value = '00 : 00 : 00';
}

function doChrono(){

	end = new Date();
	diff = end - dateStartChrono;
	diff = new Date(diff);
	var msec = diff.getMilliseconds();
	var sec = diff.getSeconds();
	var min = diff.getMinutes();
	if (min < 10){
		min = "0" + min;
	}
	if (sec < 10){
		sec = "0" + sec;
	}
	if(msec < 10){
		msec = "00" +msec;
	}
	else if(msec < 100){
		msec = "0" +msec;
	}
	document.getElementById("chronotime").value = min + " : " + sec + " : " + msec;
	timerID = setTimeout("doChrono()", 10);
}

function chronoContinue(){
	dateStartChrono = new Date() - diff;
	dateStartChrono = new Date(dateStartChrono);
	doChrono();
}

function endExperience(){

	var offset = $('#canvas').offset();
	var margeInX = offset.left + 2;
	var margeInY = offset.top + canvas.height / 2 - 50;

	// Scroll to bottom
	$("html, body").animate({ scrollTop: $(document).height() }, 1000);
	
	// Afichage de l'alert
	var html = 
	'<div class="alert alert-info col" role="alert" style="position: absolute;top:' + margeInY + 'px;width:' + canvas.width + 'px;right:' + margeInX + 'px">\
		<strong> Expérience terminée, faites \"Terminer\" pour enregistrer vos résultats </strong>\
	</div>';
	$("#experience").append(html);

	//on supprime les cercles
	currentCircles = [];

	//barre de progression passe verte
	$(".progress-bar").addClass("bg-success");
	$("#bottomButtons").css('visibility', 'visible');
}

function download_csv(){

	var csv = "Nom\t\t";
	var tt = 0;

	var resultSort = [];
	for (var object in results) {
    	resultSort.push([object, results[object]]);
	}
	//trie dans l'ordre croissant
	resultSort.sort(function tableSort(a, b){
	    if (a < b) return -1;
	    else if (a == b) return 0;
	    else return 1;

	});
	//met ensuite les ID dans l'ordre décroissant
	for(var i = 0; i < 16; i+=4){
		var temp = resultSort[i];
		resultSort[i] = resultSort[i + 3];
		resultSort[i + 3] = temp;
		temp = resultSort[i + 1];
		resultSort[i + 1] = resultSort[i + 2];
		resultSort[i + 2] = temp;
	}

	for(var i = 0; i < 16; i++){
		csv += resultSort[i][0] + "\t";
	}
	csv += "Total \n" + username + "\tTemps moyens (ms) \t";
	for(var i = 0; i < 16; i++){
		csv += resultSort[i][1] + "\t";
		tt += resultSort[i][1] * (n - 1);
	}

	csv += tt + "\n" + username + "\tTaux d'erreurs (%) \t";
	for (var i = 0; i < tableErrors.length; i++) {
		csv += 100 - Math.round(100 * tableErrors[i]) / 100 + "\t";
	}
	csv += Math.round(100 * (100 - 100 * countClicksOnCircleTotal / countClicksTotal)) / 100; 

    var hiddenElement = document.createElement('a');
    hiddenElement.href = 'data:text/csv;charset=utf-8,' + encodeURI(csv);
    hiddenElement.target = '_blank';
    hiddenElement.download = username + '.csv';
    hiddenElement.click();
}

function runError(){
	var offset = $('#canvas').offset();
	var margeInX = offset.left + 2;
	var margeInY = offset.top + canvas.height / 2 - 50;

	var html = 
		'<div class="alert alert-danger col" role="alert" style="position: absolute;top:' + margeInY + 'px;width:' + canvas.width + 'px;right:' + margeInX + 'px">\
			<strong> Erreur : La taille de l\'écran n\'est pas compatible pour l\'expérience courante </strong>\
		</div>';
	$("#experience").append(html);
}

function saveRes(){

	var offset = $('#canvas').offset();
	var margeInX = offset.left + 2;
	var margeInY = offset.top + canvas.height / 2 - 50;

	var url = "ajax/saveResults.php";

	$.post(url, results, function(data){
		// Afichage de l'alert
		$(".alert").remove();
		var html = 
		'<div class="alert alert-success col" role="alert" style="position: absolute;top:' + margeInY + 'px;width:' + canvas.width + 'px;right:' + margeInX + 'px">\
			<strong>Vos données ont correctement été enregistrées</strong>\
		</div>';
		$("#experience").append(html);
	});
}
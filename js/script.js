//canvas et contexte
var canvas = $('#canvas');
canvas[0].width = $("#canvas").width();
canvas[0].height = $("#canvas").height();
var ctx = canvas[0].getContext('2d');

// chemins
var chemins;
var currentPath;

//variables de jeu
var mouseX;
var mouseY;
var arrayStartEnd = [];
var backPixels = [];
var wayStarted = false;
var perfectGame = true;
var chrono = new Timer();
var isTraining;
var path;
var endExperience = false;

//couleurs
var colorStart = '#00ff00';
var colorEnd = '#ff0000';
var colorWay = '#7e7e7e';
var colorBackground = '#ffffff';
var colorBackPixelsGood = '#00ff00';
var colorBackPixelsBad = '#fe0000';

function eventListeners(){
	canvas.mousemove(function(event){
		var x = event.clientX;
		var y = event.clientY;

		var rect = canvas[0].getBoundingClientRect();
		x -= rect.left;
		y -= rect.top;

		mouseX = x;
		mouseY = y;

		var pixel = ctx.getImageData(mouseX, mouseY, 1, 1);
		var dat = pixel.data;
		var codePixel = '#' + dat[0].toString(16).lpad(0, 2) + dat[1].toString(16).lpad(0, 2) + dat[2].toString(16).lpad(0, 2);
		//le chemin commence : on passe du vert au gris 
		if(!wayStarted){
			arrayStartEnd.push(codePixel);
			if(arrayStartEnd.length > 2){
				arrayStartEnd.shift();
			}
			//Ca part.
			if(arrayStartEnd[0] == colorStart && arrayStartEnd[1] == colorWay){
				perfectGame = true;
				wayStarted = true;
				chrono.start();
				colorWay = '#e8e8e8';
				path.setColor(colorWay);
			}
		}else{
			if(codePixel == colorEnd){
				wayStarted = false;
				backPixels = [];
				if(perfectGame){
					$('#success')[0].play();
					chrono.pause();

					//enregistrement du temps réalisé sur le chemin courant
					var time_done = (parseInt(chrono.min) * 60 * 1000) + (parseInt(chrono.sec) * 1000) + parseInt(chrono.msec);
					$.post("../ajax/addTimeInExperience.php", {id_path: chemins[currentPath].id, time: time_done});

					resetVariables();
					currentPath++;
					// il reste encore des chemins a faire
					if(currentPath < chemins.length){
						path = new Path(ctx);
						$.each(chemins[currentPath].primitives, function(key, primitive){
							path.add(new Arc(1 / (primitive.courbure), primitive.angle * Math.PI / 180, colorWay), primitive.orientation);
						});
						path.setWidth(chemins[currentPath].width);
						draw();
					}else{
						ctx.clearRect(0, 0, canvas[0].width, canvas[0].height);
						endExperience = true;
						alert("Expérience terminée, merci d'avoir participé !");
					}

				}else{
					chrono.reset();
				}
			}
		}
		$('#coordMouse').val("x : " + mouseX + "; " + " y : " + mouseY);
	});
}

function resetVariables(){
	arrayStartEnd = [];
	backPixels = [];
	wayStarted = false;
	perfectGame = true;
	endExperience = false;
	chrono.reset();
}

function setPixels(x, y){
	//dit si le pixel existe déjà dans le tableau ou non
	var isPresent = false;
	$(backPixels).each(function(index, value){
		if(value.x == x && value.y == y){
			isPresent = true;
			return false;//break
		}
	});
	//on rajoute le pixel s'il n'existe pas deja
	if(!isPresent){
		var newPix = new Pixel(x, y);
		backPixels.push(newPix);
		//dit si le pixel est dans le chemin ou non (change sa couleur en fonction)
		var pixelCurrent = ctx.getImageData(x, y, 1, 1);
		var data = pixelCurrent.data;
		//s'il n'y rien sous notre souris (hors chemin)
		var codePixelCurrent = '#' + data[0].toString(16).lpad(0, 2) + data[1].toString(16).lpad(0, 2) + data[2].toString(16).lpad(0, 2);
		//souris sur fond blanc -> hors chemin
		if(codePixelCurrent == colorBackground){
			//buzz qu'une seule fois
			if(perfectGame){
				$('#buzzer')[0].play();
				colorWay = '#7e7e7e';
				path.setColor(colorWay);
				setTimeout(function(){
					resetVariables();
				}, 1000);
			}
			perfectGame = false;
			newPix.color = colorBackPixelsBad;
		}
	}
}

String.prototype.lpad = function(padString, length) {
	var str = this;
	while (str.length < length)
		str = padString + str;
	return str;
}

function Pixel(x, y){
	this.x = x;
	this.y = y;
	this.size = 1;
	this.color = colorBackPixelsGood; //couleur du pixel quand il est dans le chemin
	this.draw = function(){
		ctx.fillStyle = this.color;
		ctx.beginPath();
		ctx.rect(this.x, this.y, this.size, this.size);
		ctx.fill();
	}
}

function drawBackPixels(){
	for(var i = 1; i < backPixels.length; i++){
		ctx.strokeStyle = backPixels[i-1].color;
		ctx.lineWidth = 2;
		ctx.beginPath();
		ctx.moveTo(backPixels[i-1].x,backPixels[i-1].y);
		ctx.lineTo(backPixels[i].x,backPixels[i].y);
		ctx.stroke();
	}
}

function drawBack(){
	ctx.beginPath();
	ctx.fillStyle = colorBackground;
	ctx.rect(0, 0, canvas[0].width, canvas[0].height);
	ctx.fill();
}

function start(){

	resetVariables();
	eventListeners();
	currentPath = 0;
	//canvas[0].webkitRequestFullscreen();
	$('#chronotime').css('visibility', 'visible');

	if(isTraining){
		path = new PathTrain(ctx);
		draw();
	}else{
		$.get("ajax/getCurrentExperience.php", function(result){
			chemins = result;
			path = new Path(ctx);
			$.each(chemins[currentPath].primitives, function(key, primitive){
				path.add(new Arc(1 / (primitive.courbure), primitive.angle * Math.PI / 180, colorWay), primitive.orientation);
			});
			path.setWidth(chemins[currentPath].width);
			draw();
		});
	}
}


function draw(){

	ctx.clearRect(0, 0, canvas[0].width, canvas[0].height); 

	if(!endExperience){

		//dessins
		drawBack();
		path.draw();
		if(wayStarted){
			setPixels(mouseX, mouseY);
		}
		
		drawBackPixels();
		window.requestAnimationFrame(draw); //on appelle draw en boucle
	}
}

function Timer(){
	this.dateStartChrono;
	this.end;
	this.diff;
	this.min;
	this.sec;
	this.msec;
	this.timerID;
	this.start = function(){
		this.dateStartChrono = new Date();
		this.run();
	}
	this.run = function(){
		this.end = new Date();
		this.diff = this.end - this.dateStartChrono;
		this.diff = new Date(this.diff);
		this.msec = this.diff.getMilliseconds();
		this.sec = this.diff.getSeconds();
		this.min = this.diff.getMinutes();
		if (this.min < 10){
			this.min = "0" + this.min;
		}
		if (this.sec < 10){
			this.sec = "0" + this.sec;
		}
		if(this.msec < 10){
			this.msec = "00" +this.msec;
		}
		else if(this.msec < 100){
			this.msec = "0" +this.msec;
		}
		$('#chronotime').val(this.min + " : " + this.sec + " : " + this.msec);
		this.timerID = setTimeout(this.run.bind(this), 10);
	}
	this.reset = function(){
		clearTimeout(this.timerID);
		$('#chronotime').val('00 : 00 : 00');
		this.dateStartChrono = new Date();
	}
	this.pause = function(){
		clearTimeout(this.timerID);
	}
	this.resume = function(){
		this.dateStartChrono = new Date() - this.diff;
		this.dateStartChrono = new Date(this.dateStartChrono);
		this.run();
	}
}

function PathTrain(ctx){

	this.ctx = ctx

	//crée l'arc vert de départ (constante pour tous les chemins)
	var arcStart = new Arc(canvas[0].height / 2 - 40 - 10, undefined, colorStart, ctx);
	arcStart.center = {x:canvas[0].width / 2, y:canvas[0].height / 2};
	arcStart.start = Math.PI + Math.PI / 50;	
	arcStart.end = arcStart.start - Math.PI / 50;
	arcStart.isTrigonometrique = true;
	this.arcs = [arcStart];

	//crée l'arc vert de départ (constante pour tous les chemins)
	var mainArc = new Arc(arcStart.radius, undefined, colorWay, ctx);
	mainArc.center = arcStart.center;
	mainArc.start = Math.PI - Math.PI / 25;
	mainArc.end = arcStart.start;	
	mainArc.isTrigonometrique = true;
	this.arcs.push(mainArc);

	// //initialise un arc de fin (qui changera à chaque ajout d'arc dans le chemin)
	var arcEnd = new Arc(mainArc.radius, undefined, colorEnd, ctx);
	arcEnd.center = mainArc.center;
	arcEnd.start = Math.PI - Math.PI / 50;
	arcEnd.end = mainArc.start;
	arcEnd.isTrigonometrique = true;
	this.arcs.push(arcEnd);

	this.draw = function(){
		$.each(this.arcs, function(index, arc){
			arc.draw();
		});
	}
}
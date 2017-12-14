//canvas et contexte
var canvas = $('#canvas');
canvas[0].width = $("#canvas").width();
canvas[0].height = $("#canvas").height();
var ctx = canvas[0].getContext('2d');

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

//couleurs
var colorStart = '#00ff00';
var colorEnd = '#ff0000';
var colorWay = '#e8e8e8';
var colorBackground = '#ffffff';
var colorBackPixelsGood = '#00ff00';
var colorBackPixelsBad = '#ff0000';

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
				chrono.run();
			}
		}else{
			if(codePixel == colorEnd){
				wayStarted = false;
				backPixels = [];
				if(perfectGame){
					$('#success')[0].play();
					chrono.pause();
				}else{
					chrono.reset();
				}
			}
		}
		//$('#coordMouse').val("x : " + mouseX + "; " + " y : " + mouseY);
	});
}

function resetVariables(){
	arrayStartEnd = [];
	backPixels = [];
	wayStarted = false;
	perfectGame = true;
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
			}
			perfectGame = false;
			newPix.color = '#ff0000';
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
	//canvas[0].webkitRequestFullscreen();
	$('#chronotime').css('visibility', 'visible');

	if(isTraining){
		path = new PathTrain();
	}else{
		path = new Path();
		path.add(new Arc(100, Math.PI/3, colorWay));
		path.add(new Arc(200, Math.PI/2, colorWay));
		path.add(new Arc(300, Math.PI/2, colorWay));
		path.add(new Arc(300, Math.PI, colorWay));
	}
	draw();
}


function draw(){
	ctx.clearRect(0, 0, canvas[0].width, canvas[0].height); 
	//dessins
	drawBack();
	path.draw();
	if(wayStarted){
		setPixels(mouseX, mouseY);
	}
	
	drawBackPixels();
	window.requestAnimationFrame(draw); //on appelle draw en boucle
}

function Timer(){
	this.dateStartChrono = new Date();
	this.end;
	this.diff;
	this.min;
	this.sec;
	this.msec;
	this.timerID;
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

function PathTrain(){
	//crée l'arc vert de départ (constante pour tous les chemins)
	var arcStart = new Arc(canvas[0].height / 2 - 40 - 10, undefined, colorStart);
	arcStart.center = {x:canvas[0].width / 2, y:canvas[0].height / 2};
	arcStart.start = Math.PI + Math.PI / 50;	
	arcStart.end = arcStart.start - Math.PI / 50;
	arcStart.isTrigonometrique = true;
	this.arcs = [arcStart];

	//crée l'arc vert de départ (constante pour tous les chemins)
	var mainArc = new Arc(arcStart.radius, undefined, colorWay);
	mainArc.center = arcStart.center;
	mainArc.start = Math.PI - Math.PI / 25;
	mainArc.end = arcStart.start;	
	mainArc.isTrigonometrique = true;
	this.arcs.push(mainArc);

	// //initialise un arc de fin (qui changera à chaque ajout d'arc dans le chemin)
	var arcEnd = new Arc(mainArc.radius, undefined, colorEnd);
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
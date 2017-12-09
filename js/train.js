//canvas et contexte
var canvasTrain = $('#canvas');
canvasTrain[0].width = $("#canvas").width();
canvasTrain[0].height = $("#canvas").height();
var ctxTrain = canvasTrain[0].getContext('2d');
//variables de jeu
var backPixelsTrain = [];	//pixels qui suit la souris
var mouseXTrain;
var mouseYTrain;
var laGlobaleJadoreTrain = true;
var wayStartedTrain = false;
var perfectGameTrain;

var pathTrain = new PathTrain();

function eventListenersTrain(){
	canvasTrain.click(function(event){
		var x = event.clientX;
		var y = event.clientY;

		var rect = canvasTrain[0].getBoundingClientRect();
		x -= rect.left;
		y -= rect.top;

		var pixelCurrent = ctxTrain.getImageData(x, y, 1, 1);
		var data = pixelCurrent.data;
		var codePixelCurrent = '#' + data[0].toString(16).lpad(0, 2) + data[1].toString(16).lpad(0, 2) + data[2].toString(16).lpad(0, 2);
		//si on clique sur la zone verte
		if(codePixelCurrent == '#00ff00'){
			laGlobaleJadoreTrain = false;
			perfectGameTrain = true;
			wayStartedTrain = true;
			var chrono = new TimerTrain();	
			chrono.run();
			canvasTrain.mousemove(function(event){
				var x = event.clientX;
				var y = event.clientY;

				var rect = canvasTrain[0].getBoundingClientRect();
				x -= rect.left;
				y -= rect.top;

				mouseXTrain = x;
				mouseYTrain = y;

				var pixel = ctxTrain.getImageData(mouseXTrain, mouseYTrain, 1, 1);
				var dat = pixel.data;
				var codePixel = '#' + dat[0].toString(16).lpad(0, 2) + dat[1].toString(16).lpad(0, 2) + dat[2].toString(16).lpad(0, 2);
				console.log(codePixel, perfectGameTrain)
				if(codePixel == '#ff0000'){
					wayStartedTrain = false;
					backPixelsTrain = [];
					if(perfectGameTrain){
						$('#success')[0].play();
						chrono.pause();
					}else{
						chrono.reset();
					}
				}
				//$('#coordMouse').val("x : " + mouseX + "; " + " y : " + mouseY);
			});
		}
	});
}

function setPixelsTrain(x, y){
	var maxLength = 15;
	//dit si le pixel existe déjà dans le tableau ou non
	var isPresent = false;
	$(backPixelsTrain).each(function(index, value){
		if(value.x == x && value.y == y){
			isPresent = true;
			return false; //break
		}
	});
	//on rajoute le pixel s'il n'existe pas deja
	if(!isPresent){
		var newPix = new PixelTrain(x, y);
		backPixelsTrain.push(newPix);
		//dit si le pixel est dans le chemin ou non (change sa couleur en fonction)
		var pixelCurrent = ctxTrain.getImageData(x, y, 1, 1);
		var data = pixelCurrent.data;
		//s'il n'y rien sous notre souris (hors chemin)
		var codePixelCurrent = '#' + data[0].toString(16).lpad(0, 2) + data[1].toString(16).lpad(0, 2) + data[2].toString(16).lpad(0, 2);
		//souris sur fond blanc -> hors chemin
		if(codePixelCurrent == '#ffffff'){
			if(laGlobaleJadoreTrain){
				laGlobaleJadoreTrain = false;
				perfectGameTrain = false;
				$('#buzzer')[0].play();
			}
			newPix.color = '#ff0000';
			pathTrain.setColor('#e8e8e8');
		}else{
			pathTrain.setColor('#000000');
			laGlobaleJadoreTrain = true;
		}
	}
	//on supprime le dernier pixel
	if(backPixelsTrain.length > maxLength){
		backPixelsTrain.splice(0, 1);
	}
}

String.prototype.lpad = function(padString, length) {
	var str = this;
	while (str.length < length)
		str = padString + str;
	return str;
}

function PixelTrain(x, y){
	this.x = x;
	this.y = y;
	this.size = 1;
	this.color = "#00ff00"; //couleur du pixel quand il est dans le chemin
	this.draw = function(){
		ctxTrain.fillStyle = this.color;
		ctxTrain.beginPath();
		ctxTrain.rect(this.x, this.y, this.size, this.size);
		ctxTrain.fill();
	}
}
//function arc(radius, angle, color='#000000')
function Arc(radius, angle, color='#000000'){
	this.radius = radius;
	this.angle = angle;
	this.center = {x:0, y:0};
	this.start;
	this.end;
	this.isTrigonometrique;
	this.color = color;
	this.draw = function(){
		ctxTrain.strokeStyle = this.color;
		ctxTrain.beginPath();
		ctxTrain.lineWidth = 80;
		ctxTrain.arc(this.center.x, this.center.y, this.radius, this.start, this.end, this.isTrigonometrique);
		ctxTrain.stroke();
	}
	this.getStart = function(){
		return {x:this.center.x + this.radius * Math.cos(this.start), y:this.center.y + this.radius * Math.sin(this.start)};
	}
	this.getEnd = function(){
		return {x:this.center.x + this.radius * Math.cos(this.end), y:this.center.y + this.radius * Math.sin(this.end)};
	}
}

function drawbackPixelsTrain(){
	for(var i = 1; i < backPixelsTrain.length; i++){
		ctxTrain.strokeStyle = backPixelsTrain[i-1].color;
		ctxTrain.lineWidth = 2;
		ctxTrain.beginPath();
		ctxTrain.moveTo(backPixelsTrain[i-1].x,backPixelsTrain[i-1].y);
		ctxTrain.lineTo(backPixelsTrain[i].x,backPixelsTrain[i].y);
		ctxTrain.stroke();
	}
}

function drawBackTrain(){
	ctxTrain.beginPath();
	ctxTrain.fillStyle = '#ffffff';
	ctxTrain.rect(0, 0, canvasTrain[0].width, canvasTrain[0].height);
	ctxTrain.fill();
}

function startTrain(){
	eventListenersTrain();
	$('#chronotime').css('visibility', 'visible');
	backPixelsTrain = [];
	drawTrain();
}

function drawTrain(){
	ctxTrain.clearRect(0, 0, canvas[0].width, canvas[0].height); 
	//dessins
	drawBackTrain();
	pathTrain.draw();
	if(wayStartedTrain){
		setPixelsTrain(mouseXTrain, mouseYTrain);
	}
	
	drawbackPixelsTrain();
	window.requestAnimationFrame(drawTrain); //on appelle draw en boucle
}

function TimerTrain(){
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
	var arcStart = new Arc(canvas[0].height / 2 - 40, undefined, '#00ff00');
	arcStart.center = {x:canvas[0].width / 2, y:canvas[0].height / 2};
	arcStart.start = Math.PI + Math.PI / 50;	
	arcStart.end = arcStart.start - Math.PI / 50;
	arcStart.isTrigonometrique = true;
	this.arcs = [arcStart];

	//crée l'arc vert de départ (constante pour tous les chemins)
	var mainArc = new Arc(arcStart.radius, undefined, '#000000');
	mainArc.center = arcStart.center;
	mainArc.start = Math.PI - Math.PI / 25;
	mainArc.end = arcStart.start;	
	mainArc.isTrigonometrique = true;
	this.arcs.push(mainArc);

	// //initialise un arc de fin (qui changera à chaque ajout d'arc dans le chemin)
	var arcEnd = new Arc(mainArc.radius, undefined, '#ff0000');
	arcEnd.center = mainArc.center;
	arcEnd.start = Math.PI - Math.PI / 50;
	arcEnd.end = mainArc.start;
	arcEnd.isTrigonometrique = true;
	this.arcs.push(arcEnd);

	this.angle;
	this.draw = function(){
		$.each(this.arcs, function(index, arc){
			arc.draw();
		});
	}
	//fonction qui change la couleur de tous les arcs du chemin (sauf départ et arrivée)
	this.setColor = function(color){
		var lastIndex = this.arcs.length - 1;
		$.each(this.arcs, function(index, arc){
			if(index != 0 && index != lastIndex){
				arc.color = color;
			}
		});
	}
}
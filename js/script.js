//canvas et contexte
var canvas = $('#canvas');
canvas[0].width = $("#canvas").width();
canvas[0].height = $("#canvas").height();
var ctx = canvas[0].getContext('2d');
//variables de jeu
var backPixels = [];	//pixels qui suit la souris
var experienceCenter = new pixel(canvas[0].width/3, 200);

var mouseX;
var mouseY;
var laGlobaleJadore = true;
var wayStarted = false;
var perfectGame = true;

var path = new Path(experienceCenter, 100, 0, Math.PI);
path.add(new Arc(100, Math.PI/3));
path.add(new Arc(200, Math.PI/2));
path.add(new Arc(300, Math.PI/2));
path.add(new Arc(300, Math.PI));

canvas.click(function(event){
	var x = event.clientX;
	var y = event.clientY;

	var rect = canvas[0].getBoundingClientRect();
	x -= rect.left;
	y -= rect.top;

	var pixelCurrent = ctx.getImageData(x, y, 1, 1);
	var data = pixelCurrent.data;
	var codePixelCurrent = '#' + data[0].toString(16).lpad(0, 2) + data[1].toString(16).lpad(0, 2) + data[2].toString(16).lpad(0, 2);
	//si on clique sur la zone verte
	if(codePixelCurrent == '#00ff00'){
		laGlobaleJadore = false;
		wayStarted = true;
		var chrono = new Timer();	
		chrono.run();
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
			if(codePixel == '#ff0000'){
				wayStarted = false;
				backPixels = [];
				if(perfectGame){
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

function setPixels(x, y){

	var maxLength = 15;
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
		var newPix = new pixel(x, y);
		backPixels.push(newPix);
		//dit si le pixel est dans le chemin ou non (change sa couleur en fonction)
		var pixelCurrent = ctx.getImageData(x, y, 1, 1);
		var data = pixelCurrent.data;
		//s'il n'y rien sous notre souris (hors chemin)
		var codePixelCurrent = '#' + data[0].toString(16).lpad(0, 2) + data[1].toString(16).lpad(0, 2) + data[2].toString(16).lpad(0, 2);
		//souris sur fond blanc -> hors chemin
		if(codePixelCurrent == '#ffffff'){
			if(laGlobaleJadore){
				laGlobaleJadore = false;
				perfectGame = false;
				$('#buzzer')[0].play();
			}
			newPix.color = '#ff0000';
			path.setColor('#e8e8e8');
		}else{
			path.setColor('#000000');
			laGlobaleJadore = true;
		}
	}
	//on supprime le dernier pixel
	if(backPixels.length > maxLength){
		backPixels.splice(0, 1);
	}
}

function distance(p1, p2){
	return Math.sqrt(
		(p2.x - p1.x)*(p2.x - p1.x) +
		(p2.y - p1.y)*(p2.y - p1.y));
}

String.prototype.lpad = function(padString, length) {
	var str = this;
	while (str.length < length)
		str = padString + str;
	return str;
}

function pixel(x, y){
	this.x = x;
	this.y = y;
	this.size = 1;
	this.color = "#00ff00"; //couleur du pixel quand il est dans le chemin
	this.draw = function(){
		ctx.fillStyle = this.color;
		ctx.beginPath();
		ctx.rect(this.x, this.y, this.size, this.size);
		ctx.fill();
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
		ctx.strokeStyle = this.color;
		ctx.beginPath();
		ctx.lineWidth = 80;
		ctx.arc(this.center.x, this.center.y, this.radius, this.start, this.end, this.isTrigonometrique);
		ctx.stroke();
	}
	this.getStart = function(){
		return {x:this.center.x + this.radius * Math.cos(this.start), y:this.center.y + this.radius * Math.sin(this.start)};
	}
	this.getEnd = function(){
		return {x:this.center.x + this.radius * Math.cos(this.end), y:this.center.y + this.radius * Math.sin(this.end)};
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
	ctx.fillStyle = '#ffffff';
	ctx.rect(0, 0, canvas[0].width, canvas[0].height);
	ctx.fill();
}

function start(){
	$('#chronotime').css('visibility', 'visible');
	backPixels = [];
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

function Path(){
	//crée l'arc vert de départ (constante pour tous les chemins)
	var arcStart = new Arc(1000, undefined, '#00ff00');
	arcStart.center = {x:50, y:canvas[0].height / 2 + arcStart.radius};
	arcStart.start = -Math.PI/2;
	arcStart.end = arcStart.start - Math.PI / 100;
	arcStart.isTrigonometrique = true;
	this.arcs = [arcStart];

	//initialise un arc de fin (qui changera à chaque ajout d'arc dans le chemin)
	var arcEnd = new Arc(1000, undefined, '#ff0000');
	arcEnd.center = arcStart.center;
	arcEnd.start = arcStart.start + Math.PI/100;
	arcEnd.end = arcStart.start;
	arcEnd.isTrigonometrique = true;
	this.arcs.push(arcEnd);

	this.angle;
	this.draw = function(){
		$.each(this.arcs, function(index, arc){
			arc.draw();
		});
	}
	this.add = function(arc){
		//retire le dernier élément (arc de fin de chemin)
		this.arcs.splice(this.arcs.length - 1, 1);

		//ajoute celui souhaité (faites confiance aux calculs, ça a été posé sur papier.)
		var lastCurrentArc = this.arcs[this.arcs.length - 1];
		var xCenter = lastCurrentArc.center.x;
		var yCenter = lastCurrentArc.center.y;
		var xStart = lastCurrentArc.getStart().x;
		var yStart = lastCurrentArc.getStart().y;

		this.angle = Math.atan((yCenter - yStart) / (xCenter - xStart));
		var xNewCenter = xStart + arc.radius * Math.cos(this.angle);
		var yNewCenter = yStart + arc.radius * Math.sin(this.angle);
		var newCenter = {x:xNewCenter, y:yNewCenter};

		arc.center = newCenter;
		// arc.end = lastCurrentArc.start + lastCurrentArc.angle;
		// arc.start = arc.end + lastCurrentArc.angle - this.angle;
		arc.end = Math.PI + this.angle;
		if(this.arcs.length % 2 == 1){
			arc.start = arc.end - arc.angle;
			arc.isTrigonometrique = false;
		}else{
			arc.start = arc.end + arc.angle;
			arc.isTrigonometrique = true;
		}
		this.arcs.push(arc);
		//recalcule le dernier arc et l'ajoute
		this.addArcEnd();
	}
	//fonction qui calcul un arc de fin selon le chemin courant et l'insert
	this.addArcEnd = function(){
		var lastCurrentArc = this.arcs[this.arcs.length - 1];
		var newEnd = new Arc(lastCurrentArc.radius, undefined, '#ff0000');
		newEnd.center = lastCurrentArc.center;
		newEnd.end = lastCurrentArc.start;
		if(this.arcs.length % 2 == 1){
			newEnd.isTrigonometrique = true;
			newEnd.start = newEnd.end + Math.PI/(lastCurrentArc.radius / 10);
		}else{
			newEnd.isTrigonometrique = false;
			newEnd.start = newEnd.end - Math.PI/(lastCurrentArc.radius / 10);
		}
		this.arcs.push(newEnd);
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
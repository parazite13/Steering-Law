//canvas et contexte
var canvas = $('#canvas');
canvas[0].width = $("#canvas").width();
canvas[0].height = $("#canvas").height();
var ctx = canvas[0].getContext('2d');
//variables de jeu
var backPixels = [];	//pixels qui suit la souris
var experienceCenter = new pixel(canvas[0].width/2, 400);
var arc1 = new arc(experienceCenter, 300, 0, Math.PI);
var arcStart = new arc(arc1.center, arc1.radius, arc1.end, arc1.end - Math.PI/10, '#00ff00');
canvas.mousemove(function(event){
	var x = event.clientX;
	var y = event.clientY;

	var rect = canvas[0].getBoundingClientRect();
	x -= rect.left;
	y -= rect.top;

	var mouseX = x;
	var mouseY = y;

	setPixels(mouseX, mouseY);
});

canvas.click(function(event){
	var x = event.clientX;
	var y = event.clientY;

	var rect = canvas[0].getBoundingClientRect();
	x -= rect.left;
	y -= rect.top;

	var pixelCurrent = ctx.getImageData(x, y, 1, 1);
	var data = pixelCurrent.data;
	//si on clique sur la zone verte
	if(data[1] == 255){
		arcStart = undefined;
		//chrono
		var chrono = new timer();	
		chrono.run();
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
		if(data[2] != 255){
			newPix.color = "#ff0000";
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

function pixel(x, y){
	this.x = x;
	this.y = y;
	this.size = 2;
	this.color = "#ffffff"; //couleur du pixel quand il est dans le chemin
	this.draw = function(){
		ctx.fillStyle = this.color;
		ctx.beginPath();
		ctx.rect(this.x - this.size, this.y - this.size, this.size, this.size);
		ctx.fill();
	}
}

function arc(pixel, radius, start, end, color='#0000ff'){
	this.center = pixel;
	this.radius = radius;
	this.start = start;
	this.end = end;		
	this.color = color;
	this.draw = function(){
		ctx.strokeStyle = this.color;
		ctx.beginPath();
		ctx.lineWidth = 80;
		ctx.arc(this.center.x, this.center.y, this.radius, this.start, this.end, true);
		ctx.stroke();
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

function start(){
	$('#chronotime').css('visibility', 'visible');
	backPixels = [];
	draw();
}

function draw(){
	ctx.clearRect(0, 0, canvas[0].width, canvas[0].height); 

	//dessins
	arc1.draw();
	if(arcStart !== undefined){
		arcStart.draw();
	}
	drawBackPixels();

	window.requestAnimationFrame(draw); //on appelle run en boucle
}

function timer(){
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
		this.previousChrono = this.diff;
		clearTimeout(timerID);
	}
	this.resume = function(){
		this.dateStartChrono = new Date() - diff;
		this.dateStartChrono = new Date(this.dateStartChrono);
		doChrono();
	}
}
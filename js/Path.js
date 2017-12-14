function Path(){
	//crée l'arc vert de départ (constante pour tous les chemins)
	var arcStart = new Arc(1000, undefined, colorStart);
	arcStart.center = {x:50, y:canvas[0].height / 2 + arcStart.radius};
	arcStart.start = -Math.PI/2;
	arcStart.end = arcStart.start - Math.PI / 100;
	arcStart.isTrigonometrique = true;
	this.arcs = [arcStart];

	//initialise un arc de fin (qui changera à chaque ajout d'arc dans le chemin)
	var arcEnd = new Arc(1000, undefined, colorEnd);
	arcEnd.center = arcStart.center;
	arcEnd.start = arcStart.start + Math.PI/100;
	arcEnd.end = arcStart.start;
	arcEnd.isTrigonometrique = true;
	this.arcs.push(arcEnd);

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

		var angle = Math.atan((yCenter - yStart) / (xCenter - xStart));
		var xNewCenter = xStart + arc.radius * Math.cos(angle);
		var yNewCenter = yStart + arc.radius * Math.sin(angle);
		var newCenter = {x:xNewCenter, y:yNewCenter};

		arc.center = newCenter;
		console.log(angle);
		console.log("Il y a " + this.arcs.length + " arcs + celui qu'on rajoute + l'arrivée");
		if(angle < 0 ){
			angle = Math.abs(angle);
			if(this.arcs.length % 2 == 1){
				//on va dire OK
				arc.end = Math.PI - angle;
				arc.start = arc.end - arc.angle;
				arc.isTrigonometrique = false;
			}else{
				arc.end =  Math.PI - angle;
				arc.start = arc.end + arc.angle;
				arc.isTrigonometrique = true;
			}
		}else{
			if(this.arcs.length % 2 == 1){
				arc.end = Math.PI + angle;
				arc.start = arc.end - arc.angle;
				arc.isTrigonometrique = false;
			}else{
				arc.end = Math.PI + angle;
				arc.start = arc.end + arc.angle;
				arc.isTrigonometrique = true;
			}
		}
		this.arcs.push(arc);
		//recalcule le dernier arc et l'ajoute
		this.addArcEnd();
	}
	this.setArc = function(index, radius, angle){
		this.arcs[index].radius = radius;
		this.arcs[index].angle = angle;

		if(index % 2 == 1){
			this.arcs[index].start = this.arcs[index].end - this.arcs[index].angle;
		}else{
			this.arcs[index].start = this.arcs[index].end + this.arcs[index].angle;
		}

		//retire le dernier élémentpuis le rajoute (recalcule)
		this.arcs.splice(this.arcs.length - 1, 1);
		this.addArcEnd();

	}
	//fonction qui calcul un arc de fin selon le chemin courant et l'insert
	this.addArcEnd = function(){
		var lastCurrentArc = this.arcs[this.arcs.length - 1];
		var newEnd = new Arc(lastCurrentArc.radius, undefined, colorEnd);
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
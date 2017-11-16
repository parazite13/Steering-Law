$(window).click(function(event){
	if(event.pageY > 56 + $('#navbarHeader').height()){
		$('#navbarHeader').collapse('hide');
	}
});

$(window).click(function(evt){
	if(evt.target.id == "status")
		return;
	if(evt.target.className == "popover-content")
		return;
	
	$('#buttonGraph').popover('dispose');
});

$(document).ready(function(){
	if($('body').height() < $(window).height()){
		$('.navbar-fixed-bottom').addClass('fixed-bottom');
	}
});
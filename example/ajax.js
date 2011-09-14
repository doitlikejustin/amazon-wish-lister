$(document).ready(function(){

	var DEBUG = false;
	var loading = $("#loading");
	var content = $("#content");
	var amazonID = "";
	
	//show the loading bar
	showLoading();
	
	var getFromURL = window.location.pathname.slice(window.location.pathname.indexOf('/') + 1);
	
	var regex = /[\w]+\W/ig;
	var amazonID = getFromURL.replace(regex,"");
	
	if(DEBUG) { alert(amazonID); }
		
	//load selected section
	content.slideUp();
	content.load("list.php?id=" + amazonID, hideLoading);
	content.slideDown();

	//show loading bar
	function showLoading(){
		loading.css({visibility:"visible", opacity:"1", display:"block"});
	}
	
	//hide loading bar
	function hideLoading(){
		loading.slideUp().fadeTo(1000, 0);
		content.css({visibility:"visible", display:"block"});
	};
});
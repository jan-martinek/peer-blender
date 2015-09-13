"use strict";

var PeerBlender = {
	baseUri: '',
	
	init: function() {
		$.nette.init();
		
		this.ThirdParty.init();
	},
	
	ThirdParty: {
		init: function() {
			$(document).foundation();
        
        	$(".tablesorter").tablesorter(); 
		}	
	},
}

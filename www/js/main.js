"use strict";

var PeerBlender = {
	baseUri: '',
	
	init: function() {
		$.nette.init();
		
		this.ThirdParty.init();
		
		$('a[href^=http]').attr('target', '_blank');
	},
	
	ThirdParty: {
		init: function() {
			$(document).foundation();
        
        	$(".tablesorter").tablesorter(); 
		}	
	},
}

"use strict";

$.nette.ext('resizableTextareaRefresh', {
    complete: function () {
        PeerBlender.Chat.resizeTextarea();
        autosize($('#chat textarea'));
    }
});

var PeerBlender = {
	baseUri: '',
	
	init: function() {
		$.nette.init();
		
		this.ThirdParty.init();
		this.Chat.init();
		
		$('a[href^=http]').attr('target', '_blank');
	},
	
	ThirdParty: {
		init: function() {
			$(document).foundation();
		
			$(".tablesorter").tablesorter(); 
		}	
	},
	
	Chat: {		
		init: function() {			
			if ($('#chat').length) {
				autosize($('#chat textarea'));
				$('#chat').show();
				
				$(document).on('click', '#chat .header', function() {
					if ($("#chat").height() > 400) {
						$("#chat .chatContent").hide();
						$("#chat").css({height: '30px', width: '100px'});
					} else {
						$("#chat .chatContent").show();
						$("#chat").css({height: '500px', width: '400px'});
					}
					
				});
				
				document.querySelector('#chat textarea').addEventListener('autosize:resized', function() {
					PeerBlender.Chat.resizeTextarea();
				});
				
				setTimeout(function(){PeerBlender.Chat.refreshChat()}, 5*1000);
			}
		},
		
		resizeTextarea: function() {	
			var messages = $('#chat .messages');
			messages.height(430 - $('#chat textarea').height());
			$('#chat .messages').scrollTop(document.querySelector('#chat .messages').scrollHeight);
		},
			
		refreshChat: function() {
			$.nette.ajax({
				url: window.location.pathname + '?do=chatRenderer-refreshChat',
				complete: function (payload) {
					PeerBlender.Chat.resizeTextarea();
					setTimeout(function(){PeerBlender.Chat.refreshChat()}, 5*1000);
				}
			});
		}
	}
}
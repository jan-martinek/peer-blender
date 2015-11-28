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
		this.Highlighting.init();
		this.Chat.init();
		
		$('a[href^=http]').attr('target', '_blank');
	},
	
	ThirdParty: {
		init: function() {
			$(document).foundation();
		
			$(".tablesorter").tablesorter(); 
		}	
	},
	
	Highlighting: {	
		editors: [],
		
		init: function() {
			$('button.answerPreview').click(function(e) {
				var editorCount = PeerBlender.Highlighting.editors.length;
				for (var i = 0; i < editorCount; i++) {
				    PeerBlender.Highlighting.editors[i].save();
				}
				
				var answer = $(this).closest('.assignmentQuestion').find('textarea').val();
				$('#previewForm textarea[name="answer"]').text(answer);
				$('#previewForm')[0].submit();
				
				e.preventDefault();
			});
			
			
			$('textarea').each(function() {
				var textarea = $(this);
				var className = textarea.attr('class');
				if (!className) {
					return;
				}
				var mode = className.match(/highlight-([a-z]+)/)[1];
				var gutters = [];
    			var lint = false;
			
				switch (mode) {
					case 'code':
					case 'markdown':
					case 'css':
					case 'sql':
					case 'xml':
						var highlightingMode = mode;
						break;
					case 'javascript':
						var highlightingMode = mode;
						lint = true;
						gutters = ["CodeMirror-lint-markers"];
						break;
					case 'html':
						var highlightingMode = 'htmlmixed';
						break;
					default:
						return;
				}
				
				var myCodeMirror = CodeMirror.fromTextArea(
					$(this)[0], {
						lineNumbers: true, 
						mode: highlightingMode,
						lint: true,
						gutters: ["CodeMirror-lint-markers"],
						viewportMargin: Infinity,
						readOnly: textarea.hasClass('readonly') ? 'nocursor' : false
					}
				);
				
				PeerBlender.Highlighting.editors.push(myCodeMirror);
			});
		}	
	},
	
	Chat: {		
		refresh: null,
		
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
				
				PeerBlender.Chat.refresh = setTimeout(function(){PeerBlender.Chat.refreshChat()}, 3*1000);
			}
		},
		
		resizeTextarea: function() {	
			var messages = $('#chat .messages');
			messages.height(430 - $('#chat textarea').height());
			$('#chat .messages').scrollTop(document.querySelector('#chat .messages').scrollHeight);
		},
			
		refreshChat: function(courseId) {
			$.nette.ajax({
				url: '/generated/chat/' + 2 + '.html',
				complete: function (payload) {
					var messages = $('#chat .messages');
					messages.html(payload.responseText);
					PeerBlender.Chat.resizeTextarea();
					PeerBlender.Chat.refresh = setTimeout(function(courseId){PeerBlender.Chat.refreshChat(2)}, 3*1000);
				}
			});
		}
	}
}
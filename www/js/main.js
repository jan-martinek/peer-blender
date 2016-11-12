'use strict';

$.nette.ext('resizableTextareaRefresh', {
    complete: function () {
        PeerBlender.Chat.resizeTextarea();
        autosize($('#chat textarea'));
    }
});

var PeerBlender = {
	baseUri: '',
	
	init: function() {
		$.nette.ext('status', {
			start: function() {
				$('#quick-save-button i.fa').attr('class', 'fa');
				$('#quick-save-button i.fa').addClass('fa-spinner fa-spin');
				
			},
			success: function() {
				$('#quick-save-button i.fa').attr('class', 'fa');
				$('#quick-save-button i.fa').addClass('fa-check');
				setTimeout(function() { 
					$('#quick-save-button i.fa').removeClass('fa-check'); 
					$('#quick-save-button i.fa').addClass('fa-save');
				}, 2000);
			},
			error: function(xhr, status, error) {
				//var err = eval("(" +  + ")");
				$('#quick-save-button i.fa').attr('class', 'fa');
				$('#quick-save-button i.fa').addClass('fa-exclamation-triangle');
			}
		});
		$.nette.ext('flash', {
			complete: function () {
				$('.flashMessages').animate({
					opacity: 1.0
				}, 4000).fadeOut(700);
			}
		});
		$.nette.init();

		
		this.ThirdParty.init();
		this.Highlighting.init();
		//this.Chat.init();
		this.Review.init();
		
		$('a[href^=http]').attr('target', '_blank');
	},
	
	ThirdParty: {
		init: function() {
			$(document).foundation();
		
			$(".tablesorter").tablesorter(); 
		}	
	},
	
	Review: {
		init: function() {
			var reviewOngoing = ($('.assignmentQuestion input:radio').length > 0);
			if (reviewOngoing) {
				this.updateScore();
				$(document).on('change', '.assignmentQuestion input:radio, .assignmentQuestion input:checkbox', this.updateScore);	
				$(document).on('change', '#frm-reviewForm-solutionIsComplete', this.updateScore);	
			}
			
		},
		
		updateScore: function() {
			var allAnswered = true;
			$('.assignmentQuestion input:radio').each(function(){
			    if($(':radio[name="'+$(this).attr('name')+'"]:checked').length == 0)
			    {
			        allAnswered = false;
			    }
			});
			
			$('#totalScore span').text(allAnswered ? PeerBlender.Review.calculateScore() : '—');
		},
		
		calculateScore: function() {
			var values = [];
			var solutionIsCompleteMultiplier = 1;
			
			$.each($('.assignmentQuestion.model-ontology-rubric input:radio').serializeArray(), function(i, rubric) {
				values.push(rubric.value);
			});
			
			$.each($('.model-ontology-checklist'), function(i, checklist) {
				var labels = $(checklist).find('label');
				var totalWeight = 0;
				var totalScore = 0;
				var maxScore = 3;
				$.each($(labels), function(i, label) {
					if ($(label).find('.weight').length) {
						var weight = parseInt($(label).find('.weight').text());
						totalWeight += weight;
						
						var checked = $(label).find('input').is(':checked');
						if (checked) {
							totalScore += weight;
						}
					}
				});
				var score = maxScore/totalWeight*totalScore;
				
				$(checklist).find('.calcWeightedScore').remove();
				$(checklist).append('<p class="calcWeightedScore"><b>'+score+'</b></p>');
				values.push(score);
			});
			
			if ($('#frm-reviewForm-solutionIsComplete:checked').length == 0) {
				solutionIsCompleteMultiplier = 0.5;
			}
			
			if (values.length == 0) {
				return '—';
			} else {
				return Math.round(this.average(values)*solutionIsCompleteMultiplier*100)/100;
			}
		},
		
		average: function(arr) {
			var sum = 0;
			for (var i = 0; i < arr.length; i++) {
			    sum += parseInt(arr[i], 10);
			}
			return sum/arr.length;
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
			
			$('button.showPrefill').click(function(e) {
				var prefillViewer = $(this).closest('.assignmentQuestion').find('.prefill');
				prefillViewer.toggle('fast');
				if (!prefillViewer.hasClass('ready')) {
					PeerBlender.Highlighting.initCodeMirror(prefillViewer.find('textarea'));
					prefillViewer.addClass('ready');
				}
				
				e.preventDefault();
			});
			
			
			$('.assignmentQuestion > textarea').each(function() {
				PeerBlender.Highlighting.initCodeMirror($(this));
			});
			
			$('#quick-save-button').click(function(e) {
				for (var i = 0; i < PeerBlender.Highlighting.editors.length; i++) {
					PeerBlender.Highlighting.editors[i].save();
				}
			});
		},
		
		initCodeMirror: function(textarea) {
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
				textarea[0], {
					lineNumbers: true, 
					lineWrapping: true,
					mode: highlightingMode,
					lint: true,
					gutters: ["CodeMirror-lint-markers"],
					viewportMargin: Infinity,
					readOnly: textarea.hasClass('readonly') ? 'true' : false
				}
			);
			
			PeerBlender.Highlighting.editors.push(myCodeMirror);
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
'use strict';

$.nette.ext('resizableTextareaRefresh', {
	complete: function () {
		PeerBlender.Chat.resizeTextarea();
		autosize($('#chat textarea'));
	}
});

$.nette.ext('forms', false);
$.nette.ext('forms', {
	init: function () {
		var snippets;
		if (!window.Nette || !(snippets = this.ext('snippets'))) return;

		snippets.after(function ($el) {
			$el.find('form').each(function() {
				window.Nette.initForm(this);
			});
			
			PeerBlender.Highlighting.refreshIframes();
		});
	},
	prepare: function (settings) {
		var analyze = settings.nette;
		if (!analyze || !analyze.form) return;
		var e = analyze.e;
		var originalData = settings.data || {};
		var data = {};

		if (analyze.isSubmit) {
			data[analyze.el.attr('name')] = analyze.el.val() || '';
		} else if (analyze.isImage) {
			var offset = analyze.el.offset();
			var name = analyze.el.attr('name');
			var dataOffset = [ Math.max(0, e.pageX - offset.left), Math.max(0, e.pageY - offset.top) ];

			if (name.indexOf('[', 0) !== -1) { // inside a container
				data[name] = dataOffset;
			} else {
				data[name + '.x'] = dataOffset[0];
				data[name + '.y'] = dataOffset[1];
			}
		}
		
		// https://developer.mozilla.org/en-US/docs/Web/Guide/Using_FormData_Objects#Sending_files_using_a_FormData_object
		if (analyze.form.attr('method').toLowerCase() === 'post' && 'FormData' in window) {
			var formData = new FormData(analyze.form[0]);
			for (var i in data) {
				formData.append(i, data[i]);
			}

			if (typeof originalData !== 'string') {
				for (var i in originalData) {
					formData.append(i, originalData[i]);
				}
			}

			settings.data = formData;
			settings.processData = false;
			settings.contentType = false;
		} else {
			if (typeof originalData !== 'string') {
				originalData = $.param(originalData);
			}
			data = $.param(data);
			settings.data = analyze.form.serialize() + (data ? '&' + data : '') + '&' + originalData;
		}
	}
});


var PeerBlender = {
	baseUri: '',
	outdatedIframes: [],
	
	init: function() {
		$.nette.ext('status', {
			start: function() {
				$('#quick-save-button i.fa').attr('class', 'fa');
				$('#quick-save-button i.fa').addClass('fa-spinner fa-spin');
				
			},
			complete: function() {
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
			var reviewOngoing = ($('.assignmentQuestion input:radio').length > 0 || $('.assignmentQuestion input:checkbox').length > 0);
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
						var weight = parseFloat($(label).find('.weight').text());
						totalWeight += weight;
						
						var checked = $(label).find('input').is(':checked');
						if (checked) {
							totalScore += weight;
						}
					}
				});
				var score = maxScore/totalWeight*totalScore;
				
				$(checklist).find('.calcWeightedScore').remove();
				$(checklist).append('<p class="calcWeightedScore"><b>' + Math.round(score*100)/100 + '</b></p>');
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
				sum += parseFloat(arr[i], 10);
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

				var hlType = $(this).attr('type');
				if (hlType == 'highlight-javascript') {
					var answer = $(this).closest('.assignmentQuestion').find('textarea').val();
					$('#previewForm textarea[name="answer"]').text(answer);
					$('#previewForm')[0].submit();	
				} else if (/^highlight-turtle/.test(hlType)) {
					var iframe = $(this).closest('.assignmentQuestion').find('iframe').first();
					var answerId = iframe.data('answerId');
					var location = iframe.attr('src');
					var newLocation = '';
					switch (hlType) {
						case 'highlight-turtle':
							newLocation = '/code-preview/turtle/' + answerId;
							break;
						case 'highlight-turtle-na':
							newLocation = '/code-preview/turtle/' + answerId + '?animated=0';
							break;
						default:
							newLocation = location;
					}
					
					PeerBlender.outdatedIframes.push({
						iframe: iframe,
						location: newLocation
					});
					
					
					var quickSaveButton = $('#quick-save-button');
					if (quickSaveButton.length) {
						setTimeout(function() {
							$('#quick-save-button').trigger('click');	
						}, 1000);	
					} else {
						PeerBlender.Highlighting.refreshIframes();
					}
				}
				
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
			
			
			$('.assignmentQuestion textarea[class*="highlight-"]').each(function() {
				PeerBlender.Highlighting.initCodeMirror($(this));
			});
			
			$('#quick-save-button').click(function(e) {
				for (var i = 0; i < PeerBlender.Highlighting.editors.length; i++) {
					PeerBlender.Highlighting.editors[i].save();
				}
			});
			
			PeerBlender.Highlighting.initStickyIframes();
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
		},
		
		initStickyIframes: function() {
			/*window.addEventListener('scroll', function() {
				var turtlePreviewHeight = 400;
				var previews = document.querySelectorAll('iframe.turtle-preview');
				
				for (var i = previews.length - 1; i >= 0; i--) {
					var iframe = previews[i];
					var codeColumn = document.querySelector('iframe.turtle-preview').parentNode.previousElementSibling.getBoundingClientRect();
					var height = codeColumn.height - 183;
					var top = codeColumn.top;
					if (codeColumn.height > turtlePreviewHeight + 100 && top < 0) {
						var translate = -top > turtlePreviewHeight ? turtlePreviewHeight : -top;
						iframe.style.transform = 'translate(0px, ' + (translate) + 'px)';
					} else {
						iframe.style.position = 'static';
						iframe.style.top = '';
						iframe.style.transform = '';
						iframe.style.width = '100%';
					}
				}
			});*/
		},
		
		refreshIframes: function() {
			for (var i = PeerBlender.outdatedIframes.length - 1; i >= 0; i--) {
				var item = PeerBlender.outdatedIframes[i];
				item.iframe.attr('src', item.location);
				
				PeerBlender.outdatedIframes.splice(i, 1);
			}
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
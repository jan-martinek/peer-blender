'use strict';

$.nette.ext('forms', false);
$.nette.ext('forms', {
	init: function () {
		var snippets;
		if (!window.Nette || !(snippets = this.ext('snippets'))) return;

		snippets.after(function ($el) {
			$el.find('form').each(function() {
				window.Nette.initForm(this);
			});
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
	}
}

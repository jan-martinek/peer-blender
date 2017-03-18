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
	playgrounds: new Playgrounds,
	outdatedIframes: [],
	
	init: function() {
		$.nette.ext('flash', {
			complete: function () {
				$('.flashMessages').animate({
					opacity: 1.0
				}, 4000).fadeOut(700);
			}
		});
		$.nette.init();

		this.playgrounds.init();
		this.ThirdParty.init();
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
		autosave: null,
		
		init: function() {
			this.setupSaveButton();
			this.setupScoreUpdate();
		},
		
		setupScoreUpdate: function() {
			var reviewOngoing = (document.querySelectorAll('#totalScore').length > 0);
			
			if (reviewOngoing) {
				this.updateScore();
				
				var reviewForm = document.querySelector('#frm-reviewForm');
				if (reviewForm) {
					reviewForm.addEventListener('change', function(e) {
						if (e.target.matches('.rubric input') || e.target.matches('#frm-reviewForm-solutionIsComplete')) {
							this.updateScore();
						}
					}.bind(this));	
				}
			}
		},
		
		setupSaveButton: function() {
			var qs = document.querySelector('#quick-save-button');
			
			if (qs) { 
				this.setupAutoSave(qs);
				
				qs.addEventListener('click', this.save.bind(this));
				
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
						$('#quick-save-button i.fa').attr('class', 'fa');
						$('#quick-save-button i.fa').addClass('fa-exclamation-triangle');
					}
				});
			}	
		},
		
		setupAutoSave: function(qs) {
			var autosavebox = document.createElement('LI');
			autosavebox.innerHTML = '<a href="#"><input type="checkbox" id="autosaveOn"> ukládat automaticky</a>';
			qs.parentNode.parentNode.insertBefore(autosavebox, qs.parentNode.nextSibling);
			
			if (localStorage.getItem('autosave') !== false) {
				document.getElementById('autosaveOn').checked = true;	
			}
			
			autosavebox.addEventListener('click', function(e) {
				var checkbox = document.getElementById('autosaveOn');
				checkbox.checked = !checkbox.checked;
				localStorage.setItem("autosave", checkbox.checked);
				e.preventDefault();
			});
			
			window.setInterval(this.autosave.bind(this), 90 * 1000);
		},
		
		autosave: function() {
			if (document.getElementById('autosaveOn').checked) {
				this.save();
			}
		},
		
		save: function(e) {
			PeerBlender.playgrounds.save();
			document.querySelector('#frm-assignmentForm [type=submit]').click();
			
			if (e) {
				e.preventDefault();	
			}
		},
		
		updateScore: function() {
			var score = this.checkIfAllRadiosAnswered() ? this.calculateScore() : '—';
			document.querySelector('#totalScore span').innerHTML = score;
		},
		
		checkIfAllRadiosAnswered: function() {
			var allAnswered = true;
			
			document.querySelectorAll('.rubric input[type=radio]').forEach(function(radio) {
				if (!allAnswered) return;
				
				var sibs = radio.parentElement.parentElement.querySelectorAll('[type=radio]');
				var checkedCount = 0;
				
				for (var i = 0; i < sibs.length; i++) {
				    if (sibs[i].checked == true) {
				    	checkedCount++;
				    }
				}
				
				if (checkedCount == 0) {
					allAnswered = false;
				}
			});
			
			return allAnswered;
		},
		
		calculateScore: function() {
			var values = [];
			var solutionIsCompleteMultiplier = 1;
			
			$.each($('.nette-forms-controls-radiolist input:radio').serializeArray(), function(i, rubric) {
				values.push(rubric.value);
			});
			
			$.each($('.nette-forms-controls-checkboxlist'), function(i, checklist) {
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
				$(checklist).append('<p class="calcWeightedScore">☞ Hodnocení úkolu: <b>' + Math.round(score*100)/100 + '</b> b.</p>'); // translation needed
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

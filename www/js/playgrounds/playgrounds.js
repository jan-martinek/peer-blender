var Playgrounds = function() {
	this.instances = [];
	
	this.init = function() {
		document.querySelectorAll(".playground").forEach(function(el) {
			var p = new Playground(el);
			p.init();
			this.instances.push(p); 
		}.bind(this));
		this.bindToSaveButton();
	}
	
	this.bindToSaveButton = function() {
		var qs = document.querySelector('#quick-save-button');
		if (qs) {
			qs.addEventListener('click', function(e) {
				this.save();
				e.preventDefault();
			}.bind(this));	
		}
		
	}
	
	this.save = function() {
		this.instances.forEach(function(i) {
			i.save();	
		});
	}
}

var Playground = function(el) {
	this.el = el;
	this.box = null;
	this.toy = null;
	
	this.answerId = el.dataset.answer;
	this.answered = el.dataset.answered == 1 ? true : false;
	this.mode = el.dataset.mode;
	this.toyName = el.dataset.toy;
	
	this.init = function() {
		this.box = document.createElement("DIV");
		this.box.classList.add("box");
		this.el.appendChild(this.box);
		
		this.fetchToy();
		this.addPrefillAccess();
		this.indicateUntouched();
	}
	
	this.fetchToy = function() {
		switch(this.toyName) {
			case 'code':
			case 'markdown':
			case 'css':
			case 'sql':
			case 'xml':
				this.toy = new HighlightingToy(this, this.toyName);
				break;
			case 'html':
				this.toy = new HighlightingToy(this, 'htmlmixed');
				break;
			case 'javascript':
				this.toy = new JsToy(this);
				break;
			case 'turtle':
				this.toy = new TurtleToy(this);
				break;
			case 'p5js':
				this.toy = new P5Toy(this);
				break;
			case 'plaintext':
				this.toy = new PlaintextToy(this);
				break;
			default:
				console.log('Toy "'+this.toyName+'" not found . Using plain.');
				return;
		}
	}
	
	this.addPrefillAccess = function() {
		var prefill = this.el.querySelector('.prefill');
		
		if (prefill && prefill.querySelector('pre').innerHTML != '') {
			var showPrefill = document.createElement("BUTTON");
			showPrefill.classList.add('showPrefill');
			showPrefill.classList.add('secondary');
			showPrefill.innerHTML = '{ }  {_messages.solution.viewPrefill}';
						
			this.el.insertBefore(showPrefill, prefill);
			
			showPrefill.addEventListener('click', function(e) {
				var el = this.el.querySelector('.prefill');
				if (el.style.display == 'block') {
					el.style.display = 'none';
					e.target.innerHTML = '{ }  {_messages.solution.viewPrefill}';
				} else {
					el.style.display = 'block';
					e.target.innerHTML = '{ }  {_messages.solution.hidePrefill}';
				}
				
				e.preventDefault();
			}.bind(this));
		}
	}
	
	this.indicateUntouched = function() {
		if (this.mode == 'review' && !this.answered) {
			var untouched = document.createElement("DIV");
			untouched.classList.add("panel");
			untouched.classList.add("callout");
			untouched.innerHTML = '<p style="color: #aaa">{_messages.unit.notAnswered}</p>';
			
			this.el.insertBefore(untouched, this.box);
		}
	}
	
	this.post = function(path, params, target) {
		method = "post";

		var form = document.createElement("form");
		form.setAttribute("method", method);
		form.setAttribute("action", path);
		form.setAttribute("target", target);

		for(var key in params) {
			if(params.hasOwnProperty(key)) {
				var hiddenField = document.createElement("input");
				hiddenField.setAttribute("type", "hidden");
				hiddenField.setAttribute("name", key);
				hiddenField.setAttribute("value", params[key]);

				form.appendChild(hiddenField);
			 }
		}
		document.body.appendChild(form);
		form.submit();
	}
	
	this.save = function() {
		this.toy.save();
	}
}

var P5Toy = function(playground) {
	this.playground = playground;
	this.source = playground.el.querySelector('textarea');
	this.source.style.display = 'none';
	
	this.source.style.display = 'none';
	var answered = this.playground.answerId > 0;
	var previewPlaceholder = answered ? 'turtle-blank' : 'na';
	
	this.playground.box.innerHTML = 
		'<div class="row">' +
			'<div class="editor-wrapper columns large-6"></div>' +
			'<div class="columns large-6">' +
				'<iframe class="p5js-preview" src="/code-preview/'+previewPlaceholder+'"></iframe>' +
				 (answered ? '<button>&#9654; {_messages.solution.runCode}</button>' : '') +
			'</div>' +
		'</div>';
	
	this.editor = CodeMirror(
		this.playground.box.querySelector('.editor-wrapper'), 
		{
			value: this.source.value,
			lineNumbers: true, 
			lineWrapping: true,
			mode: 'javascript',
			viewportMargin: Infinity,
			readOnly: this.playground.mode == 'review' ? true : false
		}
	);
	
	if (answered) {
		this.playground.box.querySelector('button').addEventListener('click', function(e) {
			this.playground.box.querySelector('iframe').setAttribute('src', '/code-preview/p5js/' + this.playground.answerId);
			e.preventDefault();
		}.bind(this));	
	}
	
	this.save = function() {
		this.source.value = this.editor.getValue();
	}
}

var TurtleToy = function(playground) {
	this.playground = playground;
	this.source = playground.el.querySelector('textarea');
	this.source.style.display = 'none';
	
	var answered = this.playground.answerId > 0;
	var previewPlaceholder = answered ? 'turtle-blank' : 'na';
	
	this.playground.box.innerHTML = 
		'<div class="row">' +
			'<div class="editor-wrapper columns large-6"></div>' +
			'<div class="columns large-6">' +
				'<iframe class="p5js-preview" src="/code-preview/'+previewPlaceholder+'"></iframe>' +
				 (answered ? '<button>&#9654; {_messages.solution.runCode}</button> <button>&#9654; {_messages.solution.runCodeWithoutAnimation}</button>' : '') +
			'</div>' +
		'</div>';
	
	this.editor = CodeMirror(
		this.playground.box.querySelector('.editor-wrapper'), 
		{
			value: this.source.value,
			lineNumbers: true, 
			lineWrapping: true,
			mode: 'javascript',
			viewportMargin: Infinity,
			readOnly: this.playground.mode == 'review' ? true : false
		}
	);
		
	if (answered) {
		this.playground.box.querySelectorAll('button')[0].addEventListener('click', function(e) {
			this.playground.box.querySelector('iframe').setAttribute('src', '/code-preview/turtle/' + this.playground.answerId);
			e.preventDefault();
		}.bind(this));
		
		this.playground.box.querySelectorAll('button')[1].addEventListener('click', function(e) {
			this.playground.box.querySelector('iframe').setAttribute('src', '/code-preview/turtle/' + this.playground.answerId + '?animated=0');
			e.preventDefault();
		}.bind(this));
	}
}


var HighlightingToy = function(playground, lang) {
	this.playground = playground;
	this.source = playground.el.querySelector('textarea');
	this.source.style.display = 'none';
	
	this.lang = lang;
	this.editor = CodeMirror(this.playground.box, {
		value: this.source.value,
		lineNumbers: true, 
		lineWrapping: true,
		mode: this.lang,
		viewportMargin: Infinity,
		readOnly: this.playground.mode == 'review' ? true : false
	});
	
	this.save = function() {
		this.source.value = this.editor.getValue();
	}
}

var PlaintextToy = function(playground) {
	this.playground = playground;
	this.source = playground.el.querySelector('textarea');
	this.source.classList.add('plaintext');
	autosize(this.source);
	
	if (playground.mode == 'review') {
		this.source.setAttribute('disabled', 'true');
	}
	
	this.save = function() {}
}

var JsToy = function(playground) {
	this.playground = playground;
	this.source = playground.el.querySelector('textarea');
	this.source.style.display = 'none';
	
	this.previewId = Math.ceil(Math.random()*Math.pow(10, 10));
	
	this.playground.box.innerHTML = 
		'<div class="editor-wrapper"></div>' +
		'<button>&#9654; {_messages.solution.runCode}</button>' +
		'<iframe style="display: none" id="preview_iframe'+this.previewId+'" name="preview_iframe'+this.previewId+'"></iframe>';
	
	this.editor = CodeMirror(
		this.playground.box.querySelector('.editor-wrapper'), 
		{
			value: this.source.value,
			lineNumbers: true, 
			lineWrapping: true,
			mode: 'javascript',
			lint: true,
			gutters: ["CodeMirror-lint-markers"],
			viewportMargin: Infinity,
			readOnly: this.playground.mode == 'review' ? true : false
		}
	);
	
	this.playground.box.querySelector('button').addEventListener('click', function(e) {
		this.playground.post('/code-preview/hidden', {'answer': this.editor.getValue()}, 'preview_iframe'+this.previewId);
		e.preventDefault();
	}.bind(this));
	
	this.save = function() {
		this.source.value = this.editor.getValue();
	}
}

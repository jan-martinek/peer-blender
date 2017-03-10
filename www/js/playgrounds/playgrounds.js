var Playgrounds = function() {
	this.instances = [];
	
	this.init = function() {
		document.querySelectorAll(".playground").forEach(function(el) {
			var p = new Playground(el);
			p.init();
			this.instances.push(p); 
		}.bind(this));
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
		this.box.classList.add("columns");
		this.el.appendChild(this.box);
		
		this.fetchToy();
		this.addPrefillAccess();
		this.indicateUntouched();
	}
	
	this.fetchToy = function() {
		switch(this.toyName) {
			case 'code':
			case 'css':
			case 'sql':
			case 'xml':
				this.toy = new HighlightingToy(this, this.toyName);
				break;
			case 'livecss':
				this.toy = new LiveCssToy(this);
				break;
			case 'html':
				this.toy = new HighlightingToy(this, 'htmlmixed');
				break;
			case 'markdown':
				this.toy = new MarkdownToy(this);
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
			showPrefill.innerHTML = '{ }  Zobrazit předvyplněný kód'; //TRANSLATE _messages.solution.viewPrefill
			this.box.insertBefore(showPrefill, this.box.childNodes[0]);
			
			var hidePrefill = document.createElement("BUTTON");
			hidePrefill.classList.add('secondary');
			hidePrefill.innerHTML = '✕';
			prefill.appendChild(hidePrefill);
			
			showPrefill.addEventListener('click', function(e) {
				var el = this.el.querySelector('.prefill');
				el.style.display = 'block';
				
				e.target.style.display = 'none';
				
				e.preventDefault();
			}.bind(this));
			
			hidePrefill.addEventListener('click', function(e) {
				var el = this.el.querySelector('.prefill');
				el.style.display = 'none';
				
				var btn = this.el.querySelector('.showPrefill');
				btn.style.display = 'inline-block';
				
				e.preventDefault();
			}.bind(this));
		}
	}
	
	this.indicateUntouched = function() {
		if (this.mode == 'review' && !this.answered) {
			var untouched = document.createElement("DIV");
			untouched.classList.add("panel");
			untouched.classList.add("callout");
			untouched.innerHTML = '<p style="color: #aaa">U tohoto úkolu nebyly uloženy žádné změny.</p>'; // _messages.unit.notAnswered
			
			this.box.insertBefore(untouched, this.box.childNodes[0]);
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
		if (this.toy) {
			this.toy.save();	
		}
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
				 (answered ? '<button>&#9654; Spustit kód</button>' : '') + // {_messages.solution.runCode}
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
				 (answered ? '<button>&#9654; Spustit kód</button> <button>&#9654; Spustit kód bez animace</button>' : '') + // _messages.solution.runCode, _messages.solution.runCodeWithoutAnimation
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
		this.source.setAttribute('readonly', 'true');
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
		'<button>&#9654; Spustit kód</button>' + // {_messages.solution.runCode}
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

var LiveCssToy = function(playground) {
	this.playground = playground;
	this.playground.el.classList.add('wide');
	
	this.source = playground.el.querySelector('textarea');
	this.source.style.display = 'none';
	
	this.separator = '\n\n###css###\n\n';
	this.previewId = 'css-preview-' + Math.round(Math.random()*1000000);
	
	this.playground.box.innerHTML = 
		'<div class="row">' +
			'<div class="columns large-4">' + 
				'<p><span class="label">HTML</span></p>' + // Preview
				'<div class="html-editor-wrapper"></div>' + 
			'</div>' +
			'<div class="columns large-4">' + 
				'<p><span class="label">CSS</span></p>' + // Preview
				'<div class="css-editor-wrapper"></div>' + 
			'</div>' +
			'<div class="columns large-4">' +
				'<p><span class="label">Náhled</span></p>' + // Preview
				'<div class="css-preview"><style></style><div class="css-preview-wrapper"></div></div>' +
			'</div>' +
			'<div class="columns stats"></div>' +
		'</div>';
	
	
	this.preview = this.playground.box.querySelector('.css-preview');
	this.preview.setAttribute('id', this.previewId);
	this.previewStyle = this.preview.querySelector('style');
	this.previewHtml = this.preview.querySelector('div');
	
	
	this.initEditors = function() {
		var html, css;
		
		if (this.source.value.match(this.separator)) {
			var source = this.source.value.split(this.separator);
			html = source[0];
			css = source[1];
		} else {
			html = this.source.value;
			css = '';
		}
			
		this.htmlEditor = CodeMirror(
			this.playground.box.querySelector('.html-editor-wrapper'), 
			{
				value: html,
				lineNumbers: true, 
				lineWrapping: true,
				mode: 'htmlmixed',
				viewportMargin: Infinity,
				readOnly: this.playground.mode == 'review' ? true : false
			}
		);
		
		this.cssEditor = CodeMirror(
			this.playground.box.querySelector('.css-editor-wrapper'), 
			{
				value: css,
				lineNumbers: true, 
				lineWrapping: true,
				mode: 'css',
				viewportMargin: Infinity,
				readOnly: this.playground.mode == 'review' ? true : false
			}
		);	
	}
	
	this.initEditors();
	
	this.updatePreview = function() {
		this.previewHtml.innerHTML = this.htmlEditor.getValue();
		
		var css = this.cssEditor.getValue();
		this.previewStyle.innerHTML = css.replace(/(([^\r\n,{}]+)(,(?=[^}]*{)|\s*{))/g, '#' + this.previewId + ' $1');
	}
	
	this.htmlEditor.on("change", this.updatePreview.bind(this));
	this.cssEditor.on("change", this.updatePreview.bind(this));
	this.updatePreview();
	
	this.save = function() {
		this.source.value = this.htmlEditor.getValue() + this.separator + this.cssEditor.getValue();
	}
}

var MarkdownToy = function(playground) {
	this.playground = playground;
	this.playground.el.classList.add('wide');
	this.source = playground.el.querySelector('textarea');
	this.source.style.display = 'none';
	this.marked = marked.setOptions({
		renderer: new marked.Renderer(),
		gfm: true,
		tables: true,
		breaks: false,
		pedantic: false,
		sanitize: false,
		smartLists: true,
		smartypants: false
	});
	
	
	this.playground.box.innerHTML = 
		'<div class="row">' +
			'<div class="editor-wrapper columns large-6"></div>' +
			'<div class="columns large-6 markdown-preview-wrapper">' +
				'<p><span class="label">Náhled</span></p>' + // Preview
				'<div class="markdown-preview"></div>' +
			'</div>' +
			'<div class="columns stats"></div>' +
		'</div>';
	
	this.preview = this.playground.box.querySelector('.markdown-preview');
	this.stats = this.playground.box.querySelector('.stats');
	
	this.editor = CodeMirror(
		this.playground.box.querySelector('.editor-wrapper'), 
		{
			value: this.source.value,
			lineNumbers: true, 
			lineWrapping: true,
			mode: 'markdown',
			viewportMargin: Infinity,
			readOnly: this.playground.mode == 'review' ? true : false
		}
	);
	
	this.updatePreview = function() {
		var val = this.editor.getValue();
		this.preview.innerHTML = this.marked(val);
		
		var len = this.preview.textContent.replace(/\s+/g, ' ').length;
		this.stats.innerHTML = '<p>Počet znaků: ' + len + '</p>';
	}
	
	this.editor.on("change", this.updatePreview.bind(this));
	this.updatePreview();
	
	this.save = function() {
		this.source.value = this.editor.getValue();
	}
	
}

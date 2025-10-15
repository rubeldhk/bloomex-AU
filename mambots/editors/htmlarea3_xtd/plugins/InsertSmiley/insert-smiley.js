function InsertSmiley(editor) {
	this.editor = editor;
	var cfg = editor.config;
	var tt = InsertSmiley.I18N;
	var bl = InsertSmiley.btnList;
	var self = this;

	// register the toolbar buttons provided by this plugin
	var toolbar = [];
	for (var i in bl) {
		var btn = bl[i];
		if (!btn) {
			toolbar.push("separator");
		} else {
			var id = "IS-" + btn[0];
			cfg.registerButton(id, tt[id], editor.imgURL(btn[0] + ".gif", "InsertSmiley"), false,
					   function(editor, id) {
						   // dispatch button press event
						   self.buttonPress(editor, id);
					   }, btn[1]);
			toolbar.push(id);
		}
	}

	// add a new line in the toolbar
	for (var i in toolbar) {
		cfg.toolbar[0].push("separator");
		cfg.toolbar[0].push(toolbar[i]);
	}
};

InsertSmiley._pluginInfo = {
	name          : "InsertSmiley",
	version       : "v1.0",
	developer     : "Bernhard Pfeifer aka novocaine",
	developer_url : "http://www.novocaine.de/",
	c_owner       : "Bernhard Pfeifer",
	sponsor       : "none",
	sponsor_url   : "none",
	license       : "HTMLArea"
};

InsertSmiley.btnList = [
	//null, // separator
	["insert-smiley"]
	];

InsertSmiley.prototype.buttonPress = function(editor, id) {
		this.editor = editor;
		InsertSmiley.editor = editor;
		InsertSmiley.init = true;
		var sel = editor._getSelection();
		var range = editor._createRange(sel);
		editor._popupDialog("plugin://InsertSmiley/insert_smiley", function(url) {
		if(!url) {
			return false;
		}
		editor.focusEditor();
		editor._doc.execCommand("insertimage", false, url);
		var img = null;
		if (HTMLArea.is_ie) {
			try {
				img = range.parentElement();
					if (img.tagName.toLowerCase() != "img") {
						img = img.previousSibling;
					}
			} catch (e) {};
		} else {
			img = range.startContainer.previousSibling;
		}
	}, null);
};
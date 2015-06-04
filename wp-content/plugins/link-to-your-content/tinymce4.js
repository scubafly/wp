function getCheckedValue(radioObj) {
	if(!radioObj)
		return "";
	var radioLength = radioObj.length;
	if(radioLength === undefined)
		if(radioObj.checked)
			return radioObj.value;
		else
			return "";
	for(var i = 0; i < radioLength; i++) {
		if(radioObj[i].checked) {
			return radioObj[i].value;
		}
	}
	return "";
}

function insertDocumentLink(sel) {
	var tagtext; var tagtarget; var elem; var images;
	var posts_panel = document.getElementById('posts_panel');
	var pages_panel = document.getElementById('pages_panel');
	var docs_panel = document.getElementById('docs_panel');
	var images_panel = document.getElementById('images_panel');
	var media_panel = document.getElementById('media_panel');
	var cptypes_panel = document.getElementById('cptypes_panel');
	var taxonomies_panel = document.getElementById('taxonomies_panel');
	var external_panel = document.getElementById('external_panel');
	//var search_panel = document.getElementById('search_panel');

	var linkHref, linkDefaultTitle, linkTitle, linkTarget;

	// who is active ?
	if(posts_panel.className.indexOf('current') !== -1) {
		linkHref = document.getElementById('linkHref').value;
		linkDefaultTitle = document.getElementById('linkDefaultTitle').value;
		linkTitle = document.getElementById('posts_linkTitle').value;
		linkTarget = document.getElementById('posts_linkTarget').value;
		if(linkHref !== 0) {
			if(linkTitle !== '' && linkTitle.length > 0) { tagtext = linkTitle; }
			else {
				if(sel.indexOf('<img') !== -1) {
					elem = document.createElement("div");
					elem.innerHTML = sel;

					images = elem.getElementsByTagName("img");
					tagtext = images[0].alt;
				}
				else {
					tagtext = sel;
				}
			}
			tagtarget = linkTarget;
		}
		else
			top.tinymce.activeEditor.windowManager.close();
	}

	if(pages_panel.className.indexOf('current') !== -1) {
		linkHref = document.getElementById('linkHref').value;
		linkDefaultTitle = document.getElementById('linkDefaultTitle').value;
		linkTitle = document.getElementById('pages_linkTitle').value;
		linkTarget = document.getElementById('pages_linkTarget').value;
		if(linkHref !== 0) {
			if(linkTitle !== '' && linkTitle.length > 0) { tagtext = linkTitle; }
			else {
				if(sel.indexOf('<img') !== -1) {
					elem = document.createElement("div");
					elem.innerHTML = sel;

					images = elem.getElementsByTagName("img");
					tagtext = images[0].alt;
				}
				else {
					tagtext = sel;
				}
			}
			tagtarget = linkTarget;
		}
		else
			top.tinymce.activeEditor.windowManager.close();
	}
	
	if(docs_panel.className.indexOf('current') !== -1) {
		linkHref = document.getElementById('linkHref').value;
		linkDefaultTitle = document.getElementById('linkDefaultTitle').value;
		linkTitle = document.getElementById('docs_linkTitle').value;
		linkTarget = document.getElementById('docs_linkTarget').value;
		if(linkHref !== 0) {
			if(linkTitle !== '' && linkTitle.length > 0) { tagtext = linkTitle; }
			else {
				if(sel.indexOf('<img') !== -1) {
					elem = document.createElement("div");
					elem.innerHTML = sel;

					images = elem.getElementsByTagName("img");
					tagtext = images[0].alt;
				}
				else {
					tagtext = sel;
				}
			}
			tagtarget = linkTarget;
		}
		else
			top.tinymce.activeEditor.windowManager.close();
	}

	if(images_panel.className.indexOf('current') !== -1) {
		linkHref = document.getElementById('linkHref').value;
		linkDefaultTitle = document.getElementById('linkDefaultTitle').value;
		linkTitle = document.getElementById('images_linkTitle').value;
		linkTarget = document.getElementById('images_linkTarget').value;
		if(linkHref !== 0) {
			if(linkTitle !== '' && linkTitle.length > 0) { tagtext = linkTitle; }
			else {
				if(sel.indexOf('<img') !== -1) {
					elem = document.createElement("div");
					elem.innerHTML = sel;

					images = elem.getElementsByTagName("img");
					tagtext = images[0].alt;
				}
				else {
					tagtext = sel;
				}
			}
			tagtarget = linkTarget;
		}
		else
			top.tinymce.activeEditor.windowManager.close();
	}

	if(media_panel.className.indexOf('current') !== -1) {
		linkHref = document.getElementById('linkHref').value;
		linkDefaultTitle = document.getElementById('linkDefaultTitle').value;
		linkTitle = document.getElementById('media_linkTitle').value;
		linkTarget = document.getElementById('media_linkTarget').value;
		if(linkHref !== 0) {
			if(linkTitle !== '' && linkTitle.length > 0) { tagtext = linkTitle; }
			else {
				if(sel.indexOf('<img') !== -1) {
					elem = document.createElement("div");
					elem.innerHTML = sel;

					images = elem.getElementsByTagName("img");
					tagtext = images[0].alt;
				}
				else {
					tagtext = sel;
				}
			}
			tagtarget = linkTarget;
		}
		else
			top.tinymce.activeEditor.windowManager.close();
	}

	if(cptypes_panel.className.indexOf('current') !== -1) {
		linkHref = document.getElementById('linkHref').value;
		linkDefaultTitle = document.getElementById('linkDefaultTitle').value;
		linkTitle = document.getElementById('cptypes_linkTitle').value;
		linkTarget = document.getElementById('cptypes_linkTarget').value;
		if(linkHref !== 0) {
			if(linkTitle !== '' && linkTitle.length > 0) { tagtext = linkTitle; }
			else {
				if(sel.indexOf('<img') !== -1) {
					elem = document.createElement("div");
					elem.innerHTML = sel;

					images = elem.getElementsByTagName("img");
					tagtext = images[0].alt;
				}
				else {
					tagtext = sel;
				}
			}
			tagtarget = linkTarget;
		}
		else
			top.tinymce.activeEditor.windowManager.close();
	}

	if(taxonomies_panel.className.indexOf('current') !== -1) {
		linkHref = document.getElementById('linkHref').value;
		linkDefaultTitle = document.getElementById('linkDefaultTitle').value;
		linkTitle = document.getElementById('taxonomies_linkTitle').value;
		linkTarget = document.getElementById('taxonomies_linkTarget').value;
		if(linkHref !== 0) {
			if(linkTitle !== '' && linkTitle.length > 0) { tagtext = linkTitle; }
			else {
				if(sel.indexOf('<img') !== -1) {
					elem = document.createElement("div");
					elem.innerHTML = sel;

					images = elem.getElementsByTagName("img");
					tagtext = images[0].alt;
				}
				else {
					tagtext = sel;
				}
			}
			tagtarget = linkTarget;
		}
		else
			top.tinymce.activeEditor.windowManager.close();
	}

	if(external_panel.className.indexOf('current') !== -1) {
		linkHref = document.getElementById('externalLink').value;
		linkTitle = document.getElementById('external_linkTitle').value;
		linkTarget = document.getElementById('external_linkTarget').value;
		linkType = getCheckedValue(document.LinkToYourContent.externalLinkType);
		if(linkHref !== 0 && linkHref != 'http://' && linkHref != 'mailto:') {
			if(linkType == 'http') { tagtarget = linkTarget; }
			if(linkTitle !== '' && linkTitle.length > 0) { tagtext = linkTitle; }
			else {
				if(sel.indexOf('<img') !== -1) {
					elem = document.createElement("div");
					elem.innerHTML = sel;

					images = elem.getElementsByTagName("img");
					tagtext = images[0].alt;
				}
				else {
					tagtext = sel;
				}
			}
		}
		else
			top.tinymce.activeEditor.windowManager.close();
	}

	/*if(search_panel.className.indexOf('current') !== -1) {
		linkHref = document.getElementById('linkHref').value;
		linkDefaultTitle = document.getElementById('linkDefaultTitle').value;
		linkTitle = document.getElementById('search_linkTitle').value;
		linkTarget = document.getElementById('search_linkTarget').value;
		if(linkHref !== 0) {
			if(linkTitle !== '' && linkTitle.length > 0) { tagtext = linkTitle; }
			else {
				if(sel.indexOf('<img') !== -1) {
					elem = document.createElement("div");
					elem.innerHTML = sel;

					images = elem.getElementsByTagName("img");
					tagtext = images[0].alt;
				}
				else {
					tagtext = sel;
				}
			}
			tagtarget = linkTarget;
		}
		else
			top.tinymce.activeEditor.windowManager.close();
	}*/
	
	if(top.tinymce.activeEditor) {
		if(typeof tagtarget != 'undefined' && tagtarget.length > 0) {
			top.tinymce.activeEditor.execCommand('mceInsertLink', false, {href: linkHref, title: tagtext, target: tagtarget});
		}
		else {
			top.tinymce.activeEditor.execCommand('mceInsertLink', false, {href: linkHref, title: tagtext});
		}

		top.tinymce.activeEditor.execCommand('mceRepaint');
		top.tinymce.activeEditor.windowManager.close();
	}
	return;
}

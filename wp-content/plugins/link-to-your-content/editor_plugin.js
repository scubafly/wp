(function() {
	tinymce.create('tinymce.plugins.LinkToYourContent', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {
			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');

			ed.addCommand('mceLinkToYourContent', function() {
				ed.windowManager.open({
					title : ed.getLang('LinkToYourContent.pluginTitle'),
					file : url + '/content_links.php',
					width : 785,
					height : 575,
					inline : 1
				}, {
					plugin_url : url, // Plugin absolute URL
					sel : ed.selection.getContent({format: 'text'}),
					invalidSite : ed.getLang('LinkToYourContent_dlg.invalidSite'),
					siteChangeError : ed.getLang('LinkToYourContent_dlg.siteChangeError'),
					requestError : ed.getLang('LinkToYourContent_dlg.requestError'),
					invalidPage : ed.getLang('LinkToYourContent_dlg.invalidPage'),
					invalidPanel : ed.getLang('LinkToYourContent_dlg.invalidPanel'),
					invalidCPT : ed.getLang('LinkToYourContent_dlg.invalidCPT'),
					invalidTax : ed.getLang('LinkToYourContent_dlg.invalidTax')
				});
			});

			// Register example button
			ed.addButton('LinkToYourContent', {
				title : ed.getLang('LinkToYourContent.pluginTitle'),
				cmd : 'mceLinkToYourContent',
				image : url + '/imgs/content_links.gif'
			});
		},

		/**
		 * Creates control instances based in the incomming name. This method is normally not
		 * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
		 * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
		 * method can be used to create those.
		 *
		 * @param {String} n Name of the control to create.
		 * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
		 * @return {tinymce.ui.Control} New control instance or null if no control was created.
		 */
		createControl : function(n, cm) {
			return null;
		},

		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
				longname  : 'LinkToYourContent',
				author    : 'Ray Milstrey',
				authorurl : 'http://www.maymond.com',
				infourl   : 'http://www.maymond.com/link-to-your-content',
				version   : "1.8.3"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('LinkToYourContent', tinymce.plugins.LinkToYourContent);
})();
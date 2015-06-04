(function() {
	tinymce.PluginManager.add( 'LinkToYourContent', function( editor, url ) {

        // Add a button that opens a window
        editor.addButton( 'LinkToYourContent', {
            text: '',
            title: editor.getLang('LinkToYourContent.pluginTitle'),
            icon: 'LinkToYourContent',
            onclick: function() {
                // Open window
                editor.windowManager.open( {
                    title: 'Link to Your Content',
					width: 785,
					height: 575,
					url: url + '/content_links.php'
				}, {
					plugin_url : url, // Plugin absolute URL
					sel : editor.selection.getContent(),
					invalidSite : editor.getLang('LinkToYourContent_dlg.invalidSite'),
					siteChangeError : editor.getLang('LinkToYourContent_dlg.siteChangeError'),
					requestError : editor.getLang('LinkToYourContent_dlg.requestError'),
					invalidPage : editor.getLang('LinkToYourContent_dlg.invalidPage'),
					invalidPanel : editor.getLang('LinkToYourContent_dlg.invalidPanel'),
					invalidCPT : editor.getLang('LinkToYourContent_dlg.invalidCPT'),
					invalidTax : editor.getLang('LinkToYourContent_dlg.invalidTax')
                } );
            },
			getInfo : function() {
				return {
					longname  : 'LinkToYourContent',
					author    : 'Ray Milstrey',
					authorurl : 'http://www.maymond.com',
					infourl   : 'http://www.maymond.com/link-to-your-content',
					version   : "1.8.3"
				};
			}
        } );
    } );
})();
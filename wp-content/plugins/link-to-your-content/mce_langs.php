<?php
	// Language Strings for Tinymce
	$strings = 'tinyMCE.addI18n({' . _WP_Editors::$mce_locale . 
		'LinkToYourContent: {
	    	pluginTitle: "' . esc_js(__('Link to Your Content', 'link-to-your-content')) . '",
	    	invalidSite: "' . esc_js(__('I&rsquo;m sorry but it seems that you are trying to switch to a site that doesn&rsquo;t exist. Please select a valid site from the dropdown above.', 'link-to-your-content') ) . '",
			siteChangeError: "' . esc_js(__('I&rsquo;m sorry but it seems that we were unable to switch you to the site you requested. Please select another site from the dropdown above.', 'link-to-your-content') ) . '",
			requestError: "' . esc_js(__('I&rsquo;m sorry but it seems that we were unable to complete your request. Please try again.', 'link-to-your-content') ) . '",
			invalidPage: "' . esc_js(__('I&rsquo;m sorry but it seems that you are trying to switch to page that doesn&rsquo;t exist. Please try again.', 'link-to-your-content') ) . '",
			invalidPanel: "' . esc_js(__('I&rsquo;m sorry but it seems that you are trying to switch to a panel that doesn&rsquo;t exist. Please try again.', 'link-to-your-content') ) . '",
			invalidCPT: "' . esc_js(__('I&rsquo;m sorry but it seems that you are trying to switch to a custom post type that doesn&rsquo;t exist. Please try again.', 'link-to-your-content') ) . '",
			invalidTax: "' . esc_js(__('I&rsquo;m sorry but it seems that you are trying to switch to a taxonomy that doesn&rsquo;t exist. Please try again.', 'link-to-your-content') ) . '"
	    }
	})';
?>
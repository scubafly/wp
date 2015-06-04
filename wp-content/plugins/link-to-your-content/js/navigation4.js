function getQueryVariable(query, variable) {
	if (query.indexOf('?') != -1) { query = query.substring(query.indexOf('?') + 1, query.length); }
	if (query.indexOf('#') != -1) { query = query.substring(0, query.indexOf('#')); }
	var vars = query.split("&");
	for (var i=0;i<vars.length;i++) {
		var pair = vars[i].split("=");
		if (pair[0] == variable) { return pair[1]; }
	}
	return false;
}

var sel = top.tinymce.activeEditor.windowManager.getParams().sel;
if(sel === '') { top.tinymce.activeEditor.close(); }

var invalidSite = top.tinymce.activeEditor.windowManager.getParams().invalidSite;
var siteChangeError = top.tinymce.activeEditor.windowManager.getParams().siteChangeError;
var requestError = top.tinymce.activeEditor.windowManager.getParams().requestError;
var invalidPage = top.tinymce.activeEditor.windowManager.getParams().invalidPage;
var invalidPanel = top.tinymce.activeEditor.windowManager.getParams().invalidPanel;
var invalidCPT = top.tinymce.activeEditor.windowManager.getParams().invalidCPT;
var invalidTax = top.tinymce.activeEditor.windowManager.getParams().invalidTax;

jQuery.noConflict();
jQuery(document).ready(function($) {
	// Set up the table striping for each of the panels
	$("#docs_panel > .documentListWrap > table.striped > tbody > tr:even > td").css("background-color", "#f1f1f1");
	$("#images_panel > .documentListWrap > table.striped > tbody > tr:even > td").css("background-color", "#f1f1f1");
	$("#media_panel > .documentListWrap > table.striped > tbody > tr:even > td").css("background-color", "#f1f1f1");
	$("#posts_panel > .documentListWrap > table.striped > tbody > tr:even > td").css("background-color", "#f1f1f1");
	$("#pages_panel > .documentListWrap > table.striped > tbody > tr:even > td").css("background-color", "#f1f1f1");
	$("#cptypes_panel > .documentListWrap > table.striped > tbody > tr:even > td").css("background-color", "#f1f1f1");
	$("#taxonomies_panel > .documentListWrap > table.striped > tbody > tr:even > td").css("background-color", "#f1f1f1");
	// When the Select button is clicked
	$("#link").on('click', 'a.selectDocument', function() {
		$("input#linkHref").val($(this).attr("href"));
		$("input#linkDefaultTitle").val($(this).attr("title"));
		$("tr.selectedDocument").removeClass("selectedDocument");
		$(this).parent("td").parent("tr").addClass("selectedDocument");
		return false;
	});
	// Fires when the external link radio buttons are clicked
	$("#external_panel").on('change', "input[name='externalLinkType']", function() {
		if($(this).val() == 'http') { $('input#externalLink').val('http://'); }
		else if($(this).val() == 'mailto') { $('input#externalLink').val('mailto:'); }
		else { $('input#externalLink').val('http://'); }
	});
	// Site toggle drop down functionality
	$('#siteToggle').change(function() {
		//window.location = $('#siteToggle > option:selected').val();
		$.ajax({
			type: "post",
			url: ajaxURL,
			dataType: "json",
			data: {
				action: 'ltyc_site_switch',
				_ajax_nonce: siteToggleNonce,
				ajax_site: $('#siteToggle > option:selected').val()
			},
			beforeSend: function() {
				$('div#loadingGraphic').show();
			},
			success: function(data) {
				if(data.error == '1') {
					$('div.panel_wrapper').html('<p class="errorMessage">'+invalidSite+'</p>');
				} else if(data.error == '2') {
					$('div.panel_wrapper').html('<p class="errorMessage">'+siteChangeError+'</p>');
				} else {
					$('div#posts_panel').html(data.posts_panel);
					$('div#pages_panel').html(data.pages_panel);
					$('div#cptypes_panel').html(data.cptypes_panel);
					$('div#taxonomies_panel').html(data.taxonomies_panel);
					$('div#docs_panel').html(data.docs_panel);
					$('div#images_panel').html(data.images_panel);
					$('div#media_panel').html(data.media_panel);
					$('div.tabs').html(data.content_tabs);
					mcTabs.displayTab('posts_tab','posts_panel');
					$("#posts_panel > .documentListWrap > table.striped > tbody > tr:even > td").css("background-color", "#f1f1f1");
					$("#pages_panel > .documentListWrap > table.striped > tbody > tr:even > td").css("background-color", "#f1f1f1");
					$("#cptypes_panel > .documentListWrap > table.striped > tbody > tr:even > td").css("background-color", "#f1f1f1");
					$("#taxonomies_panel > .documentListWrap > table.striped > tbody > tr:even > td").css("background-color", "#f1f1f1");
					$("#docs_panel > .documentListWrap > table.striped > tbody > tr:even > td").css("background-color", "#f1f1f1");
					$("#images_panel > .documentListWrap > table.striped > tbody > tr:even > td").css("background-color", "#f1f1f1");
					$("#media_panel > .documentListWrap > table.striped > tbody > tr:even > td").css("background-color", "#f1f1f1");
				}
				$('div#loadingGraphic').hide();
			},
			error: function() {
				$('div.panel_wrapper').html('<p class="errorMessage">'+requestError+'</p>');
				$('div#loadingGraphic').hide();
			}
		});
		return false;
	});
	// Pagination link functionality
	$('.panel_wrapper').on('click', 'p.pagination > a', function() {
		var ajax_type = getQueryVariable($(this).attr('href'), 'type');
		var ajax_page = getQueryVariable($(this).attr('href'), 'page');
		var ajax_site = getQueryVariable($(this).attr('href'), 'site');
		var ajax_cpt = getQueryVariable($(this).attr('href'), 'cpt');
		var ajax_tax = getQueryVariable($(this).attr('href'), 'tax');

		$.ajax({
			type: "post",
			url: ajaxURL,
			dataType: "json",
			data: {
				action: 'ltyc_page_switch',
				_ajax_nonce: paginationNonce,
				ajax_type: ajax_type,
				ajax_page: ajax_page,
				ajax_site: ajax_site,
				ajax_cpt: ajax_cpt,
				ajax_tax: ajax_tax
			},
			beforeSend: function() {
				$('div#loadingGraphic').show();
			},
			success: function(data) {
				if(data.error == '1') {
					$('div.panel_wrapper').html('<p class="errorMessage">'+invalidPage+'</p>');
				} else {
					switch(data.ajax_type) {
						case 'posts' :
							$('div#posts_panel').html(data.posts_panel);
							$("#posts_panel > .documentListWrap > table.striped > tbody > tr:even > td").css("background-color", "#f1f1f1");
							break;
						case 'pages' :
							$('div#pages_panel').html(data.pages_panel);
							$("#pages_panel > .documentListWrap > table.striped > tbody > tr:even > td").css("background-color", "#f1f1f1");
							break;
						case 'cptypes' :
							$('div#cptypes_panel').html(data.cptypes_panel);
							$("#cptypes_panel > .documentListWrap > table.striped > tbody > tr:even > td").css("background-color", "#f1f1f1");
							break;
						case 'taxonomies' :
							$('div#taxonomies_panel').html(data.taxonomies_panel);
							$("#taxonomies_panel > .documentListWrap > table.striped > tbody > tr:even > td").css("background-color", "#f1f1f1");
							break;
						case 'docs' :
							$('div#docs_panel').html(data.docs_panel);
							$("#docs_panel > .documentListWrap > table.striped > tbody > tr:even > td").css("background-color", "#f1f1f1");
							break;
						case 'images' :
							$('div#images_panel').html(data.images_panel);
							$("#images_panel > .documentListWrap > table.striped > tbody > tr:even > td").css("background-color", "#f1f1f1");
							break;
						case 'media' :
							$('div#media_panel').html(data.media_panel);
							$("#media_panel > .documentListWrap > table.striped > tbody > tr:even > td").css("background-color", "#f1f1f1");
							break;
						default :
							$('div#posts_panel').html(data.posts_panel);
							$("#posts_panel > .documentListWrap > table.striped > tbody > tr:even > td").css("background-color", "#f1f1f1");
							break;
					}
				}
				$('div#loadingGraphic').hide();
			},
			error: function() {
				$('div.panel_wrapper').html('<p class="errorMessage">'+requestError+'</p>');
				$('div#loadingGraphic').hide();
			}
		});
		return false;
	});
	// Custom post type drop down
	$('#cptypes_panel').on('change', '#cptToggle', function() {
		$.ajax({
			type: "post",
			url: ajaxURL,
			dataType: "json",
			data: {
				action: 'ltyc_panel_switch',
				_ajax_nonce: cptToggleNonce,
				ajax_cpt: $('#cptToggle > option:selected').val(),
				ajax_type: 'cptypes'
			},
			beforeSend: function() {
				$('div#loadingGraphic').show();
			},
			success: function(data) {
				if(data.error == '1') {
					$('div.panel_wrapper').html('<p class="errorMessage">'+invalidCPT+'</p>');
				} else if(data.error == '2') {
					$('div.panel_wrapper').html('<p class="errorMessage">'+invalidPanel+'</p>');
				} else {
					$('div#cptypes_panel').html(data.cptypes_panel);
					$("#cptypes_panel > .documentListWrap > table.striped > tbody > tr:even > td").css("background-color", "#f1f1f1");
				}
				$('div#loadingGraphic').hide();
			},
			error: function() {
				$('div.panel_wrapper').html('<p class="errorMessage">'+requestError+'</p>');
				$('div#loadingGraphic').hide();
			}
		});
		return false;
	});
	// Taxonomy drop down
	$('#taxonomies_panel').on('change', '#taxToggle', function() {
		$.ajax({
			type: "post",
			url: ajaxURL,
			dataType: "json",
			data: {
				action: 'ltyc_panel_switch',
				_ajax_nonce: taxToggleNonce,
				ajax_tax: $('#taxToggle > option:selected').val(),
				ajax_type: 'taxonomies'
			},
			beforeSend: function() {
				$('div#loadingGraphic').show();
			},
			success: function(data) {
				if(data.error == '1') {
					$('div.panel_wrapper').html('<p class="errorMessage">'+invalidTax+'</p>');
				} else if(data.error == '2') {
					$('div.panel_wrapper').html('<p class="errorMessage">'+invalidPanel+'</p>');
				} else {
					$('div#taxonomies_panel').html(data.taxonomies_panel);
					$("#taxonomies_panel > .documentListWrap > table.striped > tbody > tr:even > td").css("background-color", "#f1f1f1");
				}
				$('div#loadingGraphic').hide();
			},
			error: function() {
				$('div.panel_wrapper').html('<p class="errorMessage">'+requestError+'</p>');
				$('div#loadingGraphic').hide();
			}
		});
		return false;
	});
});
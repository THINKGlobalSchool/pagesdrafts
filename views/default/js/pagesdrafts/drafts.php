<?php
/**
 * Pages drafts JS
 * 
 * @package PagesDrafts
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2013
 * @link http://www.thinkglobalschool.com/
 * 
 */
?>
elgg.provide('elgg.pagesdrafts');

// These fields will trigger draft saves, and will be loaded with draft contents if available
elgg.pagesdrafts.draftFields = ['title', 'description', 'tags', 'access_id', 'write_access_id', 'parent_guid', 'page_guid'];

/**	
 * Helper to check for local storage support
 */
elgg.pagesdrafts.supportsLocalStorage = function() {
	try {
		return 'localStorage' in window && window['localStorage'] !== null;
	} catch (e) {
		return false;
	}
}

/**
 * Pages init
 */
elgg.pagesdrafts.init = function() {
	// Check for errors (need a better way to do this)
	if ($('.elgg-system-messages > .elgg-message.elgg-state-error').length === 0 &&
		$('.elgg-system-messages > .elgg-message.elgg-state-success').length >= 1) {
		// Clear draft
		localStorage.clear();
	}

	// Load draft
	elgg.pagesdrafts.loadDraft();

	// Set up onchange/keyup to trigger draft saves
	$('form.elgg-form-pages-edit :input').each(function() {
		if ($.inArray($(this).attr('name'), elgg.pagesdrafts.draftFields) !== -1) {
			var tagName = this.tagName;
			switch (this.tagName) {
				case 'INPUT':
					$(this).keyup(elgg.pagesdrafts.saveDraft);
					break;
				case 'SELECT':
					$(this).change(elgg.pagesdrafts.saveDraft);
					break;
				case 'TEXTAREA':
					// Regular plain text input
					$(this).keyup(elgg.pagesdrafts.saveDraft);

					// CKEditor inputs
					require(['elgg/ckeditor', 'jquery', 'jquery.ckeditor'], function(elggCKEditor, $) {
						for (var i in CKEDITOR.instances) {
					        CKEDITOR.instances[i].on('change', elgg.pagesdrafts.saveDraft);
						}
					});

					break;
			}
		}
	});
};

/** 
 * Save draft when form contents change
 */
elgg.pagesdrafts.saveDraft = function(event) {
	// Make sure local storage is supported
	if (!elgg.pagesdrafts.supportsLocalStorage()) {
		return false;
	}

	// Create draft object with timestamp
	var draft = {
		timestamp: Math.round(+new Date() / 1000)
	};

	// Set draft fields
	$.each(elgg.pagesdrafts.draftFields, function(idx, item) {
		draft[item] = $('form.elgg-form-pages-edit :input[name="' + item + '"]').val();
	});

	// Store draft
	localStorage.setItem('elgg.pagesdrafts.draft', JSON.stringify(draft));
}

/**
 * Load draft if available
 */
elgg.pagesdrafts.loadDraft = function() {
	// Make sure local storage is supported
	if (!elgg.pagesdrafts.supportsLocalStorage()) {
		return false;
	}

	// If we've got a draft, load it in
	var draft = localStorage.getItem('elgg.pagesdrafts.draft');

	draft = JSON.parse(draft);

	// Check for draft, and confirm the draft exists for the current page entity (editing)
	if (draft && draft.page_guid === $('form.elgg-form-pages-edit :input[name="page_guid"]').val()) {
		$("<span class='message warning'>" + elgg.echo('pagesdrafts:editingdraft') + "</span>").insertBefore('.elgg-form-pages-edit');
		$.each(elgg.pagesdrafts.draftFields, function(idx, item) {
			$('form.elgg-form-pages-edit :input[name="' + item + '"]').val(draft[item]);
		});
	}
}

elgg.register_hook_handler('init', 'system', elgg.pagesdrafts.init);
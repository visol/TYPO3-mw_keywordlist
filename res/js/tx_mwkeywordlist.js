/**
 * Javascript class for the mw_keywordlist extension.
 * Based on jQuery.
 *
 * @package		TYPO3
 * @subpackage	tx_mwkeywordlist
 * @version		$Id$
 * @author		mehrwert <typo3@mehrwert.de>
 * @license		GPL
 */
var TxMwKeywordList = {

	/**
	 * Init method.
	 * Get all menus on this page. For each of the menus hide all sections except the first one
	 * and add click actions to jump menus.
	 */
	init: function() {
		$('.tx-mwkeywordlist-pi1').each(function(index, element) {
				// Hide all sections except the first one
			TxMwKeywordList.showLetterContent($(element).find('A[name]:first'));
				// Add click action to jump menu links
			$(element).find('.tx-mwkeywordlist-pi1-jumpmenu A').bind({
				click: function() {
					TxMwKeywordList.showLetterContent($(element).find('A[name='+this.rel+']'));
					return false;
				}
			});
		});
	},

	/**
	 * Get the siblings of the incoming A-Tag and hide them and the tag.
	 * Show the elements between the current tag and the next tag.
	 *
	 * @param element The A-Tag element whose siblings should be shown
	 */
	showLetterContent: function(element) {
		$(element).parents('.tx-mwkeywordlist-pi1').find('A[name]').each(function(index, item) {
				// If current id does not match the required id,
				// hide the item an all of its siblings (up to the next link)
			if (item.id != $(element).attr('id')) {
				var item = $(item);
				item.nextUntil('A[name]').hide();
				item.hide();
			} else {
				var item = $(item);
				item.nextUntil('A[name]').fadeIn();
				item.fadeIn();
			}
		});
	}
};

	// Test for jQuery
if (typeof jQuery == 'undefined') {
	alert('Error: The jQuery library has to be included (use page.includeJS)');
} else {
	// Wait until DOM is ready
	$(document).ready(function() {
			// Initiate the keyword list class
		new TxMwKeywordList.init();
	});
}

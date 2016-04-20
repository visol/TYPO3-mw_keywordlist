

.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. ==================================================
.. DEFINE SOME TEXTROLES
.. --------------------------------------------------
.. role::   underline
.. role::   typoscript(code)
.. role::   ts(typoscript)
   :class:  typoscript
.. role::   php(code)


==========
Change Log
==========

The following is an overview of the changes in this extension. For more details `read the online log <https://github.com/mehrwert/TYPO3-mw_keywordlist>`_.

Version 3.5.2 - 2016-04-20
^^^^^^^^^^^^^^^^^^^^^^^^^^

- [CHANGE] Render jumpmenu links with padding for more clickable area

  - Maintenance release to replace spaces in jumpmenu by CSS paddings.
    Thanks to Philipp Rönsch - https://github.com/theorak - for this contribution!
  - Update composer.json

Version 3.5.1 - 2016-04-08
^^^^^^^^^^^^^^^^^^^^^^^^^^

Re-release of 3.5.0 to TER with compatibility set to 8.0.99.

Version 3.5.0 - 2016-03-05
^^^^^^^^^^^^^^^^^^^^^^^^^^

Maintenance release with focus on compatibility for TYPO3 CMS 7 LTS. This release
does not provide new features but fixes some minor issues and provides updates for
documentation and code. Also, a composer file is included now.

- [CHANGE] Revised Code for TYPO3 CMS 7 LTS and PHP 7.0.x compatibility

  - Add missing top anchor in HTML output and rename default anchor in
    TypoScript to section = tx-mwkeywordlist-top (you must change this to
    section = top if you want to use the former anchor).
  - Add composer.json
  - Reformat ReadMe.md and remove Changelog.md
  - Reformat the change log in documentation
  - Update the manual and add screenshots for TYPO3 CMS 7 LTS
  - Add separate class file for TYPO3 CMS 6.2 LTS and above
  - Set compatibility for TYPO3 CMS 8.0-dev (tested with CSS Styled Content)

Version 3.4.1
^^^^^^^^^^^^^

- [CHANGE] Maintenance release: Fix image includes in manual and reorganize documentation structure.

Version 3.4.0
^^^^^^^^^^^^^

- [CHANGE] Converted manual to ReST and updated constraints for PHP 5.4 and TYPO3 v6.2. Last maintenance update. Next release will include a major refactoring (v4.0).

Version 3.3.0
^^^^^^^^^^^^^

- [CHANGE] The »0-9« block may be moved to last position in index. Some fixes.

Version 3.2.0
^^^^^^^^^^^^^

- [CHANGE] Each paragraph is wrapped in jQuery panels (if the JS is included)

Version 3.1.0
^^^^^^^^^^^^^

- [CHANGE] Revised Code for TYPO3 4.6 and PHP 5.x compatibility. Switch to XML locallang files.

Version 3.0.5
^^^^^^^^^^^^^

- [CHANGE] Bugfix release: Fixed a bug where a PHP error is thrown if no keywords are defined. Credits and thanks to Sascha Korzen for reporting this. (2009-05-03)

Version 3.0.4
^^^^^^^^^^^^^

- [CHANGE] Maintenance release: Updated constraints for compatiblity with TYPO3 4.2.x - no other changes. (2008-11-02)

Version 3.0.3
^^^^^^^^^^^^^

- [CHANGE] Issue #4562 [1]: Fixed a PHP fatal error which may occure if other extensions use method name arraysort(). Renamed to mw_arraysort()

Version 3.0.1
^^^^^^^^^^^^^

- [CHANGE] Major rewrite for improved stability and overall performance on large page trees. Remains compatible to old version and configuration. In this release (3.0.1): Added detection if user did not set a starting point and added support to start at the TYPO3 rootline

Version 3.0.0
^^^^^^^^^^^^^

- [CHANGE] Major rewrite for improved stability and overall performance on large page trees. Remains compatible to old version and configuration.

Version 1.3.1
^^^^^^^^^^^^^

- [CHANGE] Fixed an issue with umlauts and sorting; Added support for csConvObj if TYPO3 version is 3.7.0 or higher.

Version 1.3.0
^^^^^^^^^^^^^

- [CHANGE] Code for multilang support rewritten and optimized

Version 1.2.2
^^^^^^^^^^^^^

- [CHANGE] Updated the documentation

Version 1.2.1
^^^^^^^^^^^^^

- [CHANGE] Merged Finnish translation

Version 1.2.0
^^^^^^^^^^^^^

- [CHANGE] Added compatibility with multilangual sites, changed DB queries to utilize DBAL and added support for multiple startingpoints per index

Version 1.1.0
^^^^^^^^^^^^^

- [CHANGE] Documentation review

Version 1.0.5
^^^^^^^^^^^^^

- [CHANGE] initial public release
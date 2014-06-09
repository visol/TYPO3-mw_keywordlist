.. include:: Images.txt

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


Reference
^^^^^^^^^

Available configuration options for this extension:

.. ### BEGIN~OF~TABLE ###

.. container:: table-row

   Property
         Property:

   Data type
         Data type:

   Description
         Description:

   Default
         Default:


.. container:: table-row

   Property
         allWrap /+stdWrap

   Data type
         wrap

   Description
         Wraps the whole item

   Default


.. container:: table-row

   Property
         contentWrap

   Data type
         wrap

   Description
         Wraps the whole content

   Default
         <div class="tx-mwkeywordlist-pi1-content">\|</div>


.. container:: table-row

   Property
         sectionHeaderWrap

   Data type
         wrap

   Description
         Wraps the sections headers [2]

   Default
         <h2>\|</h2>


.. container:: table-row

   Property
         sectionWrap

   Data type
         string

   Description
         Wraps the complete section

   Default
         <div class="section">\|</div>


.. container:: table-row

   Property
         keywordSectionWrap

   Data type
         string

   Description
         Wraps the keyword section

   Default
         <div>\|</div>


.. container:: table-row

   Property
         keywordWrap

   Data type
         string

   Description
         Wraps the individual keyword [3]

   Default
         <strong>\|</strong>


.. container:: table-row

   Property
         keywordRelationListWrap

   Data type
         string

   Description
         Wraps the relates links list

   Default
         <ul>\|</ul>


.. container:: table-row

   Property
         keywordRelationListItemWrap

   Data type
         string

   Description
         Wraps the individual link [4]

   Default
         <li>\|</li>


.. container:: table-row

   Property
         jumpMenuSeperator

   Data type
         string

   Description
         The character to separate the jump links

   Default
         &#124; the pipe “\|”


.. container:: table-row

   Property
         sectionTopLink

   Data type
         string

   Description
         Text (or image) for the toplink [5]

   Default
         To top


.. container:: table-row

   Property
         sectionTopLinkWrap

   Data type
         string

   Description
         Wraps the toplink

   Default
         <div class="sectiontoplink"><a href="#top">\|</a></div>


.. container:: table-row

   Property
         showSectionTopLinks

   Data type
         boolean

   Description
         Display the toplink

   Default
         1


.. container:: table-row

   Property
         bullet

   Data type
         string

   Description
         Bullet (image) to prepend each list item

   Default


.. container:: table-row

   Property
         levels

   Data type
         Int+

   Description
         Number of levels the plugin should recursively extract keywords

   Default
         5


.. ###### END~OF~TABLE ######

[tsref:plugin.tx\_mwkeywordlist\_pi1 ]


Screenshot
""""""""""

#. The jump menu. It is wrapped in a <div> and can be formatted
   individually. It is marked by the green dotted border.

#. The section header. A section consists of the header [2], the
   keyword(s) [3] and the links [4]. The section is marked by the red
   dotted border.

#. The Keyword. Each keyword can be wrapped.

#. The keyword related link to a page.

#. The top link which is inserted after every section.

|img-4|


Example
~~~~~~~

This will give you a standard index with jump menu and top links,
where each link is wrappedby a <br>-tag and the keywords are displayed
in a strong italic font.

::

   plugin.tx_mwkeywordlist_pi1 {

           contentWrap  =  <div class="tx-mwkeywordlist-pi1-content">|</div>
           sectionHeaderWrap  =  <h2>|</h2>
           sectionWrap  =  <div class="section">|</div>
           keywordSectionWrap  =  <div>|</div>
           keywordWrap  =  <strong><i>|</i></strong>
           keywordRelationListWrap  =
           keywordRelationListItemWrap  =  |<br/>
           jumpMenuSeperator  =  &#124;
           sectionTopLink  =  To the page top
           sectionTopLinkWrap  =  <div class="sectiontoplink"><a href="#top">|</a></div>
           showSectionTopLinks  =  1
           bullet  =
           levels  =  3

   }


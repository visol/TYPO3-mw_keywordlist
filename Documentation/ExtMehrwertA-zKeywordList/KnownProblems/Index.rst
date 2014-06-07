

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


Known problems
--------------

This extension may cause performance problems on large indexes and
huge page trees because it recursively selects all pages from the tree
and resolves the links to related pages. This behaviormight cause
performance issues under certain circumstances such as

- too frequent use of this function

- slow hardware (database server)

- large page trees



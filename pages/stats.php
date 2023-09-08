<?php

$list = rex_list::factory("select term, resultcount from " . rex::getTable('simplesearch_searchterms'), 3000, null, false);
$list->setColumnLabel('term', 'Suchbegriff');
$list->setColumnLabel('resultcount', 'Anzahl');
$list->setColumnSortable('term');
$list->setColumnSortable('resultcount');

$fragment = new rex_fragment();
$fragment->setVar('title', 'Statistiken zu Suchanfragen');
$fragment->setVar('content', $list->get(), false);
echo $fragment->parse('core/page/section.php');

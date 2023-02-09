<?php

use AndiLeni\search\Search;


if (rex::isBackend()) {
    rex_extension::register('ART_CONTENT_UPDATED', 'AndiLeni\search\Search::update_article_ep');

    // rex_extension::register('ART_STATUS', function ($ep) {
    //     rex_logger::logError(0, 'ART_STATUS', '', 0);
    //     rex_logger::logError(0, $ep->params['id'], '', 0);
    //     rex_logger::logError(0, $ep->params['status'], '', 0);
    // });

    rex_extension::register('CAT_STATUS', 'AndiLeni\search\Search::status_update');
    rex_extension::register('ART_STATUS', 'AndiLeni\search\Search::status_update');
}

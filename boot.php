<?php

use AndiLeni\search\Search;


if (rex::isBackend()) {
    rex_extension::register('ART_CONTENT_UPDATED', function ($ep) {
        $article =  $ep->getArticle();
        $article_id = $article->getId();

        $search = new Search;
        $search->update_article($article_id);
    });

    rex_extension::register('CAT_STATUS', function ($ep) {
        $search = new Search;
        $search->status_update($ep);
    });

    rex_extension::register('ART_STATUS', function ($ep) {
        $search = new Search;
        $search->status_update($ep);
    });
}

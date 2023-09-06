<?php

use AndiLeni\search\Search;

$query = rex_post('query', 'string', '');

if ($query !== '') {

    $search = new Search();

    $res = $search->search($query);

    dump($res);

    $results_html = '';
    foreach ($res['hits'] as $hit) {
        $results_html .= '<h3>' . $hit["name"] . '</h3>';
        $results_html .= '<a href="' . $search->get_article_url($hit["id"]) . '">zum Artikel</a>';
        $results_html .= '<hr>';
    }

    $fragment = new rex_fragment();
    $fragment->setVar('title', 'Suchergebnisse - Exakte Suche', false);
    $fragment->setVar('body', $results_html, false);
    echo $fragment->parse('core/page/section.php');
}

?>

<div class="panel panel-edit">

    <header class="panel-heading">
        <div class="panel-title">Suchergebnisse</div>
    </header>

    <div class="panel-body">
        <form method="post">
            <div class="form-group">
                <label>Suchbegriff:</label>
                <input class="form-control" type="text" name="query">
            </div>
            <div class="form-group">
                <button class="btn btn-primary" type="submit">Suchen</button>
            </div>
        </form>
    </div>

</div>
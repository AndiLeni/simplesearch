# simplesearch

<p>Dieses Addon stellt eine einfache Suche zur Verfügung.</p>
<p>Um das Addon möglichst einfach zu halten, indiziert die Suche standardmäßig nur Artikel und deren 
Inhalte, nicht aber YForm Tabellen, Medien o.ä. 
(Die verwendete Search Engine [loupe](https://github.com/loupe-php/loupe) kann aber auch dafür genutzt 
werden wenn man weiß was man tut).</p>

<blockquote>
    <b><i>"Wenn search_it zu viel, keine Suche aber zu wenig ist."</i></b>
</blockquote>


## Verwendung:

````php
use AndiLeni\search\Search;

$search = new Search();
$res = $search->search($query);


// liefert ein ergebnis wie folgt:
array:7 [▼
    "hits" => array:1 [▼
        0 => array:4 [▼
            "id" => 5
            "name" => "Pythagoras"
            "content" => "Pythagoras von Samos..."
            "_rankingScore" => -0.28311
        ]
    ]
    "query" => "pythagoras"
    "processingTimeMs" => 34
    "hitsPerPage" => 20
    "page" => 1
    "totalPages" => 1
    "totalHits" => 1
]

````


## Beispiel Modul:

Modulausgabe:
````php
<?php

use AndiLeni\search\Search;

$query = rex_get('ssearch_query', 'string', null);


if ($query == null) {
    echo <<<FORM
    <form action="" method="get">
        <input type="text" name="ssearch_query" placeholder="Suchbegriff eingeben...">
        <button type="submit">Suchen</button>
    </form>
    FORM;
} else {
    $search = new Search();
    $result = $search->search($query);

    $results_html = '<h1>Suchergebnisse:</h1>';
    foreach ($result['hits'] as $hit) {
        $results_html .= '<h3>' . $hit["name"] . '</h3>';
        $results_html .= '<a href="' . $search->get_article_url($hit["id"]) . '">zum Artikel</a>';
        $results_html .= '<hr>';
    }
    echo $results_html;
}

````

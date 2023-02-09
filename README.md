# simplesearch

<p>Dieses Addon stellt eine einfache Suche zur Verfügung.</p>
<p>Diese kann nur auf Artikel und deren Inhalte zugreifen (nicht auf YForm Tabellen, Medien o.ä.).</p>

<blockquote>
    <b><i>"Wenn search_it zu viel, keine Suche aber zu wenig ist."</i></b>
</blockquote>


## Verwendung:

````php
use AndiLeni\search\Search;


$res = Search::search($query);
// oder
$res = Search::search_fuzzy($query);

// liefert ein ergebnis wie folgt:
array:4 [▼
    "ids" => array:3 [▼ // artikel-ids
        0 => 9
        1 => 10
        2 => 8
    ]
    "hits" => 3
    "docScores" => array:3 [▼
        9 => 2.2663017493194
        10 => 2.0066213405432
        8 => 1.6052970724346
    ]
    "execution_time" => "0.5281 ms"
]

````
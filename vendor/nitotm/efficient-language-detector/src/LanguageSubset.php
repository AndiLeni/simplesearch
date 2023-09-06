<?php
/**
 * @copyright 2023 Nito T.M.
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @author Nito T.M. (https://github.com/nitotm)
 * @package nitotm/efficient-language-detector
 */

declare(strict_types=1);

namespace Nitotm\Eld;

require_once __DIR__ . '/SubsetResult.php';

class LanguageSubset
{
    protected ?array $subset = null;
    protected ?string $loadedSubset = null;
    protected array $ngrams = [];
    protected array $langCodes = [];
    protected string $dataType;
    protected string $ngramsFolder;
    private ?array $defaultNgrams = null;

    /**
     * When active, detect() will filter the languages not included at $subset, from the scores, with filterLangSubset()
     */
    public function dynamicLangSubset(?array $languages = null): SubsetResult
    {
        if ($languages) {
            $this->subset = $this->makeSubset($languages);
            if (!$this->subset) {
                return new SubsetResult(false, null, 'No language matched this set');
            }
        } else {
            $this->subset = null;
        }

        return new SubsetResult(true, ($this->subset ? $this->isoLanguages($this->subset) : null));
    }

    protected function makeSubset(array $languages): ?array
    {
        $subset = [];
        if ($languages) {
            foreach ($languages as $lang) {
                $foundLang = array_search($lang, $this->langCodes, true);
                if ($foundLang !== false) {
                    $subset[] = $foundLang;
                }
            }
            sort($subset);
        }

        return ($subset ?: null);
    }

    /**
     * Converts ngram database language indexes (integer) to ISO 639-1 code
     */
    protected function isoLanguages(array $langSet): array
    {
        $newLangCodes = [];
        foreach ($langSet as $langID) {
            $newLangCodes[$langID] = $this->langCodes[$langID];
        }
        return $newLangCodes;
    }

    /**
     * Removes the excluded languages form the ngrams database
     * if $save option is true, the new ngrams subset will be stored, and cached for next time
     */
    public function langSubset(?array $languages = null, bool $save = true, bool $encode = true): SubsetResult
    {
        if (!$languages) {
            if ($this->loadedSubset && $this->defaultNgrams) {
                $this->ngrams = $this->defaultNgrams;
                $this->loadedSubset = null;
                return new SubsetResult(true);
            }
            return new SubsetResult(true); // there was already no subset to disable, so it is successful
        }

        $langArray = $this->makeSubset($languages);
        if (!$langArray) {
            return new SubsetResult(false, null, 'No language matched this set');
        }

        if ($this->defaultNgrams === null) {
            $this->defaultNgrams = $this->ngrams;
        }

        $new_subset = base_convert(hash('sha1', implode(',', $langArray)), 16, 36);
        $file_name = 'ngrams' . $this->dataType .'-'. count($langArray).(!$encode ? '.d':'') .'.'. $new_subset . '.php';
        $file_path = $this->ngramsFolder .  'subset/' . $file_name;
        // TODO if default loaded ngrams are already a subset (and lack languages): send warning or load main database
        if ($this->loadedSubset !== $new_subset) {
            $this->loadedSubset = $new_subset;

            if (file_exists($file_path)) {
                $ngramsData = include $file_path;
                if (isset($ngramsData['ngrams'])) {
                    $this->ngrams = $ngramsData['ngrams']; // 'type' is the same; full $langCodes array is compatible

                    return new SubsetResult(true, $this->isoLanguages($langArray), null, $file_name);
                }
            }
            if ($this->ngrams !== $this->defaultNgrams) {
                $this->ngrams = $this->defaultNgrams;
            }

            foreach ($this->ngrams as $ngram => $langsID) {
                foreach ($langsID as $id => $value) {
                    if (!in_array($id, $langArray, true)) {
                        unset($this->ngrams[$ngram][$id]);
                    }
                }
                if (!$this->ngrams[$ngram]) {
                    unset($this->ngrams[$ngram]);
                }
            }
        }

        if ($save) {
            $saved = $this->saveNgrams($file_path, $langArray, $encode);
            return new SubsetResult(true, $this->isoLanguages($langArray), null, ($saved ? $file_name : null));
        }

        return new SubsetResult(true);
    }

    protected function saveNgrams(string $file_path, array $langArray, bool $encode): bool
    {
        // in case $this->loadedSubset !== $new_subset, and was previously saved
        if (!file_exists($file_path) && !file_put_contents(
            $file_path,
            '<?php' . "\r\n" // Not using PHP_EOL, so the file is formatted in all SO
            . '// Copyright 2023 Nito T.M. [ Apache 2.0 Licence https://www.apache.org/licenses/LICENSE-2.0 ]' . "\r\n"
            . ( !$encode ? '// Do not edit unless you ensure you are using UTF-8 encoding' . "\r\n" : '' )
            . "return [" . "\r\n"
            . "'type' => '" . $this->dataType . "'," . "\r\n"
            . "'languages' => " . var_export($this->isoLanguages($langArray), true) . "," . "\r\n"
            . "'isSubset' => true," . "\r\n"
            . "'ngrams' =>" . $this->ngramExport($this->ngrams, $encode) . "\r\n"
            . "];"
        )) {
            return false;
        }
        return true;
    }

    /**
     * @param array|int $data
     */
    protected function ngramExport($data, bool $encode = false): ?string
    {
        if (is_array($data)) {
            $toImplode = array();
            foreach ($data as $key => $value) {
                $toImplode[] = ($encode === true ?
                        '"\\x' . substr(chunk_split(bin2hex($key), 2, '\\x'), 0, -2) . '"'
                        : var_export($key, true)
                    ) . '=>' . $this->ngramExport($value);
            }

            return '[' . implode(',', $toImplode) . ']';
        }

        return var_export($data, true);
    }

    /**
     * Filters languages not included in the subset, from the result scores
     */
    protected function filterLangSubset(array &$scores): array
    {
        if ($this->subset) {
            foreach ($scores as $langID => $score) {
                if (!$score || !in_array($langID, $this->subset, true)) {
                    unset($scores[$langID]);
                }
            }
        }
        return $scores;
    }
}

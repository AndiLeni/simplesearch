<?php
/**
 * @copyright 2023 Nito T.M.
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @author Nito T.M. (https://github.com/nitotm)
 * @package nitotm/efficient-language-detector
 */

declare(strict_types=1);

namespace Nitotm\Eld;

final class SubsetResult
{
    public bool $success;
    public ?array $languages;
    public ?string $error;
    public ?string $file;

    public function __construct(bool $success, ?array $languages = null, ?string $error = null, ?string $file = null)
    {
        $this->success = $success;
        $this->languages = $languages;
        $this->error = $error;
        $this->file = $file;
    }
}

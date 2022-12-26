<?php
namespace Ynotz\EasyAdmin\Contracts;

use Illuminate\Http\Request;

interface ModelViewConnector
{
    public function index(
        int $itemsCount,
        int $page,
        array $searches,
        array $sorts,
        array $filters,
        array $advParams,
        string $selectedIds,
        string $resultsName = 'results'
    ): array;

    public function indexDownload(
        array $searches,
        array $sorts,
        array $filters,
        array $advParams,
        string $selectedIds
    ): array;

    public function getIdsForParams(
        array $searches,
        array $sorts,
        array $filters,
    ): array;

    public function suggestlist(Request $request);

    public function getDownloadCols(): array;

    public function getCreatePageData(): array;

    public function getStoreValidationRules(): array;

}
?>

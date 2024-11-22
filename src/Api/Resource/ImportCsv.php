<?php declare(strict_types=1);

namespace App\Api\Resource;

use ApiPlatform\Metadata\Post;
use App\Api\Action\ImportCsvAction;


#[Post(uriTemplate: '/import-csv', controller: ImportCsvAction::class, deserialize: false)]
class ImportCsv
{
}

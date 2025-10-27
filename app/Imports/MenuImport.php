<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;


use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;

class MenuImport implements ToCollection
{
    protected $fileContent;

    public function __construct(UploadedFile $file)
    {
        $this->fileContent = $file->get();
    }

    public function collection(Collection $collection)
    {
        return Excel::csv(null, function ($reader) {
            $reader->setInputEncoding('UTF-8');
        })->string($this->fileContent)->toArray();
    }
}

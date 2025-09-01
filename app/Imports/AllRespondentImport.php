<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
class AllRespondentImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    private $rows = 0;
    private $data_container = [];
    public function collection(Collection $rows)
    {
        $count = $this->rows;
        $data = array();
        $this->data_container = $rows->toArray();
    }
    public function DataContainer()
    {
        return $this->data_container;
    }
    public function getRowCount(): int
    {
        return $this->rows;
    }
    public function rules(): array
    {
        return [
            'p_email' => [ 'required','email'],
            'r_fname' => [ 'required'],
            'r_lname' => [],
            'r_email' => ['required','email'],
            'r_type' => ['required'],
        ];
    }
}

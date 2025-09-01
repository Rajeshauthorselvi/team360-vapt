<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class QuestionImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    private $data_container = [];
    public function collection(Collection $rows)
    {
        $this->data_container = $rows;
    }
    public function DataContainer()
    {
        return $this->data_container;
    }
    public function getRowCount(): int
    {
        return $this->rows;
    }
    // public function rules(): array
    // {
    //     return [
    //         'question_text' => ['required'],
    //         'question_type' => ['required'],
    //         'question_required' => ['required'],
    //         'question_dimension' => ['required'],
    //         'display_order' => ['required'],
    //         'display_order' => ['options'],
    //     ];
    // }
}

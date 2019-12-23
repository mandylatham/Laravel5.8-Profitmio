<?php

namespace App\Services;

class RecipientListExportFormatter
{
    private $columnsToExport = [
        'id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address1',
        'city',
        'state',
        'zip',
        'year',
        'make',
        'model',
        'vin'
    ];

    private $recipients = [];

    private $rows = null;

    public function __construct($recipients)
    {
        $this->recipients = $recipients;
    }

    public function getColumnHeaders()
    {
        return $this->columnsToExport;
    }

    public function getRows()
    {
        if (!$this->rows) {
            foreach ($this->recipients as $recipient) {
                $row = [];
                foreach ($this->columnsToExport as $column) {
                    $row[] = $recipient->$column ?? null;
                }
                $this->rows[] = $row;
            }
        }
        return $this->rows;
    }
}

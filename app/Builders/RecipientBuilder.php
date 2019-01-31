<?php
namespace App\Builders;

use App\Models\Recipient;
use App\Models\RecipientList;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RecipientBuilder
{
    /**
     * List of fields to be used in field mapping
     * @var array
     */
    public $listFields = [
            'address1', 'city', 'state', 'zip', 'year',
            'phone', 'make', 'model', 'vin',
        ];

    /**
     * Create new recipients from an uploaded list
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Campaign            $campaign
     * @param \App\Models\RecipientList       $list
     * @throws \Exception
     */
    public function createFromNewList(RecipientList $list)
    {
        // Add recipients to the list
        if ($list->recipients_added) {
            return;
        }

        if ($list->type == 'mixed') {
            array_push($this->listFields, 'is_database');
        }

        $media = $list->getMedia('recipient-lists')->first();
        if (! $media) {
            throw new \Exception("Campaign {$list->campaign_id}: Unable to locate the media object for list {$list->id}");
        }
        if (! $this->makeMediaCopy($media)) {
            throw new \Exception("Campaign {$list->campaign_id}: Unable to download the media object for list {$list->id}");
        }
        \Log::debug("RecipientBuilder: found uploaded file");

        $file = fopen(Storage::disk('local')->path($media->file_name), 'r');
        $headers = fgetcsv($file);
        $rows = [];
        $i = 1;
        while (($row = fgetcsv($file)) !== false) {
            if (count($headers) != count($row)) {
                \Log::debug("RecipientBuilder: Invald CSV - column count mismatch at row {$i}");
                throw new \Exception("Invalid CSV - column count mismatch at row {$i}");
            }
            $row = array_combine($headers, $row);
            $staging = [];
            $staging['campaign_id'] = $list->campaign_id;
            $staging['recipient_list_id'] = $list->id;
            $staging['first_name'] = $this->sanitize($row[$list->fieldmap['first_name']], true);
            $staging['last_name'] = $this->sanitize($row[$list->fieldmap['last_name']], true);
            if (array_key_exists('email', $list->fieldmap)) {
                $staging['email'] = '';
                if (filter_var($row[$list->fieldmap['email']], FILTER_VALIDATE_EMAIL)) {
                    $staging['email'] = $row[$list->fieldmap['email']];
                }
            }
            if ($list->type == 'database') {
                $staging['from_dealer_db'] = true;
            } elseif ($list->type == 'mixed') {
                if ($row[$list->fieldmap['is_database']] == 'D') {
                    $staging['from_dealer_db'] = true;
                }
            }
            \log::debug("recipientbuilder: about to iterate fields");
            foreach ($this->listFields as $field) {
                if (array_key_exists($field, $list->fieldmap)) {
                    $staging[$field] = $this->sanitize($row[$list->fieldmap[$field]], true);
                }
            }
            \log::debug("recipientbuilder: iteration complete");
            $rows[] = $staging;

            if ($i % 100 == 0) {
                if (! Recipient::insert($rows)) {
                    \Log::debug("RecipientBuilder: Unable to load recipients - cannot add to db");
                    throw new \Exception("Unable to load recipients - cannot add to db");
                }
                \Log::debug("Processing file for list {$list->id}: inserting ". count($rows) ." records");
                $rows = [];
            }

            $i++;
        }

        if (! Recipient::insert($rows)) {
            \Log::debug("Processing file for list {$list->id}: inserting ". count($rows) ." records");
            throw new \Exception("Unable to load recipients - cannot add to db");
        }
        \Log::debug("Processing file for list {$list->id}: inserting ". count($rows) ." records");

        $list->update([
            'recipients_added' => true,
        ]);
    }

    /**
     * Handle local files differently due to path issue
     */
    protected function makeMediaCopy($media)
    {
        if ($media->disk == 'local') {
            return Storage::disk('local')->put($media->file_name, Storage::disk('local')->get($media->id . '/' . $media->file_name));
        }

        return Storage::disk('local')->put($media->file_name, Storage::disk($media->disk)->get($media->getPath()));
    }

    public function sanitize($value, $correctCase = false) {
        if ($correctCase) {
            $value = ucwords(strtolower($value));
        }

        return Str::ascii(filter_var($value, FILTER_SANITIZE_STRING));
    }
}

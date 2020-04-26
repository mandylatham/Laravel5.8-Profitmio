<?php
namespace App\Builders;

use App\Models\Recipient;
use App\Models\RecipientList;
use Illuminate\Support\Str;
use QrCode;
use Storage;
use Illuminate\Http\File;

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

        foreach ($list->fieldmap as $key => $value) {
            if ($value === null) {
                unset($this->listFields[$key]);
            }
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
        $i = 1;
        while (($row = fgetcsv($file)) !== false) {
            if (count($headers) != count($row)) {
                \Log::debug("RecipientBuilder: Invald CSV - column count mismatch at row {$i}");
                throw new \Exception("Invalid CSV - column count mismatch at row {$i}");
            }
            $row = array_combine($headers, $row);

            $recipient = new Recipient();
            $recipient->campaign_id = $list->campaign_id;
            $recipient->recipient_list_id = $list->id;
            $recipient->first_name = $this->sanitize($row[$list->fieldmap['first_name']], true);
            $recipient->last_name = $this->sanitize($row[$list->fieldmap['last_name']], true);
            if (array_key_exists('email', $list->fieldmap)) {
                $recipient->email = '';
                if (filter_var($row[$list->fieldmap['email']], FILTER_VALIDATE_EMAIL)) {
                    $recipient->email = $row[$list->fieldmap['email']];
                }
            }

            if ($list->type == 'database') {
                $recipient->from_dealer_db = true;
            } elseif ($list->type == 'mixed') {
                if ($row[$list->fieldmap['is_database']] == 'D') {
                    $recipient->from_dealer_db = true;
                }
            }
            foreach ($this->listFields as $field) {
                if (array_key_exists($field, $list->fieldmap) && $list->fieldmap[$field] !== null) {
                    $recipient->$field = $this->sanitize($row[$list->fieldmap[$field]], true);
                }
            }
            $recipient->phone = preg_replace('/[^0-9+]+/', '', $recipient->phone);
            try {
                $recipient->save();

                // Create text to value code and value
                if (
                    array_key_exists('text_to_value_code', $list->fieldmap) &&
                    isset($row[$list->fieldmap['text_to_value_code']]) &&
                    $row[$list->fieldmap['text_to_value_code']] !== '' &&
                    array_key_exists('text_to_value_amount', $list->fieldmap) &&
                    isset($row[$list->fieldmap['text_to_value_amount']]) &&
                    $row[$list->fieldmap['text_to_value_amount']] !== ''
                ) {
                    $textToValue = $recipient->textToValue()->create([
                        'checked_in' => false,
                        'text_to_value_code' => $row[$list->fieldmap['text_to_value_code']],
                        'text_to_value_amount' => $row[$list->fieldmap['text_to_value_amount']],
                    ]);

                    // Generate qr and upload to s3
                    $filePath = storage_path('app/' . Str::random(15) . '.png');
                    QrCode::format('png')
                        ->size(200)
                        ->margin(5)
                        ->errorCorrection('M')
                        ->generate($recipient->getCheckInUrl(), $filePath);
                    $path = Storage::disk('media')->putFile('text-to-value', new File($filePath), 'public');

                    $recipient->qrCode()->create([
                        'image_url' => Storage::disk('media')->url($path)
                    ]);
                }

            } catch (\Exception $e) {
                \Log::debug("RecipientBuilder: Unable to load recipients - cannot add to db, error" . $e->getMessage());
                throw new \Exception("Unable to load recipients - cannot add to db");
            }
        }

        $list->update([
            'recipients_added' => true,
        ]);

        $i++;
    }

    /**
     * Handle local files differently due to path issue
     */
    protected function makeMediaCopy($media)
    {
        if ($media->disk == 'local') {
            \Log::debug("RecipientBuilder: adding local file to local directory");
            return Storage::disk('local')->put($media->file_name, Storage::disk('local')->get($media->id . '/' . $media->file_name));
        }

        \Log::debug("RecipientBuilder: adding {$media->disk} file to local directory");
        return Storage::disk('local')->put($media->file_name, Storage::disk($media->disk)->get($media->getPath()));
    }

    public function sanitize($value, $correctCase = false) {
        if ($correctCase) {
            $value = ucwords(strtolower($value));
        }

        return Str::ascii(filter_var($value, FILTER_SANITIZE_STRING));
    }
}

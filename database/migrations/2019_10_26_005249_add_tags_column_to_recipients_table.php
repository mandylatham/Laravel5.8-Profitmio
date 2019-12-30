<?php

use App\Models\Recipient;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTagsColumnToRecipientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recipients', function (Blueprint $table) {
            $table->json("tags")->nullable()->after("vin");
        });

        Recipient::whereStatus('closed-lead')->chunk(100, function ($recipients) {
            foreach ($recipients as $recipient) {
                $tags = [];
                foreach (['service', 'appointment', 'heat', 'interested', 'not_interested', 'wrong_number', 'car_sold', 'callback'] as $label) {
                    if ($recipient->$label) {
                        $tags[] = $label;
                    }
                }
                $recipient->tags = $tags;
                $recipient->save();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recipients', function (Blueprint $table) {
            $table->dropColumn("tags");
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTextColumnsToDishes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("dishes", function (Blueprint $table) {
            $table->text("allergies")->nullable();
            $table->text("variations")->nullable();
            $table->text("recipe")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("dishes", function (Blueprint $table) {
            $table->dropColumn("allergies");
            $table->dropColumn("variations");
            $table->dropColumn("recipe");
        });
    }
}

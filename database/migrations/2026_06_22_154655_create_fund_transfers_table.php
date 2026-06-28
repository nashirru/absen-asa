<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("fund_transfers", function (Blueprint $table) {
            $table->id();
            $table->foreignId("from_account_id")->constrained("accounts")->cascadeOnDelete();
            $table->foreignId("to_account_id")->constrained("accounts")->cascadeOnDelete();
            $table->decimal("amount", 15, 2);
            $table->date("date");
            $table->text("note")->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("fund_transfers");
    }
};

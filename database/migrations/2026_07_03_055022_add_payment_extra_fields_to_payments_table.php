<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('customer_email')->nullable()->after('amount');
            $table->decimal('refunded_amount', 10, 2)->default(0)->after('status');
            $table->string('refund_status')->nullable()->after('refunded_amount');
            $table->string('promo_code')->nullable()->after('refund_status');
            $table->unsignedInteger('retry_count')->default(0)->after('promo_code');
            $table->boolean('invoice_sent')->default(false)->after('retry_count');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'customer_email',
                'refunded_amount',
                'refund_status',
                'promo_code',
                'retry_count',
                'invoice_sent',
            ]);
        });
    }
};
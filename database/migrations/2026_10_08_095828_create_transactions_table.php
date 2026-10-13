<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->enum('type', ['di_tempat', 'po'])->default('di_tempat');
            $table->foreignId('cashier_id')->constrained('users')->onDelete('cascade');
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('payment_method')->default('qris');
            $table->string('payment_status')->default('paid'); // paid, pending, cancelled
            $table->string('payment_proof')->nullable(); // path to camera captured receipt image
            $table->string('po_token')->nullable()->unique(); // Unique QR code identifier for PO pickup
            $table->enum('pickup_status', ['none', 'menunggu_pengambilan', 'sudah_diambil'])->default('none');
            $table->timestamp('picked_up_at')->nullable();
            $table->foreignId('picked_up_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};

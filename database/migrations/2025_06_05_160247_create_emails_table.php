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
        Schema::create('emails', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number');
            $table->string('from_email');
            $table->string('from_name')->nullable();
            $table->string('to_email');
            $table->string('to_name')->nullable();
            $table->string('sender_domain')->nullable(); 
            $table->string('status')->default('unread'); // 'unread', 'read', 'replied'
            $table->timestamp('read_at')->nullable();
            $table->foreignId('read_by')->nullable()->constrained('users', 'id');
            $table->string('priority')->default('normal'); // 'low', 'normal', 'high'
            $table->string('label')->nullable(); // e.g., 'support', 'sales', etc.  
            $table->string('folder')->default('INBOX'); // e.g., 'INBOX', 'Sent', 'Drafts'
            $table->longText('subject');
            $table->longText('body');
            $table->string('direction'); // 'incoming' or 'outgoing'
            $table->longText('message_id')->nullable();
            $table->unsignedBigInteger('imap_uid')->nullable();
            $table->string('in_reply_to')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emails');
    }
};

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Infrastructure\Persistence\Eloquent\EmailTemplate;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default Notification Template
        EmailTemplate::firstOrCreate(
            ['name' => 'Default Notification'],
            [
                'type' => 'notification',
                'subject' => '新しい問い合わせがありました',
                'body' => "フォーム名: {form_name}\n送信者: {sender_email}\n内容: {inquiry_data}",
                'is_default' => true,
            ]
        );

        // Default Auto-Reply Template
        EmailTemplate::firstOrCreate(
            ['name' => 'Default Auto-Reply'],
            [
                'type' => 'auto_reply',
                'subject' => 'お問い合わせありがとうございます',
                'body' => "{sender_name}様\n\nお問い合わせありがとうございます。\n以下の内容でお問い合わせを受け付けました。\n\nフォーム名: {form_name}\n内容: {inquiry_data}\n\n後ほど担当者よりご連絡いたします。",
                'is_default' => true,
            ]
        );
    }
}

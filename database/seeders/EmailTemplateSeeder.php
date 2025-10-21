<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default Notification Template
        EmailTemplate::firstOrCreate(
            ['name' => 'Default Notification Template', 'type' => 'notification'],
            [
                'subject' => '新しいお問い合わせがありました',
                'body' => "ウェブサイトから新しいお問い合わせがありました。\n\nお問い合わせ内容:\n{inquiry_data}\n",
                'is_default' => true,
            ]
        );

        // Default Auto Reply Template
        EmailTemplate::firstOrCreate(
            ['name' => 'Default Auto Reply Template', 'type' => 'auto_reply'],
            [
                'subject' => 'お問い合わせありがとうございます',
                'body' => "{user_name}様\n\nお問い合わせありがとうございます。内容を確認後、改めてご連絡させていただきます。\n\nお問い合わせ内容:\n{inquiry_data}\n",
                'is_default' => true,
            ]
        );
    }
}
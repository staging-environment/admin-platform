<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Look up deleted user
$oldUser = \App\Models\User::withTrashed()->where('email', 'alfonsogaro@gmail.com')->first();
if (!$oldUser) {
    echo "Old user (alfonsogaro@gmail.com) not found, even in trashed.\n";
    exit(1);
}

echo "Old User found: " . $oldUser->name . " (Deleted at: " . ($oldUser->deleted_at ?? 'Not deleted') . ")\n";
echo "Old User Telegram Chat ID: " . ($oldUser->telegram_chat_id ?? 'NULL') . "\n";

if (empty($oldUser->telegram_chat_id)) {
    echo "Old user does not have a Telegram Chat ID to migrate.\n";
    exit(0);
}

// Find new user
$newUser = \App\Models\User::where('email', 'utrecar@gmail.com')->first();
if (!$newUser) {
    echo "New user (utrecar@gmail.com) not found.\n";
    exit(1);
}

echo "Migrating Telegram Chat ID to: " . $newUser->name . " (" . $newUser->email . ")\n";
$newUser->telegram_chat_id = $oldUser->telegram_chat_id;
$newUser->save();

echo "Migration completed successfully!\n";

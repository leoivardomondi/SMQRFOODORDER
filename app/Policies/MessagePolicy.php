<?php
namespace App\Policies;
use App\Models\Message;
use App\Models\User;
class MessagePolicy
{
    public function view(User $user, Message $message): bool { return $message->user_id === $user->id; }
    public function delete(User $user, Message $message): bool { return $this->view($user, $message); }
}

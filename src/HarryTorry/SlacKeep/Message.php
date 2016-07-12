<?php
namespace HarryTorry\SlacKeep;

use Illuminate\Database\Eloquent\Model;

class Message extends Model {
    protected $table = 'messages';
    protected $fillable = ['reply_to', 'type', 'channel', 'user', 'text', 'ts'];
}

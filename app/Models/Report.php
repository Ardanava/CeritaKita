<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
  protected $fillable = [
    'user_id','story_id','chapter_id','category','description',
    'page_url','user_agent','ip_address','status'
  ];
}

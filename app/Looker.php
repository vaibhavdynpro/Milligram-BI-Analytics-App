<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Looker extends Model
{
    protected $table = 'looker';
    protected $fillable = [ 'api_url', 'client_id', 'client_secret', 'secret', 'host'];
}

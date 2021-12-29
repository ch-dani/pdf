<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Document extends Model {

    protected $table = 'documents';
    protected $primaryKey = 'id';

    protected $fillable = ['user_id', 'UUID', 'operation_id','share_id', 'download_name',
    'is_copy', 'operation_type', 'original_document', 'edited_document', 'operation_type', 'original_name', 'original_name', "delete_after"];

}

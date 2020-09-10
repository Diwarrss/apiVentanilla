<?php

namespace App;
use Storage;
use Illuminate\Database\Eloquent\Model;

class EntryFiling extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'entry_filings';

    protected $fillable = [
        'date',
        'cons_year',
        'year',
        'title',
        'settled',
        'access_level',
        'means_document',
        'folios',
        'annexes',
        'subject',
        'key_words',
        'attachments',
        'state',
        'user_id',
        'campus_id',
        'priority_id',
        'people_id',
        'type_document_id',
        'context_type_id'
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'campus_id' => 'integer',
        'priority_id' => 'integer',
        'people_id' => 'integer',
        'type_document_id' => 'integer',
        'context_type_id' => 'integer',
    ];


    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function campus()
    {
        return $this->belongsTo(\App\Campus::class);
    }

    public function priority()
    {
        return $this->belongsTo(\App\Priority::class);
    }

    public function people()
    {
        return $this->belongsTo(\App\People::class);
    }

    public function typeDocument()
    {
        return $this->belongsTo(\App\TypeDocument::class);
    }

    public function contextType()
    {
        return $this->belongsTo(\App\ContextType::class);
    }

    public function dependences()
    {
        return $this->belongsToMany(\App\Dependence::class, 'entry_filing_has_dependences');
    }

    public function upFiles()
    {
        return $this->morphMany(\App\UpFile::class, 'fileable');
    }
}

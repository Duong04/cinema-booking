<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

trait UsesUuidV7
{
    use HasUuids;

    protected function initializeUsesUuidV7()
    {
        $this->incrementing = false;
        $this->keyType = 'string';
    }

    public function newUniqueId()
    {
        return (string) Str::uuid7();
    }
}

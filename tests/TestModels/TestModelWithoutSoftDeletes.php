<?php

namespace ErnestoCh\UserAuditable\Tests\TestModels;

use ErnestoCh\UserAuditable\Traits\UserAuditable;
use Illuminate\Database\Eloquent\Model;

class TestModelWithoutSoftDeletes extends Model
{
    use UserAuditable;

    protected $table = 'test_models_without_soft_deletes';
    protected $guarded = [];
}

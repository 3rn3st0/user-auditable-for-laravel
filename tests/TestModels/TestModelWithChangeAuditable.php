<?php

namespace ErnestoCh\UserAuditable\Tests\TestModels;

use ErnestoCh\UserAuditable\Traits\ChangeAuditable;
use ErnestoCh\UserAuditable\Traits\EventAuditable;
use ErnestoCh\UserAuditable\Traits\UserAuditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestModelWithChangeAuditable extends Model
{
    use ChangeAuditable, EventAuditable, SoftDeletes, UserAuditable;

    protected $table = 'test_models_with_change_auditable';

    protected $guarded = [];

    protected $hidden = ['secret'];

    protected array $auditExclude = ['status'];
}

<?php

namespace ErnestoCh\UserAuditable\Tests\Feature;

use ErnestoCh\UserAuditable\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;

class SchemaMacrosTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Create user table for foreign keys
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        // Clean all tables after each test
        Schema::dropIfExists('test_table_1');
        Schema::dropIfExists('test_table_2');
        Schema::dropIfExists('test_table_3');
        Schema::dropIfExists('test_table_4');
        Schema::dropIfExists('users_uuid');
        Schema::dropIfExists('users');

        parent::tearDown();
    }

    #[Test]
    public function test_user_auditable_macro_creates_columns(): void
    {
        Schema::create('test_table_1', function (Blueprint $table) {
            $table->id();
            $table->userAuditable();
        });

        $this->assertTrue(Schema::hasColumns('test_table_1', [
            'created_by', 'updated_by', 'deleted_by'
        ]));
    }

    #[Test]
    public function test_full_auditable_macro_creates_all_columns(): void
    {
        Schema::create('test_table_2', function (Blueprint $table) {
            $table->id();
            $table->fullAuditable();
        });

        $this->assertTrue(Schema::hasColumns('test_table_2', [
            'created_at', 'updated_at', 'deleted_at',
            'created_by', 'updated_by', 'deleted_by'
        ]));
    }

    #[Test]
    public function test_user_auditable_with_uuid(): void
    {
        // Create a dedicated users table with UUID primary key for this test
        Schema::create('users_uuid', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('test_table_3', function (Blueprint $table) {
            $table->id();
            $table->userAuditable('users_uuid', 'uuid');
        });

        $columnType = Schema::getColumnType('test_table_3', 'created_by');

        // In SQLite, UUID is stored as 'varchar', in MySQL as 'char'
        $expectedType = config('database.default') === 'sqlite' ? 'varchar' : 'char';
        $this->assertEquals($expectedType, $columnType);
    }

    #[Test]
    public function test_drop_user_auditable_macro(): void
    {
        // Skip this test for SQLite due to database limitations
        if (config('database.default') === 'sqlite') {
            $this->markTestSkipped('SQLite does not support dropping foreign keys and columns reliably.');
            return;
        }

        Schema::create('test_table_4', function (Blueprint $table) {
            $table->id();
            $table->userAuditable();
        });

        Schema::table('test_table_4', function (Blueprint $table) {
            $table->dropUserAuditable();
        });

        $this->assertFalse(Schema::hasColumns('test_table_4', [
            'created_by', 'updated_by', 'deleted_by'
        ]));
    }
}

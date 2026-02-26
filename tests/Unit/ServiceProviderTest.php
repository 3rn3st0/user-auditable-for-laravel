<?php

namespace ErnestoCh\UserAuditable\Tests\Unit;

use ErnestoCh\UserAuditable\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ServiceProviderTest extends TestCase
{
    #[Test]
    public function it_registers_the_service_provider(): void
    {
        $providers = $this->app->getLoadedProviders();

        $this->assertArrayHasKey(
            'ErnestoCh\\UserAuditable\\Providers\\UserAuditableServiceProvider',
            $providers
        );
    }

    #[Test]
    public function it_registers_schema_macros(): void
    {
        $this->assertTrue(
            \Illuminate\Database\Schema\Blueprint::hasMacro('userAuditable')
        );

        $this->assertTrue(
            \Illuminate\Database\Schema\Blueprint::hasMacro('fullAuditable')
        );
    }

    #[Test]
    public function it_provides_config_file(): void
    {
        $config = config('user-auditable');

        $this->assertIsArray($config);
        $this->assertArrayHasKey('enabled_macros', $config);
    }
}

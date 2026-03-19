<?php

namespace JobMetric\Setting\Tests\Unit\Exceptions;

use JobMetric\Setting\Contracts\AbstractSetting;
use JobMetric\Setting\Exceptions\SettingClassInvalidException;
use JobMetric\Setting\Tests\TestCase;

/**
 * Unit tests for SettingClassInvalidException (Exceptions\SettingClassInvalidException).
 *
 * Purpose: Ensure the exception is thrown when a non-AbstractSetting class is used and message/code are correct.
 * Tests: Default message includes expected (AbstractSetting) and given class; custom message and code are accepted.
 */
class SettingClassInvalidExceptionTest extends TestCase
{
    /**
     * Exception message contains AbstractSetting class and given class name; code defaults to 400.
     */
    public function test_exception_has_default_message_with_expected_and_given(): void
    {
        $e = new SettingClassInvalidException('Invalid\\Class');

        $this->assertStringContainsString(AbstractSetting::class, $e->getMessage());
        $this->assertStringContainsString('Invalid\\Class', $e->getMessage());
        $this->assertSame(400, $e->getCode());
    }

    /**
     * Constructor accepts custom message and uses it instead of translation.
     */
    public function test_exception_accepts_custom_message(): void
    {
        $e = new SettingClassInvalidException('Foo', 'Custom message');

        $this->assertSame('Custom message', $e->getMessage());
    }

    /**
     * Constructor accepts custom exception code.
     */
    public function test_exception_accepts_custom_code(): void
    {
        $e = new SettingClassInvalidException('Foo', null, 422);

        $this->assertSame(422, $e->getCode());
    }
}

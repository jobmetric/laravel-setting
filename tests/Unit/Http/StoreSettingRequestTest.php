<?php

namespace JobMetric\Setting\Tests\Unit\Http;

use JobMetric\Setting\Http\Requests\StoreSettingRequest;
use JobMetric\Setting\Tests\TestCase;

/**
 * Unit tests for StoreSettingRequest (Http\Requests\StoreSettingRequest).
 *
 * Purpose: Ensure the form request authorizes and allows setting data for validation.
 * Tests: authorize() returns true; setData() sets the data property and returns $this for chaining.
 */
class StoreSettingRequestTest extends TestCase
{
    /**
     * authorize() returns true (request is always authorized in this context).
     */
    public function test_authorize_returns_true(): void
    {
        $request = new StoreSettingRequest;

        $this->assertTrue($request->authorize());
    }

    /**
     * setData() assigns the array to data property and returns the request instance.
     */
    public function test_setData_sets_data_and_returns_self(): void
    {
        $request = new StoreSettingRequest;
        $data = [
            'form_key' => 'value',
        ];

        $result = $request->setData($data);

        $this->assertSame($request, $result);
        $this->assertSame($data, $request->data);
    }
}

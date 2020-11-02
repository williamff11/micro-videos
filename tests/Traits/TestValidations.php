<?php

declare(strict_types=1);

namespace Tests\Traits;

use Illuminate\Foundation\Testing\TestResponse;

trait TestValidations
{
    protected function assertInvalidationInStoreAction(
        array $dataCreate,
        string $rule,
        $ruleParams = []
    ) {
        $response = $this->json('POST', $this->routeStore(), $dataCreate);
        $fields = array_keys($dataCreate);
        $this->assertInvalidationFields($response, $fields, $rule, $ruleParams);
    }

    protected function assertInvalidationInUpdateAction(
        array $dataUpdate,
        string $rule,
        $ruleParams = []
    ) {
        $response = $this->json('PUT', $this->routeUpdate(), $dataUpdate);
        $fields = array_keys($dataUpdate);
        $this->assertInvalidationFields($response, $fields, $rule, $ruleParams);
    }

    protected function assertInvalidationFields(
        TestResponse $response,
        array $fields,
        string $rule,
        array $ruleParams = []
    ) {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors($fields);

        foreach ($fields as $field) {
            $fieldname = str_replace('_', ' ', $field);
            $response->assertJsonFragment([
                \Lang::get("validation.{$rule}", ['attribute' => $fieldname] + $ruleParams)
            ]);
        }
    }
}

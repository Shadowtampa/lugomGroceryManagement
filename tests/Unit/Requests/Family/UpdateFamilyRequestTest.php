<?php

namespace Tests\Unit\Requests\Family;

use App\Http\Requests\Family\UpdateFamilyRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UpdateFamilyRequestTest extends TestCase
{
    private UpdateFamilyRequest $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new UpdateFamilyRequest();
    }

    public function test_validation_rules_are_correct()
    {
        $rules = $this->request->rules();

        $this->assertEquals([
            'nome' => 'required|string|max:255',
            'foto' => 'nullable|string|max:255'
        ], $rules);
    }

    public function test_validation_passes_with_valid_data()
    {
        $validator = Validator::make([
            'nome' => 'Família Teste Atualizada',
            'foto' => 'https://exemplo.com/nova-foto.jpg'
        ], $this->request->rules());

        $this->assertTrue($validator->passes());
    }

    public function test_validation_fails_without_required_fields()
    {
        $validator = Validator::make([
            'foto' => 'https://exemplo.com/nova-foto.jpg'
        ], $this->request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('nome', $validator->errors()->toArray());
    }

    public function test_validation_fails_with_invalid_data()
    {
        $validator = Validator::make([
            'nome' => 123, // Deve ser string
            'foto' => 456 // Deve ser string
        ], $this->request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('nome', $validator->errors()->toArray());
        $this->assertArrayHasKey('foto', $validator->errors()->toArray());
    }

    public function test_validation_passes_with_null_photo()
    {
        $validator = Validator::make([
            'nome' => 'Família Teste Atualizada',
            'foto' => null
        ], $this->request->rules());

        $this->assertTrue($validator->passes());
    }
}

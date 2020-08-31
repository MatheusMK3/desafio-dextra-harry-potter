<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CharacterControllerTest extends TestCase
{
    /**
     * Generates an invalid house ID
     */
    public function getInvalidHouseId() {
        return 'someinvalidhousegoeshere';
    }

    /**
     * Generates an valid house ID
     */
    public function getValidHouseId() {
        return '5a05e2b252f721a3cf2ea33f';
    }

    /**
     * Generates an valid house ID (alternative)
     */
    public function getValidHouseIdAlt() {
        return '5a05dc8cd45bd0a11bd5e071';
    }

    /** @test */
    public function indexShouldReturnPaginatedResponse()
    {
        // Fetches the Characters listing as JSON
        $response = $this->getJson(
            route('characters.index')
        );

        // Asserts this JSON contains a paginated response
        $response->assertJsonStructure([
            'data', 
            'meta' => [
                'current_page', 'last_page', 'per_page', 'total'
            ],
        ]);
    }
    
    /** @test */
    public function indexShouldReturnAllCharacters()
    {
        // Creates sample characters
        \App\Models\Character::create([
            'name' => 'Test One',
            'role' => 'student',
            'school' => 'Hogwarts School of Testing',
            'house' => $this->getValidHouseId(),
            'patronus' => 'test',
        ]);
        \App\Models\Character::create([
            'name' => 'Test Two',
            'role' => 'teacher',
            'school' => 'Hogwarts School of Testing',
            'house' => $this->getValidHouseId(),
            'patronus' => 'test',
        ]);
        \App\Models\Character::create([
            'name' => 'Test Three',
            'role' => 'wizard',
            'school' => 'Hogwarts School of Testing',
            'house' => $this->getValidHouseIdAlt(),
            'patronus' => 'assert',
        ]);

        // Fetches the Characters listing as JSON
        $response = $this->getJson(
            route('characters.index')
        );

        // Asserts the count includes all three sample characters
        $response->assertJsonCount(3, 'data');
    }
    
    /** @test */
    public function indexShouldAllowFilteringHouses()
    {
        // Creates sample characters
        \App\Models\Character::create([
            'name' => 'Test One',
            'role' => 'student',
            'school' => 'Hogwarts School of Testing',
            'house' => $this->getValidHouseId(),
            'patronus' => 'test',
        ]);
        \App\Models\Character::create([
            'name' => 'Test Two',
            'role' => 'teacher',
            'school' => 'Hogwarts School of Testing',
            'house' => $this->getValidHouseId(),
            'patronus' => 'test',
        ]);
        \App\Models\Character::create([
            'name' => 'Test Three',
            'role' => 'wizard',
            'school' => 'Hogwarts School of Testing',
            'house' => $this->getValidHouseIdAlt(),
            'patronus' => 'assert',
        ]);

        // Fetches the Characters listing as JSON
        $response1 = $this->getJson(
            route('characters.index', ['house' => $this->getValidHouseIdAlt()])
        );

        // Asserts the count only the alternative house character
        $response1->assertJsonCount(1, 'data');

        // Fetches the Characters listing as JSON
        $response2 = $this->getJson(
            route('characters.index', ['house' => $this->getValidHouseId()])
        );

        // Asserts the count only the default house character
        $response2->assertJsonCount(2, 'data');
    }
    
    /** @test */
    public function storeShouldNotAllowCreationWithMissingFields()
    {
        // Tries to create an invalid character (wrong house)
        $response = $this->postJson(
            route('characters.store'),
            [
                'name' => 'Some Invalid Person',
                'role' => 'student',
                'school' => 'Hogwarts School of Testing',
            ]
        );

        // Asserts the request fails with a 400 - Invalid Request error
        $response->assertStatus(422);

        // Asserts the request fails with proper error messages and descriptions
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'house', 'patronus',
            ],
        ]);
    }
    
    /** @test */
    public function storeShouldNotAllowCreationWithInvalidHouse()
    {
        // Tries to create an invalid character (wrong house)
        $response = $this->postJson(
            route('characters.store'),
            [
                'name' => 'Some Invalid Person',
                'role' => 'student',
                'school' => 'Hogwarts School of Testing',
                'house' => $this->getInvalidHouseId(),
                'patronus' => 'assert',
            ]
        );

        // Asserts the request fails with a 422 - Unprocessable Entity error
        $response->assertStatus(400);

        // Asserts the request fails with a proper error message
        $response->assertJsonStructure([
            'message',
        ]);
    }
    
    /** @test */
    public function storeShouldAllowCreationWithValidJsonAndHouse()
    {
        // Tries to create an invalid character (wrong house)
        $response = $this->postJson(
            route('characters.store'),
            [
                'name' => 'Some Invalid Person',
                'role' => 'student',
                'school' => 'Hogwarts School of Testing',
                'house' => $this->getValidHouseId(),
                'patronus' => 'assert',
            ]
        );

        // Asserts the request succeeds with a 201 - Created status
        $response->assertCreated();

        // Asserts the request includes all fields from the created character
        $response->assertJsonStructure([
            'data' => [
                'name', 'role', 'school', 'house', 'patronus',
            ],
        ]);
    }
    
    /** @test */
    public function showShouldReturnNotFoundForInvalidCharacters()
    {
        // Fetches the character
        $response = $this->getJson(
            route('characters.show', ['invalid'])
        );

        // Asserts the request includes all fields from the fetched character
        $response->assertNotFound();

        // Asserts the request fails with a proper error message
        $response->assertJsonStructure([
            'message',
        ]);
    }
    
    /** @test */
    public function showShouldReturnAValidCharacter()
    {
        // Creates sample character
        $character = \App\Models\Character::create([
            'name' => 'Test Person',
            'role' => 'student',
            'school' => 'Hogwarts School of Testing',
            'house' => $this->getValidHouseId(),
            'patronus' => 'test',
        ]);

        // Fetches the character
        $response = $this->getJson(
            route('characters.show', [$character])
        );

        // Asserts the request includes all fields from the fetched character
        $response->assertJsonStructure([
            'data' => [
                'name', 'role', 'school', 'house', 'patronus',
            ],
        ]);
    }
    
    /** @test */
    public function updateShouldFailForInvalidHouses()
    {
        // Creates sample character
        $character = \App\Models\Character::create([
            'name' => 'Test Person',
            'role' => 'student',
            'school' => 'Hogwarts School of Testing',
            'house' => $this->getValidHouseId(),
            'patronus' => 'test',
        ]);

        // Fetches the Characters listing as JSON
        $responseUpdate = $this->putJson(
            route('characters.update', [$character]),
            [
                'house' => $this->getInvalidHouseId(),
            ]
        );

        // Asserts the request fails with a 422 - Unprocessable Entity error
        $responseUpdate->assertStatus(400);

        // Asserts the request fails with a proper error message
        $responseUpdate->assertJsonStructure([
            'message',
        ]);

        // Fetches the character
        $responseShow = $this->getJson(
            route('characters.show', [$character])
        );

        // Asserts the character's house has not changed
        $responseShow->assertJsonPath('data.house', $character->house);
    }
    
    /** @test */
    public function updateShouldSucceedWithValidHouse()
    {
        // Creates sample character
        $character = \App\Models\Character::create([
            'name' => 'Test Person',
            'role' => 'student',
            'school' => 'Hogwarts School of Testing',
            'house' => $this->getInvalidHouseId(),
            'patronus' => 'test',
        ]);

        // Fetches the Characters listing as JSON
        $responseUpdate = $this->putJson(
            route('characters.update', [$character]),
            [
                'house' => $this->getValidHouseId(),
            ]
        );

        // Asserts the request succeeds with a 200 - OK status
        $responseUpdate->assertOk();

        // Asserts the request includes all fields from the updated character
        $responseUpdate->assertJsonStructure([
            'data' => [
                'name', 'role', 'school', 'house', 'patronus',
            ],
        ]);

        // Asserts the character's house has changed on update
        $responseUpdate->assertJsonPath('data.house', $this->getValidHouseId());

        // Fetches the character
        $responseShow = $this->getJson(
            route('characters.show', [$character])
        );

        // Asserts the character's house has changed after isolated show
        $responseShow->assertJsonPath('data.house', $this->getValidHouseId());
    }
    
    /** @test */
    public function destroyShouldSucceed()
    {
        // Creates sample character
        $character = \App\Models\Character::create([
            'name' => 'Test Person',
            'role' => 'student',
            'school' => 'Hogwarts School of Testing',
            'house' => $this->getInvalidHouseId(),
            'patronus' => 'test',
        ]);

        // Fetches the Characters listing as JSON
        $responseDestroy = $this->deleteJson(
            route('characters.update', [$character])
        );

        // Asserts the request succeeds with a 200 - OK status
        $responseDestroy->assertOk();

        // Asserts the request includes all fields from the deleted character
        $responseDestroy->assertJsonStructure([
            'data' => [
                'name', 'role', 'school', 'house', 'patronus',
            ],
        ]);

        // Tries to fetch the character
        $responseShow = $this->getJson(
            route('characters.show', [$character])
        );

        // Asserts the fetch fails with not found
        $responseShow->assertNotFound();
    }
}

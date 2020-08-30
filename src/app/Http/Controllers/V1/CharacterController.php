<?php

namespace App\Http\Controllers\V1;

use App\Models\Character;
use App\Http\Resources\CharacterResource;
use App\Http\Requests\V1\CharacterIndexRequest;
use App\Http\Requests\V1\CharacterStoreRequest;
use App\Http\Requests\V1\CharacterShowRequest;
use App\Http\Requests\V1\CharacterUpdateRequest;
use App\Http\Requests\V1\CharacterDestroyRequest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class CharacterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(CharacterIndexRequest $request)
    {
        // Initializes the query with Eloquent pagination
        $query = Character::paginate();

        // If our request passes a filter by house, handles it too
        if ($request->has('house')) {
            $query = $query->where('house', $request->house);
        }

        // Returns a new API Resource with the Eloquent-paginated characters
        return new CharacterResource(
            $query
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CharacterStoreRequest $request)
    {
        // Checks if the provided house is valid
        $this->checkIsHouseValid($request->house);

        // Creates the new Character and fills it with request data
        $character = new Character;
        $character->fill($request->all());

        // Persist to database
        $character->save();

        // Returns the new character
        return new CharacterResource(
            $character
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(CharacterShowRequest $request, $id)
    {
        // Fetches the character from the database and returns it, or fails to a 404 otherwise
        return new CharacterResource(
            Character::findOrFail($id)
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CharacterUpdateRequest $request, $id)
    {
        // Checks if the provided house is valid
        $this->checkIsHouseValid($request->house);

        // Fetches the character from the database
        $character = Character::findOrFail($id);

        // Updates the fields
        $character->update($request->all());

        // Returns the updated character
        return new CharacterResource(
            $character
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(CharacterDestroyRequest $request, $id)
    {
        // Fetches the character from the database
        $character = Character::findOrFail($id);

        // Deletes the character
        $character->delete();

        // Returns the deleted resource
        return new CharacterResource(
            $character
        );
    }

    /**
     * Checks if a given house ID is valid
     * 
     * @param string $houseId
     * @return bool
     */
    public function isHouseValid($houseId) {
        // Cleanup house ID
        $houseId = trim($houseId);

        // If house ID is empty, automatically returns false
        if (empty($houseId)) {
            return false;
        }

        // Defines a cache key we'll be using
        $cacheKey = 'houses_' . $houseId;

        // Tries to retrieve the house from the cache
        $house = Cache::get($cacheKey);

        // Checks if the house is valid from cache, if it's found, then return true since we validated it
        if (!is_null($house)) {
            return true;
        }

        // Sends request to the PotterAPI Houses route
        $response = Http::get('https://www.potterapi.com/v1/houses/' . $houseId . '?key=' . env('POTTERAPI_KEY'))->json();

        // Test: The response MUST not be empty since it might mean NOT FOUND
        if (empty($response) || count($response) == 0) {
            return false;
        }

        // Test: The response MUST have a first element
        if (!isset($response[0])) {
            return false;
        }

        // Extract the first element of the response as the House
        $house = $response[0];

        // Test the House must be an array
        if (!is_array($house)) {
            return false;
        }

        // Save the found house on cache
        Cache::put($cacheKey, $house);

        // Returns true = a valid house
        return true;
    }

    /**
     * Checks if a given house ID is valid and automatically returns an error status and message if invalid
     * 
     * @param string $houseId
     */
    public function checkIsHouseValid($houseId) {
        if (!$this->isHouseValid($houseId)) {
            abort(400, 'Invalid house supplied.');
        }
    }
}

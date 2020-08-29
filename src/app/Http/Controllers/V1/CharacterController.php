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

class CharacterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(CharacterIndexRequest $request)
    {
        // Returns a new API Resource with the Eloquent-paginated characters
        return new CharacterResource(
            Character::paginate()
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
        // Checks if the house is valid
        // TODO: Implement a proper checking function
        if ($request->house != '5a05e2b252f721a3cf2ea33f') {
            abort(400, 'Invalid house supplied.');
        }

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
}

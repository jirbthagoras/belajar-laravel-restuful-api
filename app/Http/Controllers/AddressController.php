<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressCreateRequest;
use App\Http\Resources\AddressResource;
use App\Http\Resources\ContactResource;
use App\Models\Address;
use App\Models\Contact;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function create(int $idContact, AddressCreateRequest $request): JsonResponse
    {
        $user = Auth::user();

        $contact = Contact::query()
            ->where("user_id", "=", $user->id)
            ->where("id", "=", $idContact)
            ->first();

        if (!$contact)
        {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => ["Contact not found"]
                ]
            ])->setStatusCode(404));
        }

        $data = $request->validated();
        $address = new Address($data);
        $address->contact_id = $contact->id;
        $address->save();

        return (new ContactResource($contact))->response()->setStatusCode(201);
    }

    public function get(int $idContact, int $idAddress): AddressResource
    {
        $user = Auth::user();

        $contact = Contact::query()
            ->where("user_id", "=", $user->id)
            ->where("id", "=", $idContact)
            ->first();

        if (!$contact)
        {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => ["Contact not found"]
                ]
            ])->setStatusCode(404));
        }

        $address = Address::query()
            ->where("id", "=", $idAddress)
            ->where("contact_id", "=", $contact->id)
            ->first();

        if (!$address)
        {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => ["Contact not found"]
                ]
            ])->setStatusCode(404));
        }

        return new AddressResource($address);
    }

    public function remove(int $idContact, int $idAddress): AddressResource
    {
        $user = Auth::user();

        $contact = Contact::query()
            ->where("user_id", "=", $user->id)
            ->where("id", "=", $idContact)
            ->first();

        if (!$contact)
        {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => ["Contact not found"]
                ]
            ])->setStatusCode(404));
        }

        $address = Address::query()
            ->where("id", "=", $idAddress)
            ->where("contact_id", "=", $contact->id)
            ->first();

        if (!$address)
        {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => ["Contact not found"]
                ]
            ])->setStatusCode(404));
        }

        $address->delete();

        return new AddressResource($address);
    }

}

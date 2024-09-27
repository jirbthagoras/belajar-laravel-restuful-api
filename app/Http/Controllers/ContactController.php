<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactCreateRequest;
use App\Http\Requests\ContactUpdateRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContactController extends Controller
{
    public function create(ContactCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = Auth::user();

        $contact = new Contact($data);
        $contact->user_id = $user->id;
        $contact->save();

        return (new ContactResource($contact))->response()->setStatusCode(201);
    }

    public function get(int $id): ContactResource
    {
        $user = Auth::user();

        $contact = Contact::query()
            ->where("id", "=", $id)
            ->where("user_id", "=", $user->id)
            ->first();

        if (!$contact)
        {

            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => ["Contact not found"]
                ]
            ])->setStatusCode(404));
        }

        return new ContactResource($contact);
    }

    public function update(int $id, ContactUpdateRequest $request): ContactResource
    {
        $user = Auth::user();

        $contact = Contact::query()
            ->where("id", "=", $id)
            ->where("user_id", "=", $user->id)
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
        $contact->fill($data);
        $contact->save();

        return new ContactResource($contact);
    }

    public function delete(int $id)
    {
        $user = Auth::user();

        $contact = Contact::query()
            ->where("id", "=", $id)
            ->where("user_id", "=", $user->id)
            ->first();

        if (!$contact)
        {

            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => ["Contact not found"]
                ]
            ])->setStatusCode(404));
        }

        $contact->delete();
        return response()->json([
            "data" => [
                "status" => true
            ]
        ])->setStatusCode(200);
    }
}

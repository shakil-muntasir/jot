<?php

namespace App\Http\Controllers;

use App\Http\Resources\Contact as ContactResource;
use Illuminate\Http\Request;

class BirthdaysController extends Controller
{
    public function index()
    {
        $contacts = request()->user()->contacts()->birthdays()->get();

        return ContactResource::collection($contacts);
    }
}

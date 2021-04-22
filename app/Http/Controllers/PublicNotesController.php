<?php

namespace App\Http\Controllers;

use App\Paste;
use Illuminate\Http\Request;

class PublicNotesController extends Controller
{
    //

    public function showPublicNotes(){

        $user = Paste::where('privacy', 'link')->get();
        return view('paste/publicDashboard', ['userPastes' => $user]);

    }
}

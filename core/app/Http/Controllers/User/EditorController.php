<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EditorController extends Controller
{
    public function index()
    {
        $pageTitle = 'Editor';

        return view('Template::user.editor.index', compact('pageTitle'));
    }
}

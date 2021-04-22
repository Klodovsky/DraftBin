<?php

namespace App\Http\Controllers;

use App\Comment;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Paste;
use Auth;
use App\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\URL;
use \Input;
use \Hash;
use Session;
use Cookie;
use DB;
use \Carbon;

class PasteController extends Controller
{
    public function index()
    {
        return view('paste/index');
    }

    public function submit(Requests\StorePaste $request)
    {
        $title = (empty(trim(Input::get('pasteTitle')))) ? 'Untitled' : Input::get('pasteTitle');

        $expiration = Input::get('expire');
        $privacy = Input::get('privacy');

         // Here we check whether if the user has not respected the bronx in the dropdown list
        $possibleValuesPrivacy = array("link", "password", "private");
        if (in_array($privacy, $possibleValuesPrivacy) == false) return view('paste/index');

        // If the user has chosen password-protected, we hash his pass, otherwise we assign the variable as 'disabled'
        if ($privacy == 'password') $password = bcrypt(Input::get('pastePassword'));
        else $password = 'disabled';

        $burnAfter = 0;
        // Here we generate the expiration timestamp
        switch ($expiration) {
            case 'never':
                $timestampExp = 0;
                break;
            case 'burn':
                $timestampExp = date('Y-m-d H:i:s', time());
                $burnAfter = 1;
                break;
            case '10m':
                $timestampExp = date('Y-m-d H:i:s', time() + 600);
                break;
            case '1h':
                $timestampExp = date('Y-m-d H:i:s', time() + 3600);
                break;
            case '1d':
                $timestampExp = date('Y-m-d H:i:s', time() + 86400);
                break;
            case '1w':
                $timestampExp = date('Y-m-d H:i:s', time() + 604800);
                break;
            default:
                die("User input error.");
                break;
        }

        // Generation of the link as long as the link exists
        $generatedLink = str_random(10);
        $existingPasteWithGeneratedLink = Paste::where('link', $generatedLink)->first();
        while (!is_null($existingPasteWithGeneratedLink)) {
            $generatedLink = str_random(10);
            $existingPasteWithGeneratedLink = Paste::where('link', $generatedLink)->first();
        }

        Paste::create([
            'link' => $generatedLink,
            'userId' => (Auth::check()) ? Auth::id() : 0,
            'views' => '0',
            'title' => $title,
            'content' => Input::get('pasteContent'),
            'ip' => $request->ip(),
            'expiration' => $timestampExp,
            'privacy' => $privacy,
            'password' => $password,
            'noSyntax' => Input::has('noSyntax'),
            'burnAfter' => $burnAfter,
        ]);

        return redirect('/' . $generatedLink);
    }

    public function view($link, Request $request)
    {
        $paste = Paste::where('link', $link)->firstOrFail();

        // Is the logged-in user the one who wrote the paste?
        $isSameUser = ((Auth::user() == $paste->user && $paste->userId != 0)) ? true : false;

        // Paste expiration
        if ($paste->expiration != 0) {
            if ($paste->burnAfter == 0) {
                if (time() > strtotime($paste->expiration)) {
                    if ($isSameUser) $expiration = "Expired";
                    else abort('404');
                } else $expiration = Carbon\Carbon::parse($paste->expiration)->diffForHumans();
            } else {
                // We remove the burn after reading mode only if the paste has not just been created and if in case of burn the pass is good
                if (time() - strtotime($paste->expiration) > 3) {
                    $disableBurn = true;
                    $expiration = "Burn after reading";
                } else $expiration = "Burn after reading (next time)";
            }
        } // Small check in case the admin did not migrate
        elseif ($paste->expiration == "10m" || $paste->expiration == "1h" || $paste->expiration == "1d" || $paste->expiration == "1w" || $paste->expiration == "never" || $paste->expiration == "burn") die("Paste expiration error. Please make sure you have the latest commit of DraftBin and run 'php artisan migrate'.");
        else $expiration = "Never";

        // We take care of the privacy options of the paste (TODO password)
        // https://stackoverflow.com/questions/30212390/laravel-middleware-return-variable-to-controller
        if ($paste->privacy == "private") {
            if ($isSameUser) $privacy = "Private";
            else abort('404');
        } elseif ($paste->privacy == "password") {
            $privacy = "Password-protected";
            if ($request->isMethod('post')) {
                if (!Hash::check(Input::get('pastePassword'), $paste->password)) return view('paste/password', ['link' => $paste->link, 'wrongPassword' => true]);
            } // If the user is not the same and the paste was created more than three seconds ago:
            elseif (!$isSameUser && time() - $paste->created_at->timestamp > 3) return view('paste/password', ['link' => $paste->link]);
        } elseif ($paste->privacy == "link") $privacy = "Public";
        else die("Error.");

        // Here we check if the burn of the paste must be removed (need to do it after checking the password)
        if (isset($disableBurn)) {
            $paste->burnAfter = 0;
            $paste->save();
        }

        // Here we increment the view counter for each view
        if (time() - $paste->updated_at->timestamp > 10) $paste->increment('views');

        $comment_lists = Comment::where('paster_link', $link)->get();
        $total_comment = count($comment_lists);

        // Rendering the view
        return view('paste/view', [
            'username' => ($paste->userId != 0) ? $paste->user->name : "Guest",
            'views' => $paste->views,
            'sameUser' => $isSameUser,
            'link' => $link,
            'title' => $paste->title,
            'content' => $paste->content,
            'expiration' => $expiration,
            'privacy' => $privacy,
            'date' => $paste->created_at->format('M jS, Y'),
            'fulldate' => $paste->created_at->format('d/m/Y - H:i:s'),
            'noSyntax' => $paste->noSyntax,
            'comment_lists'=>$comment_lists,
            'total_comment'=>$total_comment,
        ]);
    }

    public function password($link, Request $request)
    {
        $paste = Paste::where('link', $link)->firstOrFail();
        $messages = array(
            'pastePassword.required' => 'Please enter a password',
        );
        $this->validate($request, [
            'pastePassword' => 'required',
        ], $messages);

        if (Hash::check(Input::get('pastePassword'), $paste->password)) {
            Cookie::queue($paste->link, Input::get('pastePassword'), 15);
            return redirect('/' . $link);
        } else {
            return view('paste/password', ['link' => $paste->link, 'wrongPassword' => true]);
        }
    }

    // TODO Raw
    public function raw($link)
    {
        header('Content-Type: text/plain');
        $paste = Paste::where('link', $link)->firstOrFail();

        $timestampUpdated = $paste->updated_at->timestamp;
        $diffTimestamp = time() - $timestampUpdated;

        // We generate the expire messages and we expire the paste in the database if it is
        if ($paste->expiration == "never") {
            $expired = false;
        } elseif ($paste->expiration == "burn") {
            // If the paste has never been seen, then the user who created it has just been redirected to it, we manage that here
            if ($diffTimestamp < 5) {
                $expired = false;
            } // If it has already been seen once by its creator, then we put it in burn mode after reading
            else {
                $expired = false;
                $burn = true;
            }
        } elseif ($paste->expiration == "10m") {
            if ($diffTimestamp > 600) $expired = true;
            else $expired = false;
        } elseif ($paste->expiration == "1h") {
            if ($diffTimestamp > 3600) $expired = true;
            else $expired = false;
        } elseif ($paste->expiration == "1d") {
            if ($diffTimestamp > 86400) $expired = true;
            else $expired = false;
        } elseif ($paste->expiration == "1w") {
            if ($diffTimestamp > 604800) $expired = true;
            else $expired = false;
        } elseif ($paste->expiration == "expired") {
            $alreadyExpired = true;
            $expired = true;
        } // If there is a problem, we manage the exception by stopping everything
        else die('Fatal error.');

        // We check if the paste has expired
        if ($expired == true) {
            // We mark it in case itis not marked expired in the database
            if (!isset($alreadyExpired)) {
                $paste->expiration = "expired";
                $paste->save();
            }
            // We see if the authr is connected, if so he can see his paste expired, otherwise 404
            if (Auth::check()) {
                if ($paste->userId != Auth::user()->id) {
                    return view('errors/404');
                }
            } else return view('errors/404');
        }
        if ($paste->privacy == "private") {
            // We see if the creator is connected, if so he can see his paste expired, otherwise 404
            if (Auth::check()) {
                if ($paste->userId != Auth::user()->id) {
                    return view('errors/404');
                } else $privacy = "Private";
            } else return view('errors/404');
        } elseif ($paste->privacy == "password") {
            // If the paste was created less than 3 seconds ago then we do not ask for the pass, it's because the user is already looking at it
            if ($diffTimestamp > 3) {
                // Here we bypass the pass if the user is the same
                if (Auth::check()) {
                    if ($paste->userId != Auth::user()->id) {
                        // If the password cookie exists, we recheck it anyway
                        if (Cookie::get($paste->link) !== null) {
                            // We recheck the cookie and send the password view if the pass has been manipulated
                            if (Hash::check(Cookie::get($paste->link), $paste->password) == false) {
                                return view('paste/password', ['link' => $paste->link]);
                            } else {
                            }
                        } // If it does not exist, we will ask for the password
                        else {
                            return view('paste/password', ['link' => $paste->link]);
                        }
                    }
                } else {
                    // If the password cookie exists, we recheck it anyway
                    if (Cookie::get($paste->link) !== null) {
                        // We recheck the cookie and send the password view if the pass has been manipulated
                        if (Hash::check(Cookie::get($paste->link), $paste->password) == false) {
                            return view('paste/password', ['link' => $paste->link]);
                        } else {
                        }
                    } // If it does not exist, we will ask for the password
                    else {
                        return view('paste/password', ['link' => $paste->link]);
                    }
                }
            } else $privacy = "Password-protected (bypassed)";
        } elseif ($paste->privacy == "link") {
            $privacy = "Public";
        }

        // We see if the paste is in burn after reading (and therefore that it was seen only once, by its creator, just after writing)
        if (isset($burn)) {
            $paste->expiration = "expired";
            $paste->save();
        }

        // Here we increment the view counter for each view
        if ($diffTimestamp > 10) DB::table('pastes')->where('link', $link)->increment('views');

        // We create the var sent to the view indicating if the author is himself the viewer
        $sameUser = false;
        if (Auth::check()) {
            if ($paste->userId == Auth::user()->id) {
            }
        }
        return response($paste->content, 200)->header('Content-Type', 'text/plain');
    }


    public function comment_save_by_user(Request $request){

        $validator = \Validator::make($request->all(), [
            'comment_user_mail' => 'required|email|max:35',
            'comment_user_mail' => 'required',
            'comment_body' => 'required',
            '_token' => 'required',
            'paster_id' => 'required',

        ]);



        if ($validator->fails()) {
            return redirect()->back()
                ->with('error_message', 'ERROR!! NAME,EMAIL AND COMMENT FEILDS ARE REQUIRED')
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $link = Crypt::decrypt($request->paster_id);
        }catch (DecryptException $e){
            return  redirect(URL::previous());
        }

       // $paster_info = Paste::where('link', $link)->first();
       // $paster_id = $paster_info->id;

        $data = new Comment();
        $data->paster_link = $link;
        $data->comment_user_name = $request->comment_user_name;
        $data->comment_user_mail = $request->comment_user_mail;
        $data->comment_body = $request->comment_body;
        $data->save();
        return redirect()->back()->with('success_message', 'Successfully Comment Added');
    }
}
    
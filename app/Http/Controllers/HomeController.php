<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequestData;
use App\Models\User;

use Illuminate\Support\Facades\Auth;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function suggestion()
    {
//         $request = RequestData::where('receiver_id', Auth::id())
//         ->where('status', 0)
//         ->get();
// dd($request);

// $connection = RequestData::where(function ($query) {
//     $query->where('sender_id', Auth::id())
//           ->orWhere('receiver_id', Auth::id());
// })
// ->where('status', '1')
// ->get();

// dd($connection);
// $sendrequest = RequestData::where('sender_id', Auth::id())
//         ->where('status', 0)
//         ->get();
// dd($sendrequest);

// $suggestions = RequestData::where('sender_id', Auth::id())
//                          ->orWhere('receiver_id', Auth::id())
//                          ->pluck('receiver_id')
//                          ->toArray();
//                         //  dd($suggestions);

//                          $suggestions2 = RequestData::where('sender_id', Auth::id())
//                          ->orWhere('receiver_id', Auth::id())
//                          ->pluck('sender_id')
//                          ->toArray();
//                         //  dd($suggestions2);
//                          $user=User::whereNotIn('id',$suggestions)->whereNotIn('id',$suggestions2)->get();

                        //  dd($user);
        return view('home');
    }
    public function index(){

        $request = RequestData::join('users as senders', 'requests.sender_id', '=', 'senders.id')
        ->join('users as receivers', 'requests.receiver_id', '=', 'receivers.id')
        ->where('requests.receiver_id', Auth::id())
        ->where('requests.status', 0)
        ->select('requests.*', 'senders.name as sender_name', 'receivers.name as receiver_name', 'senders.email as sender_email', 'receivers.email as receiver_email')
        ->get();

      return  view('home',compact('request'));

    }
    public function suggestions_data(){
        $suggestions = RequestData::where('sender_id', Auth::id())
                        ->orWhere('receiver_id', Auth::id())
                        ->pluck('receiver_id')
                        ->toArray();

        $suggestions2 = RequestData::where('sender_id', Auth::id())
                        ->orWhere('receiver_id', Auth::id())
                        ->pluck('sender_id')
                        ->toArray();

        $user = User::whereNotIn('id', $suggestions)
                     ->whereNotIn('id', $suggestions2)
                     ->take(10)
                     ->get();

                    //  dd($user);
        return view('components.suggestion', compact('user'));
    }
    public function getMoreUsers(Request $request) {
        $page = $request->input('page');
        $perPage = 10;

        $suggestions = RequestData::where('sender_id', Auth::id())
                        ->orWhere('receiver_id', Auth::id())
                        ->pluck('receiver_id')
                        ->toArray();

        $suggestions2 = RequestData::where('sender_id', Auth::id())
                        ->orWhere('receiver_id', Auth::id())
                        ->pluck('sender_id')
                        ->toArray();

        $users = User::whereNotIn('id', $suggestions)
                     ->whereNotIn('id', $suggestions2)
                     ->skip(($page - 1) * $perPage)
                     ->take($perPage)
                     ->get();

        return response()->json($users);
    }
    public function connectUser(Request $request, $userId) {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $requestData = new RequestData();
        $requestData->receiver_id = $userId;
        $requestData->sender_id = Auth::id();
        $requestData->status = '0';
        $requestData->save();

        return response()->json(['message' => 'Connection request sent successfully', 'user' => $user]);
    }
    public function sendrequest(){

        $sendrequest = RequestData::join('users as senders', 'requests.sender_id', '=', 'senders.id')
        ->join('users as receivers', 'requests.receiver_id', '=', 'receivers.id')
        ->where('requests.sender_id', Auth::id())
        ->where('requests.status', 0)
        ->select('requests.*', 'senders.name as sender_name', 'receivers.name as receiver_name')
        ->get();
        // dd($sendrequest);

        return view('components.sendrequest', compact('sendrequest'));
    }
    public function withdrawRequest($requestId)
    {

        $requestData = RequestData::where('id', $requestId)
            ->where('sender_id', auth()->id())
            ->first();

        if ($requestData) {

            $requestData->delete();

            return response()->json(['message' => 'Request withdrawn successfully']);
        } else {
            return response()->json(['message' => 'Request not found or not authorized'], 404);
        }
    }

    public function recieved(){

        $request = RequestData::join('users as senders', 'requests.sender_id', '=', 'senders.id')
        ->join('users as receivers', 'requests.receiver_id', '=', 'receivers.id')
        ->where('requests.receiver_id', Auth::id())
        ->where('requests.status', 0)
        ->select('requests.*', 'senders.name as sender_name', 'receivers.name as receiver_name', 'senders.email as sender_email', 'receivers.email as receiver_email')
        ->get();

       return view('components.recieved_request', compact('request'));


    }

    public function acceptRequest($requestId)
    {
        $requestData = RequestData::where('id', $requestId)
            ->where('receiver_id', auth()->id())
            ->where('status', 0)
            ->first();

        if ($requestData) {

            $requestData->status = 1;
            $requestData->save();

            return response()->json(['message' => 'Request accepted successfully'], 200);
        } else {
            // Request not found or not authorized.
            return response()->json(['message' => 'Request not found or not authorized'], 404);
        }
    }

    public function connection()
    {
        $connection = RequestData::where(function ($query) {
            $query->where('sender_id', Auth::id())
                  ->orWhere('receiver_id', Auth::id());
        })
        ->where('status', '1')
        ->join('users as senders', 'requests.sender_id', '=', 'senders.id')
        ->join('users as receivers', 'requests.receiver_id', '=', 'receivers.id')
        ->select('requests.*', 'senders.name as sender_name', 'senders.email as sender_email', 'receivers.name as receiver_name', 'receivers.email as receiver_email')
        ->get();
        // dd($connection);

        return view('components.connection', compact('connection'));
    }
    public function remove($requestId)
    {
        $requestData = RequestData::where('id', $requestId)
            ->where(function ($query) {
                $query->where('sender_id', Auth::id())
                      ->orWhere('receiver_id', Auth::id());
            })
            ->where('status', '1')
            ->first();

        if ($requestData) {


            $requestData->delete();

            return response()->json(['message' => 'Request removed successfully']);
        } else {
            return response()->json(['message' => 'Request not found or not authorized'], 404);
        }
    }

}

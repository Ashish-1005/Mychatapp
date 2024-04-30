<?php

namespace App\Http\Controllers;

use App\Models\Chatm;
use App\Notifications\MessageSentNotification;
use App\Models\User;
use App\Models\MessageNotification;
use Illuminate\Http\Request;

class ChatController extends Controller
{
      public function index(){
       $chats= Chatm::all();
       $users = User::where('id', '!=', auth()->id())->get();
       $messagenotification = MessageNotification::where('user_id', auth()->id())->paginate(10);
        return view('chats.chat',compact('chats','users','messagenotification'));
    }

    public function send(Request $request){
    //          $message = Chatm::create([
    //          'body' => $request->body,
    //          'from_id' => auth()->user()->id,
    //          'to_id' => $request->to_id,
    // ]);
        $message = new Chatm();
        // $message->body = $request->body;
        $message->from_id = auth()->user()->id;
        $message->to_id = $request->to_id;

        // Check if a message body is provided
    if ($request->filled('body')) {
        $message->body = $request->body;
    }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            // dd($file);
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $filename);
            $message->file_path = 'uploads/' . $filename;
            $message->file_type = $file->getClientMimeType();
        }

        $message->save();
        
        $formattedCreatedAt = $message->created_at->format('H:i A | F j');

        //   dd($message);

    $senderNotification = new MessageNotification();
    $senderNotification->user_id = auth()->user()->id;
    $senderNotification->message = $message->body;
    $senderNotification->status = 'unread';
    $senderNotification->save();


    $recipientNotification = new MessageNotification();
    $recipientNotification->user_id = $request->to_id;
    $recipientNotification->message = $message->body;
    $recipientNotification->status = 'unread';
    $recipientNotification->save();

    // $msg=$recipientNotification->message;

    // return response()->json(['status'=>'success','message'=>$msg->body,'from_id' => $msg->from_id,
    // 'to_id' => $msg->to_id]);
    return response()->json([
        'status' => 'success',
        'message' => $message->body,
        'from_id' => $message->from_id,
        'to_id' => $message->to_id,
        'is_authenticated_user' => ($message->from_id == auth()->user()->id),
        'created_at' => $formattedCreatedAt
    ]);

    }

    public function delete($id){
        $chat = Chatm::findOrFail($id);
        if (auth()->id() == $chat->from_id) {
            $chat->delete();
            return response()->json(['status' => 'success', 'message' => 'Message deleted successfully']);
        }
        return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
    }



    public function fetchNotifications()
    {
        $userId = auth()->id();
        $notifications = MessageNotification::where('user_id', $userId)
                                             ->where('status', 'unread')
                                             ->get();

        return response()->json(['notifications' => $notifications]);
    }


public function markAsRead($id)
{
    $notifications = MessageNotification::find($id);
    if ($notifications && $notifications->user_id == auth()->id()) {
        $notifications->status = 'read';
        $notifications->save();
    }
    return response()->json(['message' => 'Notifications marked as read']);
}


}



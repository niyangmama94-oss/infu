<?php

namespace App\Http\Controllers\Influencer;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConversationController extends Controller
{

    public function index(Request $request)
    {
        $pageTitle     = 'Conversation List';
        $conversations = Conversation::searchable(['user:username', 'user:firstname', 'user:lastname'])
            ->where('influencer_id', authInfluencerId());
        $conversations = $conversations->with(['user', 'lastMessage'])
            ->whereHas('lastMessage')->orderBy('updated_at', 'desc')->paginate(getPaginate());
        return view('Template::influencer.conversation.index', compact('pageTitle', 'conversations'));
    }

    public function store(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'message'       => 'required',
            'attachments'   => 'nullable|array',
            'attachments.*' => ['required', new FileTypeValidate(['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'txt'])],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $conversation = Conversation::where('influencer_id', authInfluencerId())->where('id', $id)->first();

        if (!$conversation) {
            return response()->json(['error' => 'Invalid Conversation']);
        }

        $conversation->updated_at = now();
        $conversation->save();

        $message                  = new ConversationMessage();
        $message->conversation_id = $conversation->id;
        $message->sender          = 'influencer';
        $message->message         = $request->message;

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                try {
                    $arrFile[] = fileUploader($file, getFilePath('conversation'));
                } catch (\Exception $exp) {
                    return response()->json(['error' => 'Couldn\'t upload your image']);
                }
            }

            $message->attachments = json_encode($arrFile);
        }

        $channelName = "chat-data-conversion-{$id}";

        event(new MessageSent($message, $channelName));

        $message->save();

        return view('Template::user.conversation.last_message', compact('message'));
    }

    public function view($id)
    {
        $pageTitle           = 'Conversation with Client';
        $conversation        = Conversation::where('influencer_id', authInfluencerId())->where('id', $id)->with('user', 'messages')->first();
        $user                = $conversation->user;
        $conversationMessage = $conversation->messages->take(10);
        return view('Template::influencer.conversation.view', compact('pageTitle', 'conversation', 'conversationMessage', 'user'));
    }

    public function message(Request $request)
    {
        $conversationMessage = ConversationMessage::where('conversation_id', $request->conversation_id)->take($request->messageCount)->latest()->get();
        return view('Template::influencer.conversation.message', compact('conversationMessage'));
    }
}

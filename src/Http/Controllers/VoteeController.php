<?php

namespace Laravolt\Votee\Http\Controllers;

use Votee;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class VoteeController extends Controller
{

    /**
     * ContentController constructor.
     */
    public function __construct()
    {
    }

    public function up(Request $request)
    {
        try {
            $vote = Votee::voteUp($request->get('id'));
            $data['data'] = [
                'value'      => $vote['value'],
                'vote_up'   => $vote['vote_up'],
                'vote_down' => $vote['vote_down'],
            ];

        } catch (\Exception $e) {
            if($request->wantsJson()) {
                return response()->json(['status' => $e->getCode(), 'error' => $e->getMessage(), 'message' => $e->getMessage()], $e->getCode());
            }
            throw $e;
        }

        if ($request->ajax()) {
            return response()->json($data);
        }

        return redirect()->back();
    }

    public function down(Request $request)
    {
        try {
            $vote = Votee::voteDown($request->get('id'));
            $data['data'] = [
                'value'      => $vote['value'],
                'vote_up'   => $vote['vote_up'],
                'vote_down' => $vote['vote_down'],
            ];

        } catch (\Exception $e) {
            if($request->wantsJson()) {
                return response()->json(['status' => $e->getCode(), 'error' => $e->getMessage(), 'message' => $e->getMessage()], $e->getCode());
            }

            throw $e;
        }

        if ($request->ajax()) {
            return response()->json($data);
        }

        return redirect()->back();
    }

}

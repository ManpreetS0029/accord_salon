<?php

namespace App\Http\Controllers;

use App\Client;
use App\CustomClasses\Message_Class;
use Illuminate\Http\Request;

class SendMessages extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $msg91 = new Message_Class('231519AqsTJvFRuy5b717b05', 'ACCORD', '1');


        $listClients = Client::where("isrealclient", "=", "1")->where("can_send_sms", "=", "1")->orderBy("clientname", "asc")->get();

        //"optout" => json_encode($msg91->getOptOut())
        //print_r($msg91->getOptOut());
        
        return View('sendmessages', ['clientList' => $listClients, "balance_messages" => $msg91->getBalanaceOfMessages()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


        if (trim($request->message12) == "") {
            return redirect(route('sendmessages.index'))
                ->withInput()
                ->withErrors(array('Please enter some text to send'));
        }

        $msg = trim($request->message12);

        $msg91 = new Message_Class('231519AqsTJvFRuy5b717b05', 'ACCORD', '1');

        $res1 = array();
        //
        //test send message
        if (isset($request->test_send_msg)) {

            if ($request->testphone != '') {
                $res1 = $msg91->sendMsg($msg, [trim($request->testphone)]);
            }
        } else if (isset($request->send_msg)) {
            $clientPhoneArr = array();
            $clientsCount = 0;
            $x = 0;
            if (count($request->clientids) > 0) {
                foreach ($request->clientids as $client) {
                    $clientsCount++;

                    if ($clientsCount > 50) {
                        $clientsCount = 0;
                        $x++;
                    }
                    $clientPhoneArr[$x][] = $client;
                }

                foreach ($clientPhoneArr as $arr) {
                    $res1 = $msg91->sendMsg($msg,  $arr);
                }
            }
        }

        $request->session()->flash("successmsg", json_encode($res1));
        return redirect()->back()->withInput();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

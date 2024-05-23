<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//Route::get('/', function () {
//    return view('welcome');
//});

Route::post('mail/inbound', function () {
    try {
        $rawPostData = file_get_contents('php://input');
        $emailData = json_decode($rawPostData, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo 'Invalid JSON';
            exit;
        }
        \App\Models\MailLogRaw::query()->insert([$emailData]);
        $fileVersion = new \App\Models\FileVersion();
        $fileVersion->author = $emailData['From'];
        $fileVersion->datetime = date('Y-m-d H:i:s');
        $fileVersion->date_sent = $emailData['Date'];
        $fileVersion->save();
        foreach ($emailData['Attachments'] as $data) {
            $base64String = $data['Content'];
            $decodedData = base64_decode($base64String);
            $upload = \Illuminate\Support\Facades\Storage::disk('Wasabi')->put($emailData['Subject'] . '/' . $data['Name'], $decodedData);
            $uploadLog = \Illuminate\Support\Facades\Storage::disk('Wasabi')->put('MailLog/' . $fileVersion->_id . '/' . $data['Name'], $decodedData, 'sdwdw');
        }
        return 'Success';
    }
    catch (Exception $exception)
    {
        return abort(500);
    }
});

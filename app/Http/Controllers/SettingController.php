<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Settings;

class SettingController extends Controller
{
    public function getWhiteLogo(Request $request)
    {
        $whitelogo = Settings::where('field', Settings::WHITE_LOGO_FIELD)->first();
        if ($whitelogo) {
            return response()->json([
                'response' => [
                    'code' => 200,
                    'api_status' => 1,
                    Settings::WHITE_LOGO_FIELD => $whitelogo['value']
                ]
            ]);
        }
        return response()->json([
            'response' => [
                'code' => 200,
                'api_status' => 1,
                Settings::WHITE_LOGO_FIELD => '/logos/LogoWhite.png'
            ]
        ]);
    }

    public function getBlackLogo(Request $request)
    {
        $blacklogo = Settings::where('field', Settings::BLACK_LOGO_FIELD)->first();
        if ($blacklogo) {
            return response()->json([
                'response' => [
                    'code' => 200,
                    'api_status' => 1,
                    Settings::BLACK_LOGO_FIELD => $blacklogo['value']
                ]
            ]);
        }
        return response()->json([
            'response' => [
                'code' => 200,
                'api_status' => 1,
                Settings::BLACK_LOGO_FIELD => '/logos/LogoBlack.png'
            ]
        ]);
    }

    public function setWhiteLogo(Request $request)
    {
        $file = $request->file(Settings::WHITE_LOGO_FIELD);
        $destinationPath = 'logos';
        $filename = "Logo_" . date('Y-m-d_H-i-s') . "." . $file->getClientOriginalExtension();

        try {
            $file->move($destinationPath, $filename);
            $whitelogo = Settings::where('field', Settings::WHITE_LOGO_FIELD)->first();
            if ($whitelogo) {
                $whitelogo['value'] = "/logos/$filename";
            } else {
                Settings::create(['field' => Settings::WHITE_LOGO_FIELD, 'value' => "/logos/$filename"]);
            }
            return response()->json([
                'response' => [
                    'code' => 200,
                    'api_status' => 1,
                    'filename' => "/logos/$filename",
                ]
            ]);
        } catch (Exception $error) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "Upload failed",
                ]
            ]);
        }
    }

    public function setBlackLogo(Request $request)
    {
        $file = $request->file(Settings::BLACK_LOGO_FIELD);
        $destinationPath = 'logos';
        $filename = "Logo_" . date('Y-m-d_H-i-s') . "." . $file->getClientOriginalExtension();

        try {
            $file->move($destinationPath, $filename);
            $blacklogo = Settings::where('field', Settings::BLACK_LOGO_FIELD)->first();
            if ($blacklogo) {
                $blacklogo['value'] = "/logos/$filename";
            } else {
                Settings::create(['field' => Settings::BLACK_LOGO_FIELD, 'value' => "/logos/$filename"]);
            }
            return response()->json([
                'response' => [
                    'code' => 200,
                    'api_status' => 1,
                    'filename' => "/logos/$filename",
                ]
            ]);
        } catch (Exception $error) {
            return response()->json([
                'response' => [
                    'code' => 400,
                    'api_status' => 0,
                    'message' => "Upload failed",
                ]
            ]);
        }
    }
}

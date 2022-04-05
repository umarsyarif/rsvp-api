<?php

namespace App\Service;

use App\Models\NotifikasiUser;

class BroadcastService
{
    public static function sendNotif($key, $value, $type, $judul, $konten)
    {
        $data = [
            "app_id" => ENV('ONE_SIGNAL_APP_ID'),
            "filters" => [
                ["field" => "tag", "key" => $key, "relation" => "=", "value" => $value],
            ],
            "data" => ["type" => $type, "notif" => 0],
            "headings" => ["en" => $judul],
            "contents" => ["en" => $konten],
        ];
        $client = new \GuzzleHttp\Client(['headers' => [
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . ENV('ONE_SIGNAL_APP_KEY'),
        ]]);
        $oneSignal = $client->post(
            'https://onesignal.com/api/v1/notifications',
            [
                'body' => json_encode($data)
            ]
        );
    }
    public static function saveNotifikasi($uuid_karyawan, $isi, $keterangan, $type)
    {
        $data = [
            "id_user" => $uuid_karyawan,
            "isi" => $isi,
            "keterangan" => $keterangan,
            "type" => $type,
        ];
        NotifikasiUser::create($data);
    }
}

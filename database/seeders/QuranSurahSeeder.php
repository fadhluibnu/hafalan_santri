<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\QuranSurah;

class QuranSurahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $surahs = [
            [
                "nama" => "Al Fatihah",
                "jumlah_ayat" => 7
            ],
            [
                "nama" => "Al Baqarah",
                "jumlah_ayat" => 286
            ],
            [
                "nama" => "Ali Imran",
                "jumlah_ayat" => 200
            ],
            [
                "nama" => "An Nisa",
                "jumlah_ayat" => 176
            ],
            [
                "nama" => "Al Maidah",
                "jumlah_ayat" => 120
            ],
            [
                "nama" => "Al An`am",
                "jumlah_ayat" => 165
            ],
            [
                "nama" => "Al A`raf",
                "jumlah_ayat" => 206
            ],
            [
                "nama" => "Al Anfal",
                "jumlah_ayat" => 75
            ],
            [
                "nama" => "At Taubah",
                "jumlah_ayat" => 129
            ],
            [
                "nama" => "Yunus",
                "jumlah_ayat" => 109
            ],
            [
                "nama" => "Hud",
                "jumlah_ayat" => 123
            ],
            [
                "nama" => "Yusuf",
                "jumlah_ayat" => 111
            ],
            [
                "nama" => "Ar Ra`d",
                "jumlah_ayat" => 43
            ],
            [
                "nama" => "Ibrahim",
                "jumlah_ayat" => 52
            ],
            [
                "nama" => "Al Hijr",
                "jumlah_ayat" => 99
            ],
            [
                "nama" => "An Nahl",
                "jumlah_ayat" => 128
            ],
            [
                "nama" => "Al Isra",
                "jumlah_ayat" => 111
            ],
            [
                "nama" => "Al Kahf",
                "jumlah_ayat" => 110
            ],
            [
                "nama" => "Mariam",
                "jumlah_ayat" => 98
            ],
            [
                "nama" => "Taha",
                "jumlah_ayat" => 135
            ],
            [
                "nama" => "Al Anbiya",
                "jumlah_ayat" => 112
            ],
            [
                "nama" => "Al Hajj",
                "jumlah_ayat" => 78
            ],
            [
                "nama" => "Al Mu`minun",
                "jumlah_ayat" => 118
            ],
            [
                "nama" => "An Nur",
                "jumlah_ayat" => 64
            ],
            [
                "nama" => "Al Furqan",
                "jumlah_ayat" => 77
            ],
            [
                "nama" => "Asy Syu`ara",
                "jumlah_ayat" => 227
            ],
            [
                "nama" => "An Naml",
                "jumlah_ayat" => 93
            ],
            [
                "nama" => "Al Qashas",
                "jumlah_ayat" => 88
            ],
            [
                "nama" => "Al `Ankabut",
                "jumlah_ayat" => 69
            ],
            [
                "nama" => "Ar Rum",
                "jumlah_ayat" => 60
            ],
            [
                "nama" => "Lukman",
                "jumlah_ayat" => 34
            ],
            [
                "nama" => "As Sajdah",
                "jumlah_ayat" => 30
            ],
            [
                "nama" => "Al Ahzab",
                "jumlah_ayat" => 73
            ],
            [
                "nama" => "Saba",
                "jumlah_ayat" => 54
            ],
            [
                "nama" => "Fatir",
                "jumlah_ayat" => 45
            ],
            [
                "nama" => "Yasin",
                "jumlah_ayat" => 83
            ],
            [
                "nama" => "As Saffat",
                "jumlah_ayat" => 182
            ],
            [
                "nama" => "Sad",
                "jumlah_ayat" => 88
            ],
            [
                "nama" => "Az Zumar",
                "jumlah_ayat" => 75
            ],
            [
                "nama" => "Gafir",
                "jumlah_ayat" => 85
            ],
            [
                "nama" => "Fussilat",
                "jumlah_ayat" => 54
            ],
            [
                "nama" => "Asy Syura",
                "jumlah_ayat" => 53
            ],
            [
                "nama" => "Az Zukhruf",
                "jumlah_ayat" => 89
            ],
            [
                "nama" => "Ad Dukhan",
                "jumlah_ayat" => 59
            ],
            [
                "nama" => "Al Jasiyah",
                "jumlah_ayat" => 37
            ],
            [
                "nama" => "Al Ahqaf",
                "jumlah_ayat" => 35
            ],
            [
                "nama" => "Muhammad",
                "jumlah_ayat" => 38
            ],
            [
                "nama" => "Al Fath",
                "jumlah_ayat" => 29
            ],
            [
                "nama" => "Al Hujurat",
                "jumlah_ayat" => 18
            ],
            [
                "nama" => "Qaf",
                "jumlah_ayat" => 45
            ],
            [
                "nama" => "Az Zariyat",
                "jumlah_ayat" => 60
            ],
            [
                "nama" => "At Tur",
                "jumlah_ayat" => 49
            ],
            [
                "nama" => "An Najm",
                "jumlah_ayat" => 62
            ],
            [
                "nama" => "Al Qamar",
                "jumlah_ayat" => 55
            ],
            [
                "nama" => "Ar Rahman",
                "jumlah_ayat" => 78
            ],
            [
                "nama" => "Al Waqi`ah",
                "jumlah_ayat" => 96
            ],
            [
                "nama" => "Al Hadid",
                "jumlah_ayat" => 29
            ],
            [
                "nama" => "Al Mujadalah",
                "jumlah_ayat" => 22
            ],
            [
                "nama" => "Al Hasyr",
                "jumlah_ayat" => 24
            ],
            [
                "nama" => "Al Mumtahanah",
                "jumlah_ayat" => 13
            ],
            [
                "nama" => "As Saff",
                "jumlah_ayat" => 14
            ],
            [
                "nama" => "Al Jumu`ah",
                "jumlah_ayat" => 11
            ],
            [
                "nama" => "Al Munafiqun",
                "jumlah_ayat" => 11
            ],
            [
                "nama" => "At Tagabun",
                "jumlah_ayat" => 18
            ],
            [
                "nama" => "At Talaq",
                "jumlah_ayat" => 12
            ],
            [
                "nama" => "At Tahrim",
                "jumlah_ayat" => 12
            ],
            [
                "nama" => "Al Mulk",
                "jumlah_ayat" => 30
            ],
            [
                "nama" => "Al Qalam",
                "jumlah_ayat" => 52
            ],
            [
                "nama" => "Al Haqqah",
                "jumlah_ayat" => 52
            ],
            [
                "nama" => "Al Ma`arij",
                "jumlah_ayat" => 44
            ],
            [
                "nama" => "Nuh",
                "jumlah_ayat" => 28
            ],
            [
                "nama" => "Al Jinn",
                "jumlah_ayat" => 28
            ],
            [
                "nama" => "Al Muzzammil",
                "jumlah_ayat" => 20
            ],
            [
                "nama" => "Al Muddassir",
                "jumlah_ayat" => 56
            ],
            [
                "nama" => "Al Qiyamah",
                "jumlah_ayat" => 40
            ],
            [
                "nama" => "Al Insan",
                "jumlah_ayat" => 31
            ],
            [
                "nama" => "Al Mursalat",
                "jumlah_ayat" => 50
            ],
            [
                "nama" => "An Naba`",
                "jumlah_ayat" => 40
            ],
            [
                "nama" => "An Nazi`at",
                "jumlah_ayat" => 46
            ],
            [
                "nama" => "`Abasa",
                "jumlah_ayat" => 42
            ],
            [
                "nama" => "At Takwir",
                "jumlah_ayat" => 29
            ],
            [
                "nama" => "Al Infitar",
                "jumlah_ayat" => 19
            ],
            [
                "nama" => "Al Mutaffifin",
                "jumlah_ayat" => 36
            ],
            [
                "nama" => "Al Insyiqaq",
                "jumlah_ayat" => 25
            ],
            [
                "nama" => "Al Buruj",
                "jumlah_ayat" => 22
            ],
            [
                "nama" => "At Tariq",
                "jumlah_ayat" => 17
            ],
            [
                "nama" => "Al A`la",
                "jumlah_ayat" => 19
            ],
            [
                "nama" => "Al Gasyiyah",
                "jumlah_ayat" => 26
            ],
            [
                "nama" => "Al Fajr",
                "jumlah_ayat" => 30
            ],
            [
                "nama" => "Al Balad",
                "jumlah_ayat" => 20
            ],
            [
                "nama" => "Asy Syams",
                "jumlah_ayat" => 15
            ],
            [
                "nama" => "Al Lail",
                "jumlah_ayat" => 21
            ],
            [
                "nama" => "Ad Duha",
                "jumlah_ayat" => 11
            ],
            [
                "nama" => "Asy Syarh",
                "jumlah_ayat" => 8
            ],
            [
                "nama" => "At Tin",
                "jumlah_ayat" => 8
            ],
            [
                "nama" => "Al `Alaq",
                "jumlah_ayat" => 19
            ],
            [
                "nama" => "Al Qadr",
                "jumlah_ayat" => 5
            ],
            [
                "nama" => "Al Bayyinah",
                "jumlah_ayat" => 8
            ],
            [
                "nama" => "Az Zalzalah",
                "jumlah_ayat" => 8
            ],
            [
                "nama" => "Al `Adiyat",
                "jumlah_ayat" => 11
            ],
            [
                "nama" => "Al Qar`ah",
                "jumlah_ayat" => 11
            ],
            [
                "nama" => "At Takasur",
                "jumlah_ayat" => 8
            ],
            [
                "nama" => "Al `Asr",
                "jumlah_ayat" => 3
            ],
            [
                "nama" => "Al Humazah",
                "jumlah_ayat" => 9
            ],
            [
                "nama" => "Al Fil",
                "jumlah_ayat" => 5
            ],
            [
                "nama" => "Quraisy",
                "jumlah_ayat" => 4
            ],
            [
                "nama" => "Al Ma`un",
                "jumlah_ayat" => 7
            ],
            [
                "nama" => "Al Kausar",
                "jumlah_ayat" => 3
            ],
            [
                "nama" => "Al Kafirun",
                "jumlah_ayat" => 6
            ],
            [
                "nama" => "An Nasr",
                "jumlah_ayat" => 3
            ],
            [
                "nama" => "Al Lahab",
                "jumlah_ayat" => 5
            ],
            [
                "nama" => "Al Ikhlas",
                "jumlah_ayat" => 4
            ],
            [
                "nama" => "Al Falaq",
                "jumlah_ayat" => 5
            ],
            [
                "nama" => "An Nas",
                "jumlah_ayat" => 6
            ]
        ];

        foreach ($surahs as $surah) {
            QuranSurah::create([
                'name' => $surah['nama'],
                'jumlah_ayat' => $surah['jumlah_ayat'],
            ]);
        }
    }
}

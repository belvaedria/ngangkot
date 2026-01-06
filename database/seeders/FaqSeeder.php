<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faq;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'pertanyaan' => '💰 Berapa tarif naik angkot di Bandung?',
                'jawaban' => 'Tarif angkot di Bandung bervariasi berdasarkan jarak tempuh. 📏 Untuk jarak dekat (1-2 km) tarifnya Rp3.000 💵, jarak sedang (3-5 km) Rp4.000, dan jarak jauh atau lintas kota dalam Bandung Rp5.000. Sebaiknya siapkan uang pas 🪙 atau pecahan kecil untuk memudahkan pembayaran. ✨',
                'target' => 'penumpang',
            ],
            [
                'pertanyaan' => '💳 Bagaimana cara bayar ongkos angkot?',
                'jawaban' => 'Pembayaran angkot dilakukan saat akan turun atau di tengah perjalanan. ⏰ Gunakan sistem estafet 🤝: serahkan uang 💵 ke penumpang di depan Anda sambil menyebutkan nominal (misalnya "Bang, tiga ribu"), lalu penumpang akan meneruskan ke supir. Kembalian (jika ada) akan dikirim balik dengan cara yang sama. 🔄✨',
                'target' => 'penumpang',
            ],
            [
                'pertanyaan' => '🕐 Jam berapa angkot beroperasi di Bandung?',
                'jawaban' => 'Angkot di Bandung umumnya beroperasi mulai jam 5 pagi 🌅 hingga sekitar jam 8-9 malam 🌙. Jam sibuk ⏰ adalah pukul 06.30-08.30 (pagi) dan 16.30-18.30 (sore). Di hari libur atau weekend 📅, frekuensi angkot biasanya berkurang dan beberapa trayek berhenti lebih cepat. ⚠️',
                'target' => 'umum',
            ],
            [
                'pertanyaan' => '🚗 Bagaimana cara menjadi driver angkot?',
                'jawaban' => 'Untuk menjadi driver angkot, Anda perlu memiliki SIM A 🪪 yang masih berlaku, menguasai rute trayek tertentu 🗺️, dan bisa mengemudikan mobil dengan baik. 🚐 Biasanya Anda perlu menghubungi pemilik angkot atau koperasi angkot 🏢 untuk melamar. Beberapa pemilik menerima sistem bagi hasil 💰 atau setoran harian. ✨',
                'target' => 'pengemudi',
            ],
            [
                'pertanyaan' => '🔍 Apa yang harus dilakukan kalau ketinggalan barang di angkot?',
                'jawaban' => 'Jika ketinggalan barang, segera hubungi terminal atau pool angkot 📞 trayek yang Anda naiki. Berikan informasi sejelas mungkin seperti warna angkot 🎨, nomor plat (jika ingat) 🔢, jam naik ⏰, dan deskripsi barang 📦. Anda juga bisa melapor ke aplikasi Ngangkot 📱 atau media sosial komunitas angkot Bandung untuk bantuan. 🆘✨',
                'target' => 'umum',
            ],
            [
                'pertanyaan' => '💵 Berapa penghasilan rata-rata driver angkot per hari?',
                'jawaban' => 'Penghasilan driver angkot bervariasi tergantung trayek 🗺️, jam kerja ⏰, dan sistem kerja (bagi hasil atau setoran). 📊 Rata-rata driver bisa mendapat Rp100.000 - Rp200.000 💰 per hari setelah dikurangi setoran dan bensin. ⛽ Trayek ramai di jam sibuk biasanya lebih menguntungkan. 📈 Penghasilan juga dipengaruhi oleh skill mengemudi 🚗 dan pengetahuan rute. ✨',
                'target' => 'pengemudi',
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::create($faq);
        }
    }
}

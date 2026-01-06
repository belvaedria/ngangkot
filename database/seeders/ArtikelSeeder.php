<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Artikel;

class ArtikelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $artikels = [
            // TIPS (6 artikel)
            [
                'judul' => '🚐 Tips Naik Angkot untuk Pemula',
                'kategori' => 'tips',
                'konten' => "Naik angkot untuk pertama kalinya bisa bikin gugup, tapi tenang aja! 😊 Yang penting adalah percaya diri dan tau beberapa trik dasarnya. Pertama, perhatikan trayek angkot yang lewat. Setiap angkot punya kode trayek yang biasanya tertulis di bagian depan dan samping kendaraan. 👀\n\nKedua, lambaikan tangan ke arah angkot yang mau kamu tumpangi. 👋 Jangan malu-malu, supir angkot udah terbiasa kok. Mereka akan berhenti kalau ada yang ngelambai. Setelah naik, duduk di tempat yang nyaman dan siapkan uang pas atau uang kecil untuk ongkos. 💵\n\nTerakhir, kalau udah mau turun, ketuk pintu atau kaca jendela pelan-pelan sebagai tanda mau turun. Bilang \"Kiri bang!\" atau \"Turun bang!\" dengan sopan. 🙏 Bayar ongkos sesuai jarak tempuh, biasanya mulai dari Rp3.000 sampai Rp5.000 untuk jarak jauh.\n\nJangan lupa, kalau bingung soal rute atau ongkos, tanya aja sama penumpang lain atau supir. Orang Bandung ramah-ramah kok dan suka bantu! 🤝✨",
                'gambar' => null,
            ],
            [
                'judul' => '🎭 Etika Naik Angkot yang Perlu Kamu Tau',
                'kategori' => 'tips',
                'konten' => "Naik angkot bukan cuma soal bayar dan duduk aja, ada etika yang perlu kamu perhatikan biar perjalanan nyaman untuk semua penumpang. ✨ Pertama, jangan duduk di kursi yang dekat pintu kalau angkot masih sepi. Biarkan kursi itu kosong untuk penumpang yang naik berikutnya biar mereka gampang duduk. 🪑\n\nKedua, kalau angkot udah penuh, jangan maksa naik kalau emang udah ga ada tempat. ⛔ Tunggu angkot berikutnya aja. Kalau terpaksa berdiri, pegang pegangan yang ada dan jangan menghalangi pintu atau jalur orang yang mau turun.\n\nKetiga, jaga kebersihan! 🧹 Jangan buang sampah sembarangan di dalam angkot. Simpan sampahmu sampai ketemu tempat sampah. Kalau makan atau minum di dalam angkot, pastikan nggak berantakan dan ganggu penumpang lain. 🍱\n\nTerakhir, hormati penumpang lain. 🤝 Jangan teriak-teriak atau main musik kenceng dari HP. 🔇 Kalau ada ibu hamil, lansia, atau orang yang bawa bayi, tawarkan tempat dudukmu. 🤰👴👶 Sikap saling menghargai bikin perjalanan jadi lebih menyenangkan untuk semua! 💙",
                'gambar' => null,
            ],
            [
                'judul' => '🔒 Tips Keamanan Saat Naik Angkot',
                'kategori' => 'tips',
                'konten' => "Keamanan adalah prioritas utama saat naik angkot! 🔒 Pertama, selalu perhatikan barang bawaanmu. Simpan tas di pangkuan atau di antara kaki, jangan taruh di samping atau di belakang yang susah kamu awasi. 👜 HP dan dompet sebaiknya masuk ke dalam tas, jangan pegang di tangan terus. 📱💼\n\nKedua, pilih tempat duduk yang strategis. 🪑 Kalau angkot sepi, duduk di dekat supir atau di tengah yang gampang keluar. Hindari duduk sendirian di bagian paling belakang waktu malam hari. 🌙 Kalau merasa ada yang mencurigakan, percaya sama insting kamu dan turun di tempat ramai. 👀\n\nKetiga, jangan pamer barang berharga. ⚠️ Lepas perhiasan yang mencolok, simpan HP mahal, dan hindari hitung-hitung duit banyak di dalam angkot. Semakin low profile kamu, semakin aman. 🤐\n\nTerakhir, kalau naik angkot malam, kabari keluarga atau teman tentang trayek dan estimasi waktu sampai. 📲 Share location via WhatsApp juga bisa jadi opsi. Dan ingat, kalau merasa tidak aman, lebih baik turun dan cari transportasi lain. Keselamatan kamu lebih penting! ✨🛡️",
                'gambar' => null,
            ],
            [
                'judul' => '💰 Cara Hemat Ongkos Naik Angkot',
                'kategori' => 'tips',
                'konten' => "Naik angkot emang murah, tapi tetep bisa lebih hemat lagi kalau tau caranya! 💰✨ Pertama, pahami sistem tarif angkot. Biasanya ada tarif minimal untuk jarak dekat (sekitar Rp3.000) dan tarif maksimal untuk jarak jauh (sekitar Rp5.000). 💵 Jadi kalau jarakmu deket, jangan bayar lebih dari tarif minimal.\n\nKedua, cari rute langsung. 🗺️ Kadang kita bisa sampai tujuan dengan naik 2-3 angkot berbeda atau naik 1 angkot yang rutenya agak putar tapi langsung. Kalau jaraknya nggak terlalu jauh, pilih yang langsung meskipun sedikit memutar. Hemat ongkos dan waktu tunggu! ⏱️\n\nKetiga, siapkan uang pas. 🪙 Kalau bayar pake uang gede, kadang supir nggak ada kembalian dan kamu bisa rugi. Atau malah kamu yang bayar lebih karena nggak enak minta kembalian. Biasakan bawa uang receh Rp2.000, Rp5.000, atau Rp10.000. 💸\n\nTerakhir, pertimbangkan jalan kaki untuk jarak dekat. 🚶‍♂️ Kalau cuma 500 meter, mending jalan kaki aja daripada naik angkot yang minimal Rp3.000. Selain hemat, kamu juga olahraga. 💪 Pakai aplikasi Ngangkot juga bisa bantu kamu cari rute paling efisien dan murah! 📱",
                'gambar' => null,
            ],
            [
                'judul' => '⏰ Tips Naik Angkot di Jam Sibuk',
                'kategori' => 'tips',
                'konten' => "Jam sibuk di Bandung biasanya pagi (06.30-08.30) dan sore (16.30-18.30). ⏰ Di waktu-waktu ini, angkot bisa penuh banget dan perjalanan jadi lebih lama karena macet. 🚦 Tapi ada beberapa tips biar tetep nyaman.\n\nPertama, berangkat lebih awal. ⏱️ Kalau bisa, hindari peak hours dengan berangkat 30 menit lebih pagi atau lebih sore. Angkot lebih sepi, kamu dapat tempat duduk nyaman, dan waktu tempuh lebih cepat. ✅ Ini berlaku terutama kalau kamu mau ke kampus atau kantor. 🎓💼\n\nKedua, pilih posisi strategis saat antri. 📍 Kalau nunggu di halte atau terminal, posisikan diri kamu di depan. Jadi pas angkot dateng, kamu bisa naik duluan dan dapat tempat duduk. 🪑 Kalau udah penuh, tunggu angkot berikutnya aja daripada berdiri sesak. 🙅‍♂️\n\nKetiga, sabar dan tetep sopan. 😌 Jam sibuk bikin semua orang lelah dan kadang emosi. Tapi tetep jaga sikap, jangan saling dorong, dan hormati penumpang lain. 🤝 Kalau ada yang perlu turun, geser atau turun sebentar biar mereka bisa lewat.\n\nTerakhir, siapkan mental untuk delay. 🧘‍♂️ Macet di jam sibuk itu pasti. Jadi jangan stress, nikmati aja perjalanannya, dengerin musik 🎵, atau baca artikel di HP. Yang penting tetep aware sama barang bawaan! 👜",
                'gambar' => null,
            ],
            [
                'judul' => '💬 Tips Berkomunikasi dengan Supir Angkot',
                'kategori' => 'tips',
                'konten' => "Komunikasi yang baik sama supir angkot bikin perjalanan jadi lebih lancar dan nyaman. 💬✨ Pertama, gunakan bahasa yang sopan dan jelas. 🗣️ Pas mau turun, bilang \"Kiri bang!\" atau \"Turun bang!\" dengan suara yang cukup keras biar kedengaran. Jangan teriak, tapi pastikan supir ngedenger. 👂\n\nKedua, kalau mau nanya rute atau ongkos, tanya pas angkot lagi berhenti atau jalan pelan. 🛑 Jangan ganggu supir pas lagi konsentrasi nyetir di jalan ramai atau tikungan. Keselamatan lebih penting. 🚗 Tanya dengan sopan, misalnya \"Bang, ini lewat Dago nggak?\" atau \"Bang, ongkos ke Cihampelas berapa?\" ❓\n\nKetiga, hormati keputusan supir. 🙏 Kalau supir bilang angkot udah penuh atau nggak lewat rute yang kamu mau, terima aja. Jangan maksa atau debat. ❌ Cari angkot lain yang sesuai. Ingat, supir juga manusia yang kerja keras cari nafkah. 💪\n\nTerakhir, kasih apresiasi kalau supir ramah atau helpful. 😊 Senyum dan ucapan terima kasih itu sederhana tapi berarti. Kalau supir bantu jawab pertanyaan atau turunin kamu di tempat yang pas, ucapkan terima kasih. 🤗 Sikap positif bikin hubungan supir-penumpang jadi lebih baik! 💙",
                'gambar' => null,
            ],

            // PANDUAN (6 artikel)
            [
                'judul' => '📖 Panduan Lengkap Naik Angkot Pertama Kali',
                'kategori' => 'panduan',
                'konten' => "Buat kamu yang baru pertama kali mau naik angkot di Bandung, jangan khawatir! 😊 Panduan ini akan bantu kamu dari awal sampai akhir. Pertama-tama, kamu perlu tau trayek angkot yang sesuai dengan tujuanmu. 📍 Download aplikasi Ngangkot 📱 atau tanya orang sekitar tentang angkot mana yang lewat ke tempat tujuanmu.\n\nSetelah tau trayeknya, tunggu angkot di pinggir jalan yang dilalui trayek tersebut. 🛣️ Perhatikan kode trayek yang tertulis di bagian depan atau samping angkot. 👀 Kalau udah ketemu angkot yang sesuai, lambaikan tangan. 👋 Supir akan berhenti dan kamu bisa naik.\n\nSetelah naik, duduk di tempat yang kosong. 🪑 Jangan khawatir soal bayar dulu atau belakangan - di angkot Bandung, pembayaran biasanya dilakukan saat kamu mau turun atau di tengah perjalanan. 💵 Bayar sesuai jarak: Rp3.000 untuk jarak dekat, Rp4.000 untuk jarak sedang, dan Rp5.000 untuk jarak jauh.\n\nSaat udah mau turun, ketuk pintu atau kaca jendela sambil bilang \"Kiri bang!\" 🚪 Angkot akan berhenti di tempat yang aman. Turun dengan hati-hati, bayar ongkos lewat supir atau penumpang di depan (sistem estafet), dan jangan lupa ucapkan terima kasih. 🙏 Selamat! Kamu udah berhasil naik angkot pertama kali! 🎉",
                'gambar' => null,
            ],
            [
                'judul' => '💳 Panduan Sistem Pembayaran Angkot',
                'kategori' => 'panduan',
                'konten' => "Sistem pembayaran angkot di Bandung cukup unik dan beda dari transportasi umum lainnya. 🎫 Pertama, kamu perlu tau kalau pembayaran angkot tidak dilakukan di awal seperti bus. ⏰ Kamu bayar saat mau turun atau kadang di tengah perjalanan kalau supir minta.\n\nTarif angkot di Bandung umumnya berkisar Rp3.000 sampai Rp5.000 tergantung jarak. 💰 Untuk jarak dekat (1-2 km), bayar Rp3.000. 📏 Jarak sedang (3-5 km) bayar Rp4.000. Dan jarak jauh atau lintas kota dalam Bandung bayar Rp5.000. Kalau kamu nggak yakin, tanya penumpang lain atau liat penumpang lain bayar berapa. 👥\n\nCara bayarnya juga unik - pakai sistem estafet! 🤝 Kalau kamu duduk di belakang, kasih uang ke penumpang di depan kamu sambil bilang \"Bang, tiga ribu\" (sesuai ongkos yang kamu bayar). 💵 Penumpang depan akan terusin ke supir. Kalau ada kembalian, akan dioper balik lewat penumpang juga. 🔄\n\nTips penting: siapkan uang pas atau pecahan kecil! ⚠️ Supir sering kesulitan kasih kembalian kalau kamu bayar pake uang Rp50.000 atau Rp100.000 untuk ongkos Rp3.000. Bawa uang receh Rp2.000, Rp5.000, atau maksimal Rp10.000 biar lancar. 🪙✨",
                'gambar' => null,
            ],
            [
                'judul' => '🗺️ Panduan Rute Angkot Populer di Bandung',
                'kategori' => 'panduan',
                'konten' => "Bandung punya banyak trayek angkot yang melayani berbagai rute. 🚐 Berikut beberapa rute populer yang sering dipakai warga dan wisatawan. Pertama, ada Angkot Abdul Muis - Cicaheum yang melayani rute dari terminal Cicaheum ke pusat kota lewat jalan utama. 🏢\n\nKedua, Angkot Ciroyom - Cicaheum yang populer karena lewat kawasan perdagangan dan perkantoran. 💼 Angkot ini cocok kalau kamu mau ke area Dago, Dipatiukur, atau Cicadas. Ketiga, Angkot Ciroyom - Cibeureum yang melayani area Cihampelas dan sekitarnya, tempat favorit buat shopping. 🛍️\n\nKeempat, ada Angkot Ledeng - Dago yang melayani kawasan Dago atas sampai bawah. 🎓 Ini angkot favorit mahasiswa karena lewat beberapa kampus. Kelima, Angkot Stasiun Hall - Dago yang cocok buat kamu yang baru turun dari kereta 🚂 dan mau ke area Dago atau pusat kota.\n\nUntuk rute lengkap dan detail setiap trayek, kamu bisa download aplikasi Ngangkot 📱 atau tanya warga sekitar. Setiap trayek punya kode dan warna yang berbeda 🎨, jadi pastikan kamu naik angkot yang tepat. Jangan malu buat nanya supir atau penumpang lain kalau ragu! 💬✨",
                'gambar' => null,
            ],
            [
                'judul' => '🕐 Panduan Jam Operasional Angkot Bandung',
                'kategori' => 'panduan',
                'konten' => "Angkot di Bandung nggak beroperasi 24 jam seperti transportasi online. ⏰ Ada jam-jam tertentu dimana angkot mulai dan berhenti beroperasi. Secara umum, angkot mulai beroperasi dari jam 5 pagi. 🌅 Ini waktu dimana angkot-angkot mulai keluar dari terminal atau pool untuk melayani penumpang pagi.\n\nJam puncak atau jam sibuk angkot adalah pukul 06.30 - 08.30 pagi (orang berangkat kerja/sekolah) 🎒💼 dan 16.30 - 18.30 sore (orang pulang kerja/sekolah). 🏠 Di jam-jam ini, angkot biasanya penuh dan perjalanan bisa lebih lama karena macet. 🚦 Kalau bisa hindari jam-jam ini kalau nggak suka keramaian.\n\nAngkot mulai jarang lewat setelah jam 8 malam. 🌙 Beberapa trayek bahkan berhenti beroperasi jam 7 atau 8 malam. Jadi kalau kamu ada acara malam, siapkan opsi transportasi lain untuk pulang seperti ojek online atau taksi. 🚕 Jangan sampai nunggu angkot yang nggak akan dateng. ⚠️\n\nKhusus weekend atau hari libur, frekuensi angkot biasanya berkurang. 📅 Kamu mungkin perlu nunggu lebih lama. ⏳ Beberapa supir libur atau pulang lebih cepat di weekend. Jadi pastikan kamu planning perjalanan dengan baik, terutama kalau mau pergi sore atau malam. 📝✨",
                'gambar' => null,
            ],
            [
                'judul' => '🔢 Panduan Membaca Kode Trayek Angkot',
                'kategori' => 'panduan',
                'konten' => "Setiap angkot di Bandung punya kode trayek yang unik. 🏷️ Kode ini biasanya berupa kombinasi huruf dan angka yang tertulis di bagian depan dan samping angkot. 🚐 Memahami kode ini penting biar kamu nggak salah naik angkot. ✅ Mari kita pelajari sistem penamaan trayek angkot Bandung.\n\nKode trayek biasanya terdiri dari nama-nama tempat awal dan akhir rute. 📍 Misalnya \"Cicaheum - Ciroyom\" berarti angkot ini beroperasi dari Cicaheum ke Ciroyom (dan sebaliknya). 🔄 Nama-nama ini adalah terminal atau landmark penting di Bandung. Jadi kalau tujuanmu ada di antara kedua titik itu, angkot ini bisa kamu pakai. 🎯\n\nSelain nama, angkot juga sering dibedakan dengan warna. 🎨 Misalnya ada angkot warna biru 🔵, merah 🔴, kuning 🟡, atau kombinasi warna. Warna ini juga bisa jadi penanda trayek tertentu. Tapi jangan andalkan warna aja, tetep liat nama trayeknya untuk memastikan. ⚠️\n\nKalau kamu bingung atau nggak hapal kode trayek, cara termudah adalah dengan download aplikasi Ngangkot. 📱 Aplikasi ini akan bantu kamu cari trayek yang tepat berdasarkan lokasi awal dan tujuan. 🗺️ Atau kamu bisa tanya sama satpam, penjaga toko, atau penumpang lain yang nunggu angkot. 💬 Orang Bandung biasanya helpful kok! 🤗✨",
                'gambar' => null,
            ],
            [
                'judul' => '🧳 Panduan Naik Angkot Bawa Barang Banyak',
                'kategori' => 'panduan',
                'konten' => "Kadang kita perlu naik angkot sambil bawa barang banyak, entah belanjaan 🛍️, koper 🧳, atau kardus 📦. Ada etika dan tips khusus buat kondisi ini biar nggak ganggu penumpang lain. Pertama, pastikan barang yang kamu bawa nggak terlalu besar atau banyak. ⚠️ Kalau barangnya sangat besar seperti kulkas atau kasur, jelas nggak bisa naik angkot. ❌ Pertimbangkan pakai pickup atau angkutan barang. 🚚\n\nKalau barangnya masih wajar seperti koper, tas belanjaan, atau kardus berukuran sedang, kamu bisa naik angkot. ✅ Tapi duduk di bagian belakang yang lebih lapang. 🪑 Taruh barangmu di bawah kaki atau di samping yang nggak menghalangi jalur orang lalu lalang. Jangan sampai barangmu bikin penumpang lain susah duduk atau gerak. 🚶‍♂️\n\nKadang supir angkot minta tambahan ongkos kalau barangmu besar dan makan tempat. 💵 Ini wajar karena space yang kamu pakai lebih banyak. Biasanya tambahan sekitar Rp1.000 - Rp2.000. Jangan protes, karena ini sudah kebiasaan umum dan supir juga perlu kompensasi untuk kehilangan space penumpang lain. 🤝\n\nTips penting: pegang barangmu dengan erat, terutama kalau angkot lagi jalan. 🚐 Jangan sampai barangmu jatuh atau bergeser dan kena penumpang lain. ⚡ Kalau ada penumpang mau lewat atau turun, bantu angkat atau geser barangmu biar mereka bisa lewat. Sikap kooperatif bikin semua penumpang nyaman! 😊✨",
                'gambar' => null,
            ],
        ];

        foreach ($artikels as $artikel) {
            Artikel::create($artikel);
        }
    }
}

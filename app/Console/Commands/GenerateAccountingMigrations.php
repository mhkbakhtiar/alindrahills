<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateAccountingMigrations extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'accounting:generate-migrations';

    /**
     * The console command description.
     */
    protected $description = 'Generate accounting system migrations (Perkiraan, Jurnal, Item Jurnal, etc.)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Generating Accounting Migrations...');
        
        $migrations = [
            'adjust_users_table',
            'adjust_project_locations_table',
            'create_departemen_table',
            'create_tahun_anggaran_table',
            'create_perkiraan_table',
            'create_jurnal_table',
            'create_item_jurnal_table',
            'create_cicilan_kavling_table',
            'create_pengeluaran_bulanan_table',
        ];

        $counter = 0;
        foreach ($migrations as $migration) {
            $this->createMigration($migration, $counter);
            $counter++;
        }

        $this->info('✅ All migrations generated successfully!');
        $this->info('📝 Run: php artisan migrate');
        
        return 0;
    }

    /**
     * Create a migration file
     */
    private function createMigration($name, $counter)
    {
        $timestamp = now()->addSeconds($counter)->format('Y_m_d_His');
        $filename = database_path("migrations/{$timestamp}_{$name}.php");

        $content = $this->getStubContent($name);

        File::put($filename, $content);
        $this->line("✓ Created: {$name}");
    }

    /**
     * Get the stub content for the migration
     */
    private function getStubContent($name)
    {
        $stubPath = __DIR__ . '/stubs/' . $name . '.stub';
        
        // If stub exists, use it, otherwise use inline content
        if (File::exists($stubPath)) {
            return File::get($stubPath);
        }
        
        return $this->getInlineStub($name);
    }

    /**
     * Get inline stub content
     */
    private function getInlineStub($name)
    {
        return match($name) {
            'adjust_users_table' => $this->getAdjustUsersStub(),
            'adjust_project_locations_table' => $this->getAdjustProjectLocationsStub(),
            'create_departemen_table' => $this->getDepartemenStub(),
            'create_tahun_anggaran_table' => $this->getTahunAnggaranStub(),
            'create_perkiraan_table' => $this->getPerkiraanStub(),
            'create_jurnal_table' => $this->getJurnalStub(),
            'create_item_jurnal_table' => $this->getItemJurnalStub(),
            'create_cicilan_kavling_table' => $this->getCicilanKavlingStub(),
            'create_pengeluaran_bulanan_table' => $this->getPengeluaranBulananStub(),
            default => '',
        };
    }

    private function getAdjustUsersStub()
    {
        return '<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(\'users\', function (Blueprint $table) {
            if (!Schema::hasColumn(\'users\', \'no_hp\')) {
                $table->string(\'no_hp\', 20)->nullable();
            }
            if (!Schema::hasColumn(\'users\', \'alamat\')) {
                $table->text(\'alamat\')->nullable();
            }
            if (!Schema::hasColumn(\'users\', \'deleted_at\')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table(\'users\', function (Blueprint $table) {
            $table->dropColumn([\'no_hp\', \'alamat\', \'deleted_at\']);
        });
    }
};';
    }

    private function getAdjustProjectLocationsStub()
    {
        return '<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(\'project_locations\', function (Blueprint $table) {
            if (!Schema::hasColumn(\'project_locations\', \'user_id\')) {
                $table->unsignedBigInteger(\'user_id\')->nullable()->comment(\'Pemilik Kavling\');
                $table->foreign(\'user_id\')->references(\'user_id\')->on(\'users\')->onDelete(\'set null\');
            }
            
            if (!Schema::hasColumn(\'project_locations\', \'luas_tanah\')) {
                $table->decimal(\'luas_tanah\', 10, 2)->nullable();
            }
            
            if (!Schema::hasColumn(\'project_locations\', \'luas_bangunan\')) {
                $table->decimal(\'luas_bangunan\', 10, 2)->nullable();
            }
            
            if (!Schema::hasColumn(\'project_locations\', \'tipe_rumah\')) {
                $table->string(\'tipe_rumah\', 50)->nullable();
            }
            
            if (!Schema::hasColumn(\'project_locations\', \'status_kavling\')) {
                $table->enum(\'status_kavling\', [\'tersedia\', \'booking\', \'dp\', \'cicilan\', \'lunas\', \'proses_bangun\', \'serah_terima\'])->default(\'tersedia\');
            }
            
            if (!Schema::hasColumn(\'project_locations\', \'harga_jual\')) {
                $table->decimal(\'harga_jual\', 15, 2)->nullable();
            }
            
            if (!Schema::hasColumn(\'project_locations\', \'total_dibayar\')) {
                $table->decimal(\'total_dibayar\', 15, 2)->default(0);
            }
            
            if (!Schema::hasColumn(\'project_locations\', \'deleted_at\')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table(\'project_locations\', function (Blueprint $table) {
            $table->dropForeign([\'user_id\']);
            $table->dropColumn([\'user_id\', \'luas_tanah\', \'luas_bangunan\', \'tipe_rumah\', \'status_kavling\', \'harga_jual\', \'total_dibayar\', \'deleted_at\']);
        });
    }
};';
    }

    private function getDepartemenStub()
    {
        return '<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(\'departemen\', function (Blueprint $table) {
            $table->id();
            $table->string(\'kode_departemen\', 10)->unique();
            $table->string(\'nama_departemen\', 100);
            $table->text(\'deskripsi\')->nullable();
            $table->unsignedBigInteger(\'kepala_departemen\')->nullable();
            $table->foreign(\'kepala_departemen\')->references(\'user_id\')->on(\'users\')->onDelete(\'set null\');
            $table->boolean(\'is_active\')->default(true);
            $table->timestamps();
        });
        
        DB::table(\'departemen\')->insert([
            [\'kode_departemen\' => \'BT\', \'nama_departemen\' => \'Bebas Tanah\', \'created_at\' => now(), \'updated_at\' => now()],
            [\'kode_departemen\' => \'TK\', \'nama_departemen\' => \'Teknik\', \'created_at\' => now(), \'updated_at\' => now()],
            [\'kode_departemen\' => \'PR\', \'nama_departemen\' => \'Perijinan\', \'created_at\' => now(), \'updated_at\' => now()],
            [\'kode_departemen\' => \'MK\', \'nama_departemen\' => \'Marketing\', \'created_at\' => now(), \'updated_at\' => now()],
            [\'kode_departemen\' => \'KU\', \'nama_departemen\' => \'Keuangan\', \'created_at\' => now(), \'updated_at\' => now()],
            [\'kode_departemen\' => \'UM\', \'nama_departemen\' => \'Umum\', \'created_at\' => now(), \'updated_at\' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists(\'departemen\');
    }
};';
    }

    private function getTahunAnggaranStub()
    {
        return '<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(\'tahun_anggaran\', function (Blueprint $table) {
            $table->id();
            $table->year(\'tahun\')->unique();
            $table->date(\'periode_awal\');
            $table->date(\'periode_akhir\');
            $table->enum(\'status\', [\'aktif\', \'tutup_buku\'])->default(\'aktif\');
            $table->text(\'keterangan\')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(\'tahun_anggaran\');
    }
};';
    }

    private function getPerkiraanStub()
    {
        return '<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(\'perkiraan\', function (Blueprint $table) {
            $table->id();
            $table->string(\'kode_perkiraan\', 10)->unique();
            $table->string(\'nama_perkiraan\', 255);
            $table->decimal(\'saldo_debet\', 20, 2)->default(0);
            $table->decimal(\'saldo_kredit\', 20, 2)->default(0);
            $table->decimal(\'anggaran\', 20, 2)->nullable();
            $table->enum(\'jenis_akun\', [\'Aset\', \'Kewajiban\', \'Modal\', \'Pendapatan\', \'Biaya\']);
            $table->enum(\'kategori\', [\'Lancar\', \'Tetap\', \'Jangka Panjang\', \'Persediaan\', \'Operasional\', \'Non Operasional\'])->nullable();
            $table->string(\'departemen\', 50)->nullable();
            $table->foreignId(\'parent_id\')->nullable()->constrained(\'perkiraan\')->onDelete(\'set null\');
            $table->integer(\'level\')->default(1);
            $table->boolean(\'is_active\')->default(true);
            $table->boolean(\'is_header\')->default(false);
            $table->boolean(\'is_cash_bank\')->default(false);
            $table->boolean(\'is_hpp\')->default(false);
            $table->text(\'keterangan\')->nullable();
            $table->date(\'tanggal\')->nullable();
            $table->unsignedBigInteger(\'created_by\')->nullable();
            $table->foreign(\'created_by\')->references(\'user_id\')->on(\'users\')->onDelete(\'set null\');
            $table->unsignedBigInteger(\'updated_by\')->nullable();
            $table->foreign(\'updated_by\')->references(\'user_id\')->on(\'users\')->onDelete(\'set null\');
            $table->timestamps();
            $table->softDeletes();
            $table->index(\'kode_perkiraan\');
            $table->index(\'jenis_akun\');
            $table->index(\'departemen\');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(\'perkiraan\');
    }
};';
    }

    private function getJurnalStub()
    {
        return '<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(\'jurnal\', function (Blueprint $table) {
            $table->id();
            $table->string(\'nomor_bukti\', 50)->unique();
            $table->date(\'tanggal\');
            $table->text(\'keterangan\')->nullable();
            $table->enum(\'jenis_jurnal\', [\'umum\', \'penyesuaian\', \'penutup\', \'pembalik\'])->default(\'umum\');
            $table->string(\'departemen\', 50)->nullable();
            $table->foreignId(\'id_tahun_anggaran\')->nullable()->constrained(\'tahun_anggaran\')->onDelete(\'set null\');
            $table->unsignedBigInteger(\'created_by\')->nullable();
            $table->foreign(\'created_by\')->references(\'user_id\')->on(\'users\')->onDelete(\'set null\');
            $table->unsignedBigInteger(\'updated_by\')->nullable();
            $table->foreign(\'updated_by\')->references(\'user_id\')->on(\'users\')->onDelete(\'set null\');
            $table->enum(\'status\', [\'draft\', \'posted\', \'void\'])->default(\'posted\');
            $table->timestamps();
            $table->softDeletes();
            $table->index(\'nomor_bukti\');
            $table->index(\'tanggal\');
            $table->index(\'status\');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(\'jurnal\');
    }
};';
    }

    private function getItemJurnalStub()
    {
        return '<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(\'item_jurnal\', function (Blueprint $table) {
            $table->id();
            $table->foreignId(\'id_jurnal\')->constrained(\'jurnal\')->onDelete(\'cascade\');
            $table->string(\'kode_perkiraan\', 10);
            $table->foreign(\'kode_perkiraan\')->references(\'kode_perkiraan\')->on(\'perkiraan\')->onDelete(\'restrict\');
            $table->string(\'kode_kavling\', 10)->nullable();
            $table->foreign(\'kode_kavling\')->references(\'kavling\')->on(\'project_locations\')->onDelete(\'set null\');
            $table->unsignedBigInteger(\'id_user\')->nullable();
            $table->foreign(\'id_user\')->references(\'user_id\')->on(\'users\')->onDelete(\'set null\');
            $table->text(\'keterangan\')->nullable();
            $table->decimal(\'debet\', 20, 2)->default(0);
            $table->decimal(\'kredit\', 20, 2)->default(0);
            $table->integer(\'urutan\')->default(0);
            $table->timestamps();
            $table->index(\'id_jurnal\');
            $table->index(\'kode_perkiraan\');
            $table->index(\'kode_kavling\');
            $table->index(\'id_user\');
            $table->index([\'kode_perkiraan\', \'kode_kavling\']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(\'item_jurnal\');
    }
};';
    }

    private function getCicilanKavlingStub()
    {
        return '<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(\'cicilan_kavling\', function (Blueprint $table) {
            $table->id();
            $table->string(\'kode_kavling\', 10);
            $table->foreign(\'kode_kavling\')->references(\'kavling\')->on(\'project_locations\')->onDelete(\'cascade\');
            $table->unsignedBigInteger(\'id_user\');
            $table->foreign(\'id_user\')->references(\'user_id\')->on(\'users\')->onDelete(\'cascade\');
            $table->integer(\'nomor_cicilan\');
            $table->date(\'tanggal_jatuh_tempo\');
            $table->decimal(\'jumlah\', 15, 2);
            $table->enum(\'status\', [\'belum_bayar\', \'sudah_bayar\', \'telat\'])->default(\'belum_bayar\');
            $table->date(\'tanggal_bayar\')->nullable();
            $table->foreignId(\'id_jurnal_pembayaran\')->nullable()->constrained(\'jurnal\')->onDelete(\'set null\');
            $table->text(\'keterangan\')->nullable();
            $table->timestamps();
            $table->index([\'kode_kavling\', \'nomor_cicilan\']);
            $table->index(\'status\');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(\'cicilan_kavling\');
    }
};';
    }

    private function getPengeluaranBulananStub()
    {
        return '<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(\'pengeluaran_bulanan\', function (Blueprint $table) {
            $table->id();
            $table->date(\'bulan\');
            $table->string(\'kategori\', 100)->nullable();
            $table->text(\'keterangan\');
            $table->decimal(\'jumlah\', 15, 2);
            $table->unsignedBigInteger(\'created_by\')->nullable();
            $table->foreign(\'created_by\')->references(\'user_id\')->on(\'users\')->onDelete(\'set null\');
            $table->timestamps();
            $table->index(\'bulan\');
            $table->index(\'kategori\');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(\'pengeluaran_bulanan\');
    }
};';
    }
}
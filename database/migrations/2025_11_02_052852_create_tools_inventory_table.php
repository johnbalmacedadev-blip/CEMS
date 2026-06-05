<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tools_inventory', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('quantity')->default(1);
            $table->decimal('amount', 10, 2);
            $table->date('date_acquired');
            $table->timestamps();
        });

        // Seed initial data from the image
        $tools = [
            // Date: 2-Aug-25
            ['name' => 'FLOOR JAC', 'quantity' => 1, 'amount' => 12000.00, 'date_acquired' => '2025-08-02'],
            ['name' => '12PCS OFF SET TOOLS', 'quantity' => 1, 'amount' => 2220.00, 'date_acquired' => '2025-08-02'],
            ['name' => '152PCS SET CIWLI2050-24353910191 (IMPACT WRENCH)', 'quantity' => 1, 'amount' => 8625.00, 'date_acquired' => '2025-08-02'],
            ['name' => 'CURVED JAW LOCKING PLIER', 'quantity' => 1, 'amount' => 450.00, 'date_acquired' => '2025-08-02'],
            ['name' => 'ADJUSTABLE WRENCH', 'quantity' => 1, 'amount' => 420.00, 'date_acquired' => '2025-08-02'],
            ['name' => '18PCS SCREW', 'quantity' => 1, 'amount' => 700.00, 'date_acquired' => '2025-08-02'],
            ['name' => '4PCS PLIER SET', 'quantity' => 1, 'amount' => 1250.00, 'date_acquired' => '2025-08-02'],
            ['name' => 'JACK STAND', 'quantity' => 2, 'amount' => 3900.00, 'date_acquired' => '2025-08-02'],
            ['name' => 'RUBBER AND PLASTIC HAMMER', 'quantity' => 1, 'amount' => 360.00, 'date_acquired' => '2025-08-02'],
            ['name' => 'BALLPEEN HAMMER', 'quantity' => 1, 'amount' => 315.00, 'date_acquired' => '2025-08-02'],
            
            // Date: 14 Aug 2025
            ['name' => 'PIPE WRENCH 10', 'quantity' => 1, 'amount' => 2575.00, 'date_acquired' => '2025-08-14'],
            ['name' => 'PIPE WRENCH 12', 'quantity' => 1, 'amount' => 2745.00, 'date_acquired' => '2025-08-14'],
            
            // Date: 15 Aug 2025
            ['name' => 'POWERCRAFT GRINDER 4 PAG 8-105SE (ALYSSA - ANNEX)', 'quantity' => 1, 'amount' => 1344.00, 'date_acquired' => '2025-08-15'],
            
            // Date: 16 Aug 2025
            ['name' => 'DUAL MANIFOLD GAUGE', 'quantity' => 1, 'amount' => 3300.00, 'date_acquired' => '2025-08-16'],
            ['name' => '1 PC VLV RMVR', 'quantity' => 1, 'amount' => 40.00, 'date_acquired' => '2025-08-16'],
            ['name' => 'CFLR HIGH SIDE CONNECTOR AND LOW (ROTARY TRADING)', 'quantity' => 2, 'amount' => 700.00, 'date_acquired' => '2025-08-16'],
            
            // Date: 17 Aug 2025
            ['name' => 'ELECTRIC SOLDERING IRON - TOOL MWL CORP', 'quantity' => 1, 'amount' => 480.00, 'date_acquired' => '2025-08-17'],
            ['name' => 'EXTENSION WIRE - TOOL MWL CORP', 'quantity' => 1, 'amount' => 610.00, 'date_acquired' => '2025-08-17'],
            
            // Date: 22 Aug 2025
            ['name' => 'VERNIER CALIPER', 'quantity' => 1, 'amount' => 765.00, 'date_acquired' => '2025-08-22'],
        ];

        $now = now();
        foreach ($tools as $tool) {
            DB::table('tools_inventory')->insert([
                'name' => $tool['name'],
                'quantity' => $tool['quantity'],
                'amount' => $tool['amount'],
                'date_acquired' => $tool['date_acquired'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tools_inventory');
    }
};
<?php

use App\Models\CompanyBank;
use App\Models\Company;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteBankInfoOnCompay extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // $companies = Company::all();

        // foreach ($companies as $c) {
        //     CompanyBank::create([
        //         'company_id' => $c->id,
        //         'bank_name' => $c->bank_name,
        //         'bank_branch' => $c->bank_branch,
        //         'account_number' => $c->account_number,
        //         'account_holder' => $c->account_holder,
        //         'active' => 1
        //     ]);
        // }

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['bank_name', 'bank_branch', 'account_number', 'account_holder']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_holder')->nullable();
        });
    }
}

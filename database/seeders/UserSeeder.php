<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Account;
use App\Models\Customer;
use App\Models\Person;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1 ; $i <= 100; $i++){

            DB::table('users')->insert([
                'password' => bcrypt('Matkhau'.$i),
                'username'  => "testlogin".$i,
                'role' =>"customer",
                'created_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
            ]);
            $account = new Account();
            $account->username = "testlogin".$i;
            $account->password =bcrypt('Matkhau'.$i);
            $account->role = 'customer';
            $account->save();
            # add person
            $person = new Person();
            $person->account_id = $account->id;
            $person->full_name = "full_name".$i;
            $person->gender = "Nam";
            $person->address = "Hải Phòng";
            $person->date_of_birth =  Carbon::parse('1997-10-12');
            $person->phone = '0788337682';
            $person->email ="testlogin".$i."@gmail.com";
            $person->save();
            # add customer
            $customer = new Customer();
            $customer->person_id = $person->id;
            $customer->save();
        }
    }
}

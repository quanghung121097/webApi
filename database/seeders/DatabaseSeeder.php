<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use App\Models\Account;
use App\Models\Customer;
use App\Models\Person;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
       /* DB::table('slide')->insert([
        	['name'=> 'slide1.png'],
        	['name'=> 'slide2.png'],
        	['name'=> 'slide3.png'],
        ]);*/
        // DB::table('account')->insert(
        //     ['username'=>'customertest3','password' => bcrypt('12345'),'role'=>'customer'],
        // );
        for ($i = 1 ; $i <= 100; $i++){
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

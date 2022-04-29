<?php

declare(strict_types=1);

use App\Shop\Model\ShopUser;
use Carbon\Carbon;
use Faker\Factory;
use Hyperf\Database\Seeders\Seeder;
use Hyperf\DbConnection\Db;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class ShopAddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function run()
    {
        $faker = Factory::create('zh_CN');
        Db::table('shop_addresses')->truncate();
        $users = ShopUser::pluck('id');
        $data = [];
        $locations = [
            ['湖北省', '黄冈市', '英山县'],
            ['上海市', '上海市', '闵行区'],
            ['江苏省', '苏州市', '姑苏区'],
            ['湖北省', '武汉市', '洪山区'],
            ['湖南省', '长沙市', '浏阳市'],
            ["北京市", "市辖区", "东城区"],
            ["河北省", "石家庄市", "长安区"],
            ["江苏省", "南京市", "浦口区"],
            ["江苏省", "苏州市", "相城区"],
            ["广东省", "深圳市", "福田区"]
        ];

        $start = 0;
        while ($start < 1000) {
            $location = $faker->randomElement($locations);
            $data[] = [
                'id' => snowflake_id(),
                'user_id' => $faker->randomElement($users->toArray()),
                'province' => $location[0],
                'city' => $location[1],
                'district' => $location[2],
                'address' => sprintf('第%d街道第%d号', $faker->randomNumber(2), $faker->randomNumber(3)),
                'zip' => $faker->postcode,
                'contact_phone' => $faker->phoneNumber,
                'contact_name' => $faker->name,
                'last_used_at' => Carbon::now()->toDateTimeString(),
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ];
            $start++;
        }

        DB::table('shop_addresses')->insert($data);
    }
}

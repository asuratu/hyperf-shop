<?php

declare(strict_types=1);

use App\Shop\Model\ShopProducts;
use Carbon\Carbon;
use Faker\Factory;
use Hyperf\Database\Seeders\Seeder;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class ShopProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function run(): void
    {
        $faker = Factory::create('zh_CN');
        $start = 0;
        $now = Carbon::now()->toDateTimeString();
        $skuList = ['颜色', '尺寸', '大小', '高低', '形状'];
        $images = [
            "https://cdn.learnku.com/uploads/images/201806/01/5320/7kG1HekGK6.jpg",
            "https://cdn.learnku.com/uploads/images/201806/01/5320/1B3n0ATKrn.jpg",
            "https://cdn.learnku.com/uploads/images/201806/01/5320/r3BNRe4zXG.jpg",
            "https://cdn.learnku.com/uploads/images/201806/01/5320/C0bVuKB2nt.jpg",
            "https://cdn.learnku.com/uploads/images/201806/01/5320/82Wf2sg8gM.jpg",
            "https://cdn.learnku.com/uploads/images/201806/01/5320/nIvBAQO5Pj.jpg",
            "https://cdn.learnku.com/uploads/images/201806/01/5320/XrtIwzrxj7.jpg",
            "https://cdn.learnku.com/uploads/images/201806/01/5320/uYEHCJ1oRp.jpg",
            "https://cdn.learnku.com/uploads/images/201806/01/5320/2JMRaFwRpo.jpg",
            "https://cdn.learnku.com/uploads/images/201806/01/5320/pa7DrV43Mw.jpg",
        ];

        while ($start < 100) {
            // 准备sku数据
            $skuStart = 0;
            $skuData = [];
            while ($skuStart < 5) {
                $skuData[] = [
                    'id' => snowflake_id(),
                    'title' => $skuList[$skuStart],
                    'description' => $faker->word,
                    'price' => $faker->randomFloat(2, 1, 100),
                    'stock' => mt_rand(10, 100),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $skuStart++;
            }

            $product = ShopProducts::create([
                'id' => snowflake_id(),
                'title' => $faker->word,
                'description' => $faker->sentence,
                'image' => $faker->randomElement($images),
                'on_sale' => mt_rand(0, 1),
                'rating' => $faker->randomFloat(2, 1, 9),
                'sold_count' => mt_rand(1000, 9999),
                'review_count' => mt_rand(1000, 9999),
                'price' => collect($skuData)->min('price'),
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $product->skus()->createMany($skuData);
            $start++;
        }
    }
}

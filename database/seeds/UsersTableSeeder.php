<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 获取 Faker 实例
        $faker = app(Faker\Generator::class);

        // 头像假数据
        $avatars = [
            'https://cdn.learnku.com/uploads/images/201710/14/1/s5ehp11z6s.png',
            'https://cdn.learnku.com/uploads/images/201710/14/1/Lhd1SHqu86.png',
            'https://cdn.learnku.com/uploads/images/201710/14/1/LOnMrqbHJn.png',
            'https://cdn.learnku.com/uploads/images/201710/14/1/xAuDMxteQy.png',
            'https://cdn.learnku.com/uploads/images/201710/14/1/ZqM7iaP4CR.png',
            'https://cdn.learnku.com/uploads/images/201710/14/1/NDnzMutoxX.png',
        ];

        // 生成数据集合
        $users = factory(User::class)
                        ->times(200)
                        ->make()
                        ->each(function ($user, $index)
                            use ($faker, $avatars)
        {
            // 从头像数组中随机取出一个并赋值
            $user->avatar = $faker->randomElement($avatars);
        });

        // 让隐藏字段可见，并将数据集合转换为数组
        $user_array = $users->makeVisible(['password', 'remember_token'])->toArray();

        // 插入到数据库中
        User::insert($user_array);

        // 单独处理第一个用户的数据 Founder  Maintainer
        $user = User::find(1);
        $user->name = 'Huang01';
        $user->email = '875986139@qq.com';
        $user->avatar = 'http://laravel02/uploads/images/avatars/202006/02/1_1591089565_6dCeHj5f2X.jpg';
        $user->assignRole('超级管理员');
        $user->save();


        $two_user = User::find(2);
        $two_user->name = 'Yellow02';
        $two_user->email = '1647600722@qq.com';
        $two_user->avatar = 'http://laravel02/uploads/images/avatars/202006/02/1_1591089565_6dCeHj5f2X.jpg';
        $two_user->assignRole('管理员');
        $two_user->save();
    }
}

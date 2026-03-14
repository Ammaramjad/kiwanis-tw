<?php
namespace Database\Seeders;

use App\Models\Club;
use App\Models\District;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create districts
        $districts = [
            ['name' => '台北地區', 'name_en' => 'Taipei District', 'district_code' => 'D01'],
            ['name' => '台中地區', 'name_en' => 'Taichung District', 'district_code' => 'D02'],
            ['name' => '高雄地區', 'name_en' => 'Kaohsiung District', 'district_code' => 'D03'],
        ];
        foreach ($districts as $d) {
            District::firstOrCreate(['district_code' => $d['district_code']], $d);
        }

        $tpDistrict = District::where('district_code', 'D01')->first();
        $tcDistrict = District::where('district_code', 'D02')->first();

        // Create clubs
        $clubs = [
            ['name' => '台北中正扶輪社', 'name_en' => 'Taipei Zhongzheng Club', 'club_code' => 'C001', 'district_id' => $tpDistrict->id, 'city' => '台北市'],
            ['name' => '台北信義扶輪社', 'name_en' => 'Taipei Xinyi Club', 'club_code' => 'C002', 'district_id' => $tpDistrict->id, 'city' => '台北市'],
            ['name' => '台中文心扶輪社', 'name_en' => 'Taichung Wenxin Club', 'club_code' => 'C003', 'district_id' => $tcDistrict->id, 'city' => '台中市'],
        ];
        foreach ($clubs as $c) {
            Club::firstOrCreate(['club_code' => $c['club_code']], $c);
        }

        $club1 = Club::where('club_code', 'C001')->first();

        // Create super admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@kiwanis.tw'],
            [
                'name' => '系統管理員',
                'email' => 'admin@kiwanis.tw',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $adminUser->assignRole('super_admin');

        // Create member user
        $memberUser = User::firstOrCreate(
            ['email' => 'member@kiwanis.tw'],
            [
                'name' => '測試會員',
                'email' => 'member@kiwanis.tw',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Create members
        $memberId = 'KW' . str_pad(1, 6, '0', STR_PAD_LEFT);
        $member1 = Member::firstOrCreate(
            ['member_id' => $memberId],
            [
                'member_id' => $memberId,
                'user_id' => $memberUser->id,
                'club_id' => $club1->id,
                'district_id' => $tpDistrict->id,
                'name_zh' => '王大明',
                'name_en' => 'David Wang',
                'phone' => '0912345678',
                'email' => 'member@kiwanis.tw',
                'city' => '台北市',
                'profession' => '企業主',
                'company' => '大明企業有限公司',
                'membership_type' => 'regular',
                'join_date' => '2020-01-01',
                'is_active' => true,
            ]
        );
        $memberUser->assignRole('member');

        // More sample members
        $sampleMembers = [
            ['name_zh' => '李美麗', 'name_en' => 'Mary Lee', 'phone' => '0923456789', 'city' => '台北市', 'profession' => '醫師'],
            ['name_zh' => '陳志明', 'name_en' => 'John Chen', 'phone' => '0934567890', 'city' => '台北市', 'profession' => '律師'],
            ['name_zh' => '張雅文', 'name_en' => 'Alice Chang', 'phone' => '0945678901', 'city' => '台北市', 'profession' => '教師'],
            ['name_zh' => '林建國', 'name_en' => 'Kevin Lin', 'phone' => '0956789012', 'city' => '台北市', 'profession' => '工程師'],
        ];

        foreach ($sampleMembers as $i => $m) {
            $id = 'KW' . str_pad($i + 2, 6, '0', STR_PAD_LEFT);
            Member::firstOrCreate(['member_id' => $id], array_merge($m, [
                'member_id' => $id,
                'club_id' => $club1->id,
                'district_id' => $tpDistrict->id,
                'membership_type' => 'regular',
                'join_date' => '2021-01-01',
                'is_active' => true,
            ]));
        }
    }
}

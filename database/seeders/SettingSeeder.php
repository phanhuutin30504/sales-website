<?php

namespace Database\Seeders;

use App\Enums\Setting\SettingGroup;
use App\Enums\Setting\SettingTypeInput;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('settings')->truncate();
        DB::table('settings')->insert([
            [
                'setting_key' => 'Nước',
                'setting_name' => 'Tên site',
                'plain_value' => 'Site name',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::General,
                'desc' => 'Tên của website, shop, app'
            ],
            [
                'setting_key' => 'site_logo',
                'setting_name' => 'Logo',
                'plain_value' => '/public/assets/images/logo-nuoc-uong.jpg',
                'type_input' => SettingTypeInput::Image,
                'group' => SettingGroup::General,
                'desc' => 'Logo thương hiệu'
            ],
            [
                'setting_key' => 'site_favicon',
                'setting_name' => 'Favicon',
                'plain_value' => '/public/assets/images/logo-nuoc-uong.jpg',
                'type_input' => SettingTypeInput::Image,
                'group' => SettingGroup::General,
                'desc' => 'Favicon'
            ],
            [
                'setting_key' => 'email',
                'setting_name' => 'Email',
                'plain_value' => 'phanhuutin3052004@gmail.com',
                'type_input' => SettingTypeInput::Email,
                'group' => SettingGroup::General,
                'desc' => 'Email liên hệ'
            ],
            [
                'setting_key' => 'hotline',
                'setting_name' => 'Số điện thoại',
                'plain_value' => '0326254914',
                'type_input' => SettingTypeInput::Phone,
                'group' => SettingGroup::General,
                'desc' => 'Số điện thoại liên lạc.'
            ],
            [
                'setting_key' => 'address',
                'setting_name' => 'Địa chỉ',
                'plain_value' => '21/2 Ấp 2 Xuân Thới Sơn, Hóc Môn',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::General,
                'desc' => 'Địa chỉ liên lạc.'
            ],
            [
                'setting_key' => 'iframe_map',
                'setting_name' => 'Bản đồ',
                'plain_value' => '',
                'type_input' => SettingTypeInput::Textarea,
                'group' => SettingGroup::General,
                'desc' => 'Bản đồ'
            ],
            [
                'setting_key' => 'introduce_short',
                'setting_name' => 'Giới thiệu ngắn',
                'plain_value' => 'Thương hiệu giày được sản xuất theo tiêu chuẩn khu vực Bắc Mỹ dưới sự cố vấn bởi các Chuyên Gia Sneaker Canada.',
                'type_input' => SettingTypeInput::Textarea,
                'group' => SettingGroup::General,
                'desc' => 'Giới thiệu ngắn'
            ],
        ]);
    }
}

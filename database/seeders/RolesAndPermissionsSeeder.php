<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Infrastructure\Persistence\Eloquent\Role;
use App\Infrastructure\Persistence\Eloquent\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Roles
        $adminRole = Role::firstOrCreate(['name' => 'システム管理者'], ['description' => 'システム全体を管理する権限']);
        $generalUserRole = Role::firstOrCreate(['name' => '一般ユーザー'], ['description' => '通常のユーザー権限']);

        // Create Permissions
        $permissions = [
            'forms.create' => 'フォームの作成',
            'users.delete' => 'ユーザーの削除',
            'users.manage' => 'ユーザーの管理',
            'users.view' => 'ユーザーの閲覧',
            'roles.manage' => 'ロールの管理',
            'forms.manage' => 'フォームの管理',
            'inquiries.view' => '問い合わせ履歴の閲覧',
            'templates.manage' => 'メールテンプレートの管理',
            'plans.manage' => 'プランの管理',
        ];

        foreach ($permissions as $name => $description) {
            Permission::firstOrCreate(compact('name'), compact('description'));
        }

        // Assign all permissions to admin role
        $adminRole->permissions()->sync(Permission::all()->pluck('id'));

        // Assign basic permissions to general user role
        $generalUserRole->permissions()->sync([
            Permission::where('name', 'forms.create')->first()->id,
            Permission::where('name', 'forms.manage')->first()->id,
            Permission::where('name', 'inquiries.view')->first()->id,
        ]);
    }
}
